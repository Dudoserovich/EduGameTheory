<?php

namespace App\Service;

use Exception;

class TaskMarkService
{
    /**
     * Получение оценки ученика в зависимости от количества попыток.
     * Соответствие попыткам следующее:
     *  попыток: 1 - оценка: 5,
     *  попыток: 2 - оценка: 4,
     *  попыток: 3 - оценка: 4,
     *  попыток: 4 - оценка: 3
     *  попыток: 5 - оценка: 2
     *  попыток: n - оценка: 2
     *
     * @param int $n Количество попыток
     * @return int результирующая оценка системы
     *
     * @throws Exception
     */
    static public function get(int $n): int
    {
        if ($n <= 0)
            throw new Exception("Недопустимый аргумент n в логарифме с основанием 10");
        if ($n > 5)
            return 2;
        else
            return floor(
                5 - (log($n, 10) * ($n-1))
            );
    }
}