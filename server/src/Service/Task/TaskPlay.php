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
     * @return array заполненный массив
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
     * @return float|null случайный элемент
     */
    static private function weightedRandomSimple(array $values, array $weights): ?float
    {
        $weights = self::normalizeWeights($weights);
        $count = count($values);
        $i = 0;
        $n = 0;
        $num = mt_rand(1, (int)array_sum($weights));
        while($i < $count){
            $n += $weights[$i];
            if($n >= $num){
                break;
            }
            $i++;
        }
        return $values[$i];
    }

    /**
     * Так как массив вероятностей - число меньшее 1,
     * причём иногда очень маленькое, необходимо скорректировать веса.
     *
     * @param array $weights
     * @return array
     */
    static private function normalizeWeights(array $weights): array
    {
        $salt = 100;
        $count = count($weights);
        foreach ($weights as &$weight) {
            $weight *= $count*$salt;
        }
        return $weights;
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
            $weights = self::fillArray(count($matrix[$numRow]), 1);

        // Выбор элемента на основе выбранной пользователем строки и
        //      случайной колонки компьютером
        return self::weightedRandomSimple($matrix[$numRow], $weights);
    }


}