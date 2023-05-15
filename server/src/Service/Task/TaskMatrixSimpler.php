<?php

namespace App\Service\Task;

use Traversable;

class TaskMatrixSimpler
{
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

                    yield $matrix;
                }

                $m = count($matrix);
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

                    yield $matrix;
                }

            }

        } while ($rows or $cols);

    }

}