<?php

namespace App\Service\Task;

use Traversable;

class TaskPlay
{
    /**
     * Создание массива указанной длины с указанным значением элементов
     *
     * @param int $count длина массива
     * @param float|null $num значение элементов
     * @return array
     */
    static public function fillArray(int $count, float|null $num): array
    {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $num;
        }

        return $result;
    }

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


}