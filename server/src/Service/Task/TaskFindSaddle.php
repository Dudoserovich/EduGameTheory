<?php

namespace App\Service\Task;

use JetBrains\PhpStorm\ArrayShape;

class TaskFindSaddle
{
    private array $matrix;
    public function __construct(array $matrix)
    {
        $this->matrix = $matrix;
    }

    public function getMinInRowsMaxInCols(): array
    {
        $cols = count($this->matrix[0]);

        // Ищем минимальный элемент в каждой строке
        $minInRows = array_map('min', $this->matrix);

        // Ищем максимальный элемент в каждом столбце
        $maxInCols = array();
        for ($j = 0; $j < $cols; $j++) {
            $col = array_column($this->matrix, $j);
            $maxInCols[] = max($col);
        }

        return array($minInRows, $maxInCols);
    }

    #[ArrayShape([
        "rowResult" => "float",
        "colResult" => "float"
    ])]
    public function getMinMax(): array
    {
        list($minInRows, $maxInCols) = $this->getMinInRowsMaxInCols();

        return array("rowResult" => max($minInRows), "colResult" => min($maxInCols));
    }

    /**
     * Поиск седловой точки.
     *
     * @return ?array координаты седловой точки или если её нет, то null
     */
    public function findSaddlePoint(): ?array
    {
        $rows = count($this->matrix);
        $cols = count($this->matrix[0]);

        list($minInRows, $maxInCols) = $this->getMinInRowsMaxInCols($this->matrix);

        // Ищем седловую точку
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                if ($this->matrix[$i][$j] == $minInRows[$i]
                    && $this->matrix[$i][$j] == $maxInCols[$j]) {
                    return array($i, $j);
                }
            }
        }

        // Если седловая точка не найдена, возвращаем null
        return null;
    }

}