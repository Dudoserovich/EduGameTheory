<?php

namespace App\Service\Task;

use MathPHP\Exception\BadDataException;
use MathPHP\Exception\IncorrectTypeException;
use MathPHP\Exception\MathException;
use MathPHP\Exception\MatrixException;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\Functions\Map;

class TaskSolver
{
    static private function calcOptimalStrategy(array $solution): array
    {
        $Z = array_sum($solution);

        $X = [];
        foreach ($solution as $x) {
            if ($x != 0) {
                $X[] = $x / $Z;
            }
        }

        return $X;
    }

    /**
     * @throws MatrixException
     * @throws IncorrectTypeException
     * @throws BadDataException
     * @throws MathException
     */
    static public function solvePayoffMatrix($matrix): array
    {
        # 1. Поиск седловой точки
        # Если седловая точка есть, то найдено решение в частных стратегиях.
        # TODO: на этом этапе можно возвращать
        #   с помощью yield промежуточные этапы решения
        $taskFindSaddle = new TaskFindSaddle($matrix);
        $saddlePoint = $taskFindSaddle->findSaddlePoint();

        // Возвращаем оптимальную стратегию первого и второго игрока
        if ($saddlePoint) {
            list($i, $j) = $saddlePoint;
            return array(
                "strategy" => "чистые стратегии",
                "first_player" => $i,
                "second_player" => $j,
                "game_price" => $matrix[$i][$j]
            );
        }

        # 2. Упрощение матрицы.
        # Происходит удаление доминирующих столбцов и строк.
        # TODO: так же можно получить промежуточные шаги
        #   с помощью getTurnsSimpleMatrix
        // TODO: Пока что упрощённую матрицу не используем
        // TODO: Ломается на Камень-ножницы-бумага
//        $simpleMatrix = TaskMatrixSimpler::getEndSimpleMatrix($matrix);

        # 3. Поиск решения в смешанных стратегиях с помощью симплекс-метода.
        # Ответ: Цена игры и оптимальные стратегии первого и второго игрока.
        // TODO: Ломается на Камень-ножницы-бумага

        # Решение для 1-го игрока
        $c = TaskPlay::fillArray(count($matrix[0]), 1);
        $b = TaskPlay::fillArray(count($matrix), 1);
        $taskSimplexFirstPlayer = new TaskSimplex($matrix, $c, $b);
        $solution = $taskSimplexFirstPlayer->simplex();

        # Оптимальная стратегия первого игрока
        $P = self::calcOptimalStrategy($solution);

        // Цена игры(V) высчитывается только по первому игроку
        $V = round(
            array_sum(
                Map\Multi::multiply($P, $matrix[0])
            ), 2
        );

        # Решение для 2-го игрока
        $newMatrix = MatrixFactory::create($matrix);
        $newMatrix = $newMatrix->transpose()->getMatrix();
        $taskSimplexSecondPlayer = new TaskSimplex($newMatrix, $c, $b);
        $solution = $taskSimplexSecondPlayer->simplex();

        # Оптимальная стратегия второго игрока
        $Q = self::calcOptimalStrategy($solution);

        return array(
            "strategy" => "смешанные стратегии",
            "first_player" => $P,
            "second_player" => $Q,
            "game_price" => $V
        );
    }

    // TODO: возможно, ещё нужен метод,
    //  который будет возвращать координаты
    //  для решения смешанных стратегий ГРАФИЧЕСКИМ МЕТОДОМ

    /**
     * @throws MatrixException
     * @throws BadDataException
     * @throws IncorrectTypeException
     * @throws MathException
     */
    static public function solveRiskMatrix($matrix): array
    {
        $A = MatrixFactory::create($matrix);
        $countCol = $A->getN();

        // Составляем матрицу рисков (R)
        $R = [];
        for ($i = 0; $i < $countCol; $i++) {
            $column = $A->getColumn($i);
            $max = max($column);

            $newRow = [];
            foreach ($column as $element) {
                $newRow[] = $max - $element;
            }

            $R[] = $newRow;
        }

        // Приводим матрицу в правильный вид
        $TrueR = MatrixFactory::create($R)->transpose();
        $TrueR = $TrueR->getMatrix();

        // Считаем ответ на основе матрицы рисков
        $maxArr = [];
        foreach ($TrueR as $row) {
            $maxArr[] = max($row);
        }

        $minValue = min($maxArr);
        $minIndex = array_search($minValue, $maxArr);

        return array("min_value" => $minValue, "min_index" => $minIndex);
    }

    /**
     * @throws IncorrectTypeException
     * @throws MatrixException
     * @throws BadDataException
     * @throws MathException
     */
    static public function comparisionPaymentResult(
        array $matrix,
        array $solvePlayer
    ): array
    {
        $solveSystem = TaskSolver::solvePayoffMatrix($matrix);

        $diff = array_diff(
            array_map('serialize', $solvePlayer),
            array_map('serialize', $solveSystem)
        );

        $multidimensional_diff = array_map('unserialize', $diff);

//        print_r($multidimensional_diff);
        //$diff = array_diff_assoc($array1, $array2);

        if (!array_key_exists("strategy", $multidimensional_diff)) {
            //    echo "Правильно!" . PHP_EOL;

            // нормализуем массив вероятностей, полученный учеником
            self::normalizeArr($solvePlayer['first_player']);
            self::normalizeArr($solvePlayer['second_player']);

            // нормализуем массив вероятностей, полученный системой
            self::normalizeArr($solveSystem['first_player']);
            self::normalizeArr($solveSystem['second_player']);

            $isEqFirst = $solveSystem["first_player"] == $solvePlayer["first_player"];
            $isEqSecond = $solveSystem["second_player"] == $solvePlayer["second_player"];

            $isEqPrice = round($solveSystem['game_price'], 2) == round($solvePlayer['game_price'], 2);

            if ($isEqFirst && $isEqSecond && $isEqPrice) {
                // Вернуть ещё полное решение задания системой
                $message = "Вы правильно решили задание!";
                $success = true;
            } else {
                $success = false;
                if (!$isEqFirst)
                    $message = "Ошибка в результате первого игрока";
                elseif (!$isEqSecond)
                    $message = "Ошибка в результате второго игрока";
                else
                    $message = "Ошибка в цене игры";
            }
        } else {
            $success = false;
            $message = "Вы выбрали неправильную стратегию игры";
        }

        return array("success" => $success, "message" => $message);
    }

    static private function normalizeArr(array|int &$arr): void
    {
        if (gettype($arr) == 'array') {
            foreach ($arr as &$element) {
                $element = round($element, 2);
            }
        }
    }
}