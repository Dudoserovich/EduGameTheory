<?php

namespace App\Service;

use Traversable;

class TaskSolver
{
    /**
     * Выборка случайного элемента с учетом веса
     *
     * @param array $values индексный массив элементов
     * @param array $weights индексный массив соответствующих весов
     * @return float|null
     */
    static private function weightedRandomSimple(array $values, array $weights): float|null
    {
        $total = array_sum($weights);
        $n = 0;

        $num = mt_rand(1, $total);

        foreach ($values as $i => $value)
        {
            $n += $weights[$i];

            if ($n >= $num)
            {
                return $value;
            }
        }

        return null;
    }

    /**
     * Нормализация массива вероятностей (весов)
     *
     * @param array $matrixRow выбранная пользователем строка
     * @param array $weights массив весов (вероятностей)
     * @return void
     */
    static private function weightNormalization(array $matrixRow, array &$weights = []): void
    {
        // Заполнение массива весов единицами, если вес не задан
        if (!$weights) {
            $countColumns = count($matrixRow);

            for ($i = 0; $i < $countColumns; $i++) {
                $weights[] = 1;
            }
        }
    }

    /**
     * Ход на основе матрицы и массива вероятностей
     *
     * @param array $matrix матрица
     * @param int $numRow номер строки
     * @param array|null $weights массив вероятностей (весов)
     * @return float|null результат хода
     */
    static public function move(array $matrix,
                                int $numRow,
                                array|null $weights = []
    ): float|null
    {
        // Заполнение массива весов единицами, если вес не задан
        if (!$weights)
            $weights = self::fillArray(count($matrix[$numRow]), null);

        // Выбор элемента на основе выбранной пользователем строки и
        //      случайной колонки компьютером
        return self::weightedRandomSimple($matrix[$numRow], $weights);
    }


    /**
     * Поиск седловой точки.
     *
     * @param array $matrix матрица
     * @return ?array координаты седловой точки или если её нет, то null
     */
    static public function findSaddlePoint(array $matrix): ?array
    {
        // TODO: Можно так же сделать итератором,
        //  который, вернёт минимальные, максимальные элементы
        //  и соответствующие минимаксы и если есть седловая точка,
        //      то ещё и её.
        $rows = count($matrix);
        $cols = count($matrix[0]);

        // Ищем минимальный элемент в каждой строке
        $minInRows = array_map('min', $matrix);

        // Ищем максимальный элемент в каждом столбце
        $maxInCols = array();
        for ($j = 0; $j < $cols; $j++) {
            $col = array_column($matrix, $j);
            $maxInCols[] = max($col);
        }

        // Ищем седловую точку
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                if ($matrix[$i][$j] == $minInRows[$i] && $matrix[$i][$j] == $maxInCols[$j]) {
                    return array($i, $j);
                }
            }
        }

        // Если седловая точка не найдена, возвращаем null
        return null;
    }

    /**
     * Создание массива указанной длины с указанным значением элементов
     *
     * @param int $count длина массива
     * @param float|null $num значение элементов
     * @return array
     */
    static private function fillArray(int $count, float|null $num): array
    {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $num;
        }

        return $result;
    }

    /**
     * Получение результата упрощения матрицы.
     *
     * @param array $matrix матрица
     * @return array
     */
    static public function getEndSimpleMatrix(array $matrix): array
    {
        $newMatrix = iterator_to_array(self::simpleMatrixIterable($matrix));
        return end($newMatrix);
    }

    /**
     * Получение шагов(['turn_1' => [...], ...]) упрощения матрицы.
     *
     * @param array $matrix матрица
     * @return array
     */
    static public function getTurnsSimpleMatrix(array $matrix): array
    {
        $count = 1;
        $result = [];
        foreach (self::simpleMatrixIterable($matrix) as $mat) {
            $result += ["turn_$count" => $mat];
            $count += 1;
        }

        return $result;
    }

    // По поводу упрощения можно сказать,
    //  что отличные от этих стратегий стратегии брать не смысла,
    //  т.к. они не выгодны обои игрокам и считать их вероятности тоже нет смысла.
    /**
     * Упрощение матрицы путём удаления доминирующих строк и столбцов.
     * Изменение вероятностей не учитывается.
     *
     * @param array $matrix матрица
     * @return Traversable
     */
    static private function simpleMatrixIterable(array $matrix): Traversable
    {
        // Поиск доминирующих строк
        do {
            $rows = array();
            $cols = array();
            $m = count($matrix);
            $n = count($matrix[0]);

            for ($i = 0; $i < $m; $i++) {

                // Заглушка для ситуаций когда осталась одна строка
                if ($m == 1)
                    continue;

                $isGreater = false;
                for ($j = 0; $j < $m; $j++) {
                    if ($i != $j) {
                        $greater = true;
                        for ($k = 0; $k < $n; $k++) {
                            if ($matrix[$i][$k] < $matrix[$j][$k]) {
                                $greater = false;
                                break;
                            }
                        }
                        if ($greater) {
                            $isGreater = true;
                            break;
                        }
                    }
                }

                if ($isGreater)
                    $rows[] = $j;
            }

            if ($rows) {
                // удаление всех записанных доминирующих строк
                foreach ($rows as $row) {
                    self::deleteRow($matrix, $row);
                }

                $m = count($matrix);

                yield $matrix;
            }

            // Поиск доминирующих столбцов
            for ($i = 0; $i < $n; $i++) {
                if ($n == 1)
                    continue;

                $isGreater = false;
                for ($j = 0; $j < $n; $j++) {
                    if ($i != $j) {
                        $greater = true;
                        for ($k = 0; $k < $m; $k++) {
                            if ($matrix[$k][$i] < $matrix[$k][$j]) {
                                $greater = false;
                                break;
                            }
                        }
                        if ($greater) {
                            $isGreater = true;
                            break;
                        }
                    }
                }

                if ($isGreater)
                    $cols[] = $i;

            }

            if ($cols) {
                // удаление всех записанных доминирующих строк
                foreach ($cols as $col) {
                    self::deleteColumn($matrix, $col);
                }

                yield $matrix;
            }

        } while ($rows or $cols);

    }

    /**
     * Удаление столбца в двумерном массиве (матрице)
     *
     * @param array $matrix матрица
     * @param int $colIndex номер удаляемого столбца
     * @return void
     */
    static private function deleteColumn(array &$matrix, int $colIndex): void
    {
        foreach ($matrix as &$row) {
            array_splice($row, $colIndex, 1);
        }
    }

    /**
     * Удаление строки в двумерном массиве (матрице)
     *
     * @param array $matrix матрица
     * @param int $rowIndex номер удаляемой строки
     * @return void
     */
    static private function deleteRow(array &$matrix, int $rowIndex): void
    {
        array_splice($matrix, $rowIndex, 1);
    }

    // TODO: нужен метод, вычисляющий лучшее решение
    //  для смешанных стратегий

    // TODO: нужен метод, вычисляющий лучшее решение
    //  для чистых стратегий

    // TODO: нужен метод, вычисляющий лучшее решение
    //  для матрицы последствий
}