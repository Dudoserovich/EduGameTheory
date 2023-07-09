<?php

namespace App\Service;

class Declension
{
    static function doByThreeForms($num, $form_1, $form_2, $form_3)
    {
        $num = abs($num) % 100;
        $remainder = $num % 10;

        if ($num > 10 && $num < 20) {
            return $form_3;
        }
        if ($remainder > 1 && $remainder < 5) {
            return $form_2;
        }
        if ($remainder == 1) {
            return $form_1;
        }
        return $form_3;
    }
}