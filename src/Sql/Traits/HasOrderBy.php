<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Sql\Operators\OrderBy;

trait HasOrderBy
{
    private ?OrderBy $orderBy = null;
    private array $columns = [];

    public function reorder(): self
    {
        $this->columns = [];
        $this->orderBy = null;

        return $this;
    }

    public function orderBy(string $column, string $sort = 'ASC'): self
    {
        $this->columns[$column] = $sort;
        $this->orderBy = new OrderBy($this->columns);

        return $this;
    }

    public function orderByAsc(string $column): self
    {
        return $this->orderBy($column);
    }

    public function orderByDesc(string $column): self
    {
        return $this->orderBy($column, 'DESC');
    }

    public function latest(string $column): self
    {
        return $this->orderByDesc($column);
    }

    public function oldest(string $column): self
    {
        return $this->orderByAsc($column);
    }

    public function inRandomOrder(): self
    {
        $this->columns['RAND()'] = 'ASC';
        $this->orderBy = new OrderBy($this->columns);

        return $this;
    }
}
