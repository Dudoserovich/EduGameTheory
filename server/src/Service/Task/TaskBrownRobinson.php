<?php

namespace App\Service\Task;

class TaskBrownRobinson
{
    private array $p;
    private array $q;
    private float $v;

    public function __construct()
    {
    }

    public function getP(): array
    {
        return $this->p;
    }
    public function getQ(): array
    {
        return $this->q;
    }

    public function getV(): float
    {
        return $this->v;
    }

    public function BraunRobinson(array $matrix, $N = 1000): void
    {
        $row = count($matrix);
        $col = count($matrix[0]);
        $count = 1; //счетчик
        $A = 1; // выбранная стратегия
        $B = 1; // выбранная стратегия
        $v = 0; //цена игры
        $p = array();
        $q = array();

        //для промежуточных вычислений
        $max_alpha = array();
        $min_betta = array();

        //определяем произвольные стратегии
        $p[0] = 1;
        $q[0] = 1;
        for ($i = 1; $i < $col; $i++)
            $q[$i] = 0;

        for ($i = 1; $i < $row; $i++)
            $p[$i] = 0;

        while ($count <= $N) {
            for ($i = 0; $i < $row; $i++) {
                //
                $sum1 = 0;
                //
                for ($j = 0; $j < $col; $j++)
                    $sum1 = $sum1 + $matrix[$i][$j] * $q[$j];
                $max_alpha[$i] = $sum1;
            }
            $max = -10000000;
            for ($i = 0; $i < $row; $i++) {
                if ($max < $max_alpha[$i]) {
                    $max = $max_alpha[$i];
                    $A = $i;
                }
            }
            $alpha = $max;
            //////////////////////////////////////
            for ($j = 0; $j < $col; $j++) {
                $sum2 = 0;
                for ($i = 0; $i < $row; $i++)
                    $sum2 = $sum2 + $matrix[$i][$j] * $p[$i];
                $min_betta[$j] = $sum2;
            }
            //
            $min = 100000000;
            for ($j = 0; $j < $col; $j++) {
                if ($min > $min_betta[$j]) {
                    $min = $min_betta[$j];
                    $B = $j;
                }
            }
            //
            $betta = $min;
            //
            $v = ($alpha + $betta) / 2;
            for ($i = 0; $i < $row; $i++) {
                if ($i != $A) {
                    $p[$i] = (double)(($count * $p[$i]) / ($count + 1));
                } else {
                    $p[$i] = (double)(($count * $p[$i] + 1) / ($count + 1));
                }
            }
            for ($i = 0; $i < $col; $i++) {
                if ($i != $B)
                    $q[$i] = ($count * $q[$i]) / ($count + 1);
                else
                    $q[$i] = ($count * $q[$i] + 1) / ($count + 1);
            }
            $count++;
        }

        $this->q = $q;
        $this->p = $p;
        $this->v = $v;
    }
}