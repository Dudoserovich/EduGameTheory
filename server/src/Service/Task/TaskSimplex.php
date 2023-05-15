<?php

namespace App\Service\Task;

use MathPHP\Functions\Map;

class TaskSimplex
{
    private array $c;
    private array $A;
    private array $b;
    public function __construct($A, $c, $b)
    {
        $this->A = $A;
        $this->c = $c;
        $this->b = $b;
    }

    private function to_tableau(): array
    {
        $xb = array_map(function($eq, $x) {
            return array_merge($eq, [$x]);
        }, $this->A, $this->b);
        $z = array_merge($this->c, [0]);
        return array_merge($xb, [$z]);
    }

    private function can_be_improved($tableau): bool
    {
        $z = end($tableau);
        foreach (array_slice($z, 0, -1) as $x) {
            if ($x > 0) {
                return true;
            }
        }
        return false;
    }

    private function get_pivot_position($tableau): array
    {
        $z = end($tableau);
        $column = array_search(max(array_slice($z, 0, -1)), array_slice($z, 0, -1));

        $restrictions = [];
        foreach (array_slice($tableau, 0, -1) as $eq) {
            $el = $eq[$column];
            $restrictions[] = ($el <= 0) ? INF : $eq[count($eq) - 1] / $el;
        }

        $row = array_keys($restrictions, min($restrictions))[0];
        return [$row, $column];
    }

    private function pivot_step($tableau, $pivot_position): array
    {
        $new_tableau = array_fill(0, count($tableau), array());

        list($i, $j) = $pivot_position;
        $pivot_value = $tableau[$i][$j];
        $new_tableau[$i] = array_map(function($x) use ($pivot_value) {
            return $x / $pivot_value;
        }, $tableau[$i]);

        foreach ($tableau as $eq_i => $eq) {
            if ($eq_i != $i) {
                $multiplier = Map\Single::multiply($new_tableau[$i], $eq[$j]);
                $new_tableau[$eq_i] = Map\Multi::subtract($eq, $multiplier);
            }
        }

        return $new_tableau;
    }

    private function is_basic($column): bool
    {
        return array_sum($column) == 1
            && count(
                array_filter($column, function($c) { return $c == 0; })
            ) == count($column) - 1;
    }

    private function get_solution($tableau): array
    {
        $columns = array_map(null, ...$tableau);
        $solutions = [];
        foreach ($columns as $column) {
            $solution = 0;
            if ($this->is_basic($column)) {
                $one_index = array_search(1, $column);
                $solution = end($columns)[$one_index];
            }
            $solutions[] = $solution;
        }
        return $solutions;
    }

    function simplex(): array
    {
        $tableau = $this->to_tableau();

        while ($this->can_be_improved($tableau)) {
            $pivot_position = $this->get_pivot_position($tableau);
            $tableau = $this->pivot_step($tableau, $pivot_position);
        }

        return $this->get_solution($tableau);
    }


}