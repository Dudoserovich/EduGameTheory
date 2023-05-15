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
                $X[] = $x/$Z;
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

        # 2. Упрощение матрицы.
        # Происходит удаление доминирующих столбцов и строк.
        # TODO: так же можно получить промежуточные шаги с помощью getTurnsSimpleMatrix
        $simpleMatrix = TaskMatrixSimpler::getEndSimpleMatrix($matrix);

        # 3. Поиск решения в смешанных стратегиях с помощью симплекс-метода.
        # Ответ: Цена игры и оптимальные стратегии первого и второго игрока.

        # Решение для 1-го игрока
        $c = TaskPlay::fillArray(count($matrix[0]), 1);
        $b = TaskPlay::fillArray(count($matrix), 1);
        $taskSimplexFirstPlayer = new TaskSimplex($matrix, $c, $b);
        $solution = $taskSimplexFirstPlayer->simplex();

        # Оптимальная стратегия первого игрока
        $P = self::calcOptimalStrategy($solution);

        // Цена игры(V) высчитывается только по первому игроку
        $V = array_sum(Map\Multi::multiply($P, $matrix[0]));

        # Решение для 2-го игрока
        $newMatrix = MatrixFactory::create($matrix);
        $newMatrix = $newMatrix->transpose()->getMatrix();
        $taskSimplexSecondPlayer = new TaskSimplex($newMatrix, $c, $b);
        $solution = $taskSimplexSecondPlayer->simplex();

        # Оптимальная стратегия второго игрока
        $Q = self::calcOptimalStrategy($solution);

        return array(
            "firstPlayer" => $P,
            "secondPlayer" => $Q,
            "gamePrice" => $V
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
    function solveRiskMatrix($matrix): array
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

        return array("minValue" => $minValue, "minIndex" => $minIndex);
    }
}