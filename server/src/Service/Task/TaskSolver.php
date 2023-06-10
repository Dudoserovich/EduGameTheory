<?php

namespace App\Service\Task;

use Exception;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\IncorrectTypeException;
use MathPHP\Exception\MathException;
use MathPHP\Exception\MatrixException;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\Functions\Map;

class TaskSolver
{
    /**
     * Расчёт оптимальных стратегий на основе результатов симплекс-метода.
     *
     * @param array $solution результат симплекс-метода в виде массива
     * @return array оптимальные стратегии (вероятности выбора стратегий)
     */
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
     * Решение платёжной матрицы:
     * 1. Поиск седловой точки (чистые стратегии).
     * 2. Упрощение матрицы (пока не используется).
     * 3. Поиск решения в смешанных стратегиях.
     *
     * @param array $matrix платёжная матрица
     * @return array результат чистых/смешанных стратегий
     *
     * @throws MatrixException
     * @throws IncorrectTypeException
     * @throws BadDataException
     * @throws MathException
     * @throws Exception
     */
    static public function solvePayoffMatrix(array $matrix, bool $fullResult = false): array
    {
        if (!$matrix) {
            throw new Exception('Matrix cannot be empty');
        }

        # 1. Поиск седловой точки
        # Если седловая точка есть, то найдено решение в частных стратегиях.
        $taskFindSaddle = new TaskFindSaddle($matrix);
        $saddlePoint = $taskFindSaddle->findSaddlePoint();

        // Возвращаем оптимальную стратегию первого и второго игрока
        if ($saddlePoint) {
            list($i, $j) = $saddlePoint;

            $result = array(
                "strategy" => "чистые стратегии",
                "first_player" => $i,
                "second_player" => $j,
                "game_price" => $matrix[$i][$j]
            );

            // Если получаем полное решение - добавляем промежуточный шаг в результат
            if ($fullResult) {
                list($minInRows, $maxInCols) = $taskFindSaddle->getMinInRowsMaxInCols();
                $result[] =
                    [
                        "min_in_rows" => $minInRows,
                        "max_in_cols" => $maxInCols
                    ];
            }

            return $result;
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

        $result = array(
            "strategy" => "смешанные стратегии",
            "first_player" => $P,
            "second_player" => $Q,
            "game_price" => $V
        );

        // TODO: Полное решение игры с промежуточными шагами
        if ($fullResult) {
            $result["turns_first"] = $taskSimplexFirstPlayer->getFullResult();
            $result["turns_second"] = $taskSimplexSecondPlayer->getFullResult();
        }

        return $result;
    }

    // TODO: возможно, ещё нужен метод,
    //  который будет возвращать координаты
    //  для решения смешанных стратегий ГРАФИЧЕСКИМ МЕТОДОМ

    /**
     * Решение матрицы рисков.
     *
     * @param array $matrix матрица последствий
     * @return array массив, содержащий номер строки (лучшая стратегия) и минимальный элемент.
     *
     * @throws MatrixException
     * @throws BadDataException
     * @throws IncorrectTypeException
     * @throws MathException
     * @throws Exception
     */
    static public function solveRiskMatrix(array $matrix): array
    {
        if (!$matrix) {
            throw new Exception('Matrix cannot be empty');
        }

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
     * Функция, сравнивающая результаты решения
     *  платёжной матрицы системы $matrix и решение пользователя $solvePlayer.
     *
     * @param array $matrix платёжная матрица
     * @param array $solvePlayer решение пользователя
     * @return array массив, содержащий успешность сравнения и сообщение
     *
     * @throws IncorrectTypeException
     * @throws MatrixException
     * @throws BadDataException
     * @throws MathException
     * @throws Exception
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

    // TODO: в тестах используется округление до 4х.
    //  Скорее всего в ответе пользователя стоит передавать не десятичные числа, а дробь.
    /**
     * Округление элементов массива до 2ух знаков.
     *
     * @param array|int $arr массив с числами или число
     * @return void
     */
    static private function normalizeArr(array|int &$arr): void
    {
        if (gettype($arr) == 'array') {
            foreach ($arr as &$element) {
                $element = round($element, 2);
            }
        }
    }
}