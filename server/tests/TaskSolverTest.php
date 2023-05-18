<?php

namespace App\Tests;

use App\Service\Task\TaskSolver;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\IncorrectTypeException;
use MathPHP\Exception\MathException;
use MathPHP\Exception\MatrixException;
use PHPUnit\Framework\TestCase;

class TaskSolverTest extends TestCase
{
    private function normalizeArr(array|int &$arr): void
    {
        if (gettype($arr) == 'array') {
            foreach ($arr as &$element) {
                $element = round($element, 4);
            }
        }
    }

    /**
     * @throws IncorrectTypeException
     * @throws MatrixException
     * @throws BadDataException
     * @throws MathException
     */
    public function testSolvePayoffMatrixWithoutSaddle()
    {
        // === Первый тест с матрицей 2 на 2. ===
        // Седловой точки нет.
        $matrix = [
            [4, 7],
            [5, 3]
        ];

        $solve = TaskSolver::solvePayoffMatrix($matrix);

        // нормализуем полученный ответ
        $this->normalizeArr($solve['first_player']);
        $this->normalizeArr($solve['second_player']);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // проверяем, что в сумме вероятности равны 1
        self::assertEquals(1, array_sum($solve['first_player']));
        self::assertEquals(1, array_sum($solve['second_player']));
        // проверка на правильность всех данных
        self::assertEquals(
            ["strategy" => "смешанные стратегии",
                "first_player" => [0.8, 0.2],
                "second_player" => [0.4, 0.6],
                "game_price" => 4.6
            ],
            $solve
        );

        // ======

        // === Второй тест с матрицей 3 на 3. ===
        // Седловой точки нет.
        $matrix = [
            [4,	7, 2],
            [7,	3, 2],
            [2,	1, 8]
        ];

        $solve = TaskSolver::solvePayoffMatrix($matrix);

        // нормализуем полученный ответ
        $this->normalizeArr($solve['first_player']);
        $this->normalizeArr($solve['second_player']);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // проверяем, что в сумме вероятности равны 1
        self::assertEquals(1, array_sum($solve['first_player']));
        self::assertEquals(1, array_sum($solve['second_player']));
        // проверка на правильность всех данных
        self::assertEquals(
            ["strategy" => "смешанные стратегии",
                "first_player" => [0.3529, 0.2647, 0.3824],
                "second_player" => [0.4265, 0.2353, 0.3382],
                "game_price" => 4.03
            ],
            $solve
        );

        // ======

        // === Третий тест с матрицей 2 на 2. ===
        // Седловой точки нет.
        $matrix = [
            [10, 7],
            [8,	11]
        ];

        $solve = TaskSolver::solvePayoffMatrix($matrix);

        // нормализуем полученный ответ
        $this->normalizeArr($solve['first_player']);
        $this->normalizeArr($solve['second_player']);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // проверяем, что в сумме вероятности равны 1
        self::assertEquals(1, array_sum($solve['first_player']));
        self::assertEquals(1, array_sum($solve['second_player']));
        // проверка на правильность всех данных
        self::assertEquals(
            ["strategy" => "смешанные стратегии",
                "first_player" => [round(2/3, 4), round(1/3, 4)],
                "second_player" => [0.5, 0.5],
                "game_price" => 9
            ],
            $solve
        );

        // ======
    }

    // Нумерация строк идёт с 0
    /**
     * @throws IncorrectTypeException
     * @throws MatrixException
     * @throws BadDataException
     * @throws MathException
     */
    public function testSolvePayoffMatrixWithSaddle()
    {
        // === Первый тест с матрицей 3 на 3. ===
        // Седловая точка есть.
        $matrix = [
            [0.1, 0.4, 0.2],
            [0.5, 0.4, 0.3],
            [0.3, 0.2, 0.1]
        ];

        $solve = TaskSolver::solvePayoffMatrix($matrix);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // Нижняя цена игры не превосходит верхнюю
        self::assertTrue($solve['first_player'] <= $solve['second_player']);
        // проверка на правильность всех данных
        self::assertEquals(
            ["strategy" => "чистые стратегии",
                "first_player" => 1,
                "second_player" => 2,
                "game_price" => 0.3
            ],
            $solve
        );

        // ======

        // === Второй тест с матрицей 2 на 3. ===
        // Седловая точка есть.
        $matrix = [
            [8, 7, 0, 6],
            [6, 8, 5, 10]
        ];

        $solve = TaskSolver::solvePayoffMatrix($matrix);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // Нижняя цена игры не превосходит верхнюю
        self::assertTrue($solve['first_player'] <= $solve['second_player']);
        // проверка на правильность всех данных
        self::assertEquals(
            ["strategy" => "чистые стратегии",
                "first_player" => 1,
                "second_player" => 2,
                "game_price" => 5
            ],
            $solve
        );

        // ======
    }

    /**
     * @throws IncorrectTypeException
     * @throws MatrixException
     * @throws BadDataException
     * @throws MathException
     */
    public function testPayoffMatrixStupid()
    {
        // TODO: Наверное, нужны какие-то
        //  проверки на нормальность платёжной матрицы.
        //  Проверка, что все элементы одинаковые (или нули) - не гарантирует,
        //      что результат будет нормальный

        // === Тест на нулевые решения и нулевую матрицу ===
        $matrix = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0]
        ];

        $solve = TaskSolver::solvePayoffMatrix($matrix);

        self::assertEquals(
            ["strategy" => "чистые стратегии",
                "first_player" => 0,
                "second_player" => 0,
                "game_price" => 0
            ],
            $solve
        );

        // ======

        // === Тест на игру в камень-ножницы-бумагу
        //  (не ограниченное количество решений) ===
        $matrix = [
            [0, 1, -1],
            [-1, 0, 1],
            [1, -1, 0]
        ];

        $errorMessageActual = "";
        $errorMessageExpect = "The solution is unbounded";

        try {
            TaskSolver::solvePayoffMatrix($matrix);
        } catch (\Exception $e) {
            $errorMessageActual = $e->getMessage();
        }

        // проверка на то, что количество решений не ограничено
        self::assertEquals($errorMessageActual, $errorMessageExpect);

        // ======

        // === Проверка пустой матрицы на входе ===
        $errorMessageActual = "";
        $errorMessageExpect = "Matrix cannot be empty";

        try {
            TaskSolver::solvePayoffMatrix([]);
        } catch (\Exception $e) {
            $errorMessageActual = $e->getMessage();
        }

        // проверка пустой матрицы на входе
        self::assertEquals($errorMessageActual, $errorMessageExpect);

        // ======
    }

    // Нумерация с нуля
    /**
     * @throws IncorrectTypeException
     * @throws MatrixException
     * @throws BadDataException
     * @throws MathException
     */
    public function testRiskMatrix()
    {
        // TODO: Наверное, нужны какие-то
        //  проверки на нормальность матрицы последствий

        // === Простой тест на матрицу последствий ===
        $matrix = [
            [5, 2, 8, 4],
            [2, 3, 4, 12],
            [8, 5, 3, 10],
            [1, 4, 2, 8]
        ];

        $solve = TaskSolver::solveRiskMatrix($matrix);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // проверка на правильность всех данных
        self::assertEquals(
            [
                "min_value" => 5,
                "min_index" => 2
            ],
            $solve
        );

        // ======

        // === Ещё один простой тест на матрицу последствий ===

        $matrix = [
            [0, -1500000],
            [-100000, -100000]
        ];

        $solve = TaskSolver::solveRiskMatrix($matrix);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // проверка на правильность всех данных
        self::assertEquals(
            [
                "min_value" => 100000,
                "min_index" => 1
            ],
            $solve
        );

        // ======

        // === Простой тест на матрицу последствий ===
        $matrix = [
            [2, 5, 8, 4],
            [2, 3, 4, 12],
            [8, 5, 3, 10],
            [1, 4, 2, 8]
        ];

        $solve = TaskSolver::solveRiskMatrix($matrix);

        // проверка на пустоту
        self::assertNotEmpty($solve);
        // проверка на правильность всех данных
        self::assertEquals(
            [
                "min_value" => 5,
                "min_index" => 2
            ],
            $solve
        );

        // ======

        // === Проверка пустой матрицы на входе ===
        $errorMessageActual = "";
        $errorMessageExpect = "Matrix cannot be empty";

        try {
            TaskSolver::solveRiskMatrix([]);
        } catch (\Exception $e) {
            $errorMessageActual = $e->getMessage();
        }

        // проверка пустой матрицы на входе
        self::assertEquals($errorMessageActual, $errorMessageExpect);

        // ======
    }

}