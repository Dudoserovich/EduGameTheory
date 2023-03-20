<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as PaginatorAlias;

class Paginator
{
    private const PAGE_SIZE = 20;
    private \ArrayIterator $result;
    private int $numResult;
    private int $currentPage;

    public function __construct(
        private QueryBuilder $queryBuilder,
        private int          $pageSize = self::PAGE_SIZE
    ) {}

    /**
     * @throws \Exception
     */
    final public function pagination(int $page = 1): void
    {
        $this->currentPage = max(1, $page);
        $firstResult = ($this->currentPage - 1) * $this->pageSize;

        $query = $this->queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        $paginator = new PaginatorAlias($query, true);

        $this->result = $paginator->getIterator();
        $this->numResult = $paginator->count();
    }

    /**
     * @return \ArrayIterator
     */
    public function getResult(): \ArrayIterator
    {
        return $this->result;
    }

    /**
     * @return int
     */
    public function getNumResult(): int
    {
        return $this->numResult;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return (int)ceil($this->numResult / $this->pageSize);
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return bool
     */
    public function hasPrevPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int
     */
    public function getPrevPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function hasToPaginate(): bool
    {
        return $this->numResult > $this->pageSize;
    }
}