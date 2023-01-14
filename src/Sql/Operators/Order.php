<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators;

use QueryBuilder\Sql\SqlWithValues;
use QueryBuilder\Interfaces\SqlWithValuesInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnsException;

class Order extends SqlWithValues implements SqlWithValuesInterface
{
    private const SQL_SORT_OPERATORS = [
        'ASC' => 'ASC',
        'DESC' => 'DESC',
    ];

    protected SqlWithValuesInterface $sql;
    private array $columns;

    public function __construct(SqlWithValuesInterface $sql, array $columns)
    {
        parent::__construct($sql->getValues());

        if (empty($columns)) {
            throw new InvalidArgumentColumnsException(
                'Column array cannot be empty.',
            );
        }

        $this->sql = $sql;
        $this->columns = $this->formatAndValidateColumns($columns);
    }

    private function formatAndValidateColumns(array $columns): array
    {
        $formattedColumns = [];

        foreach ($columns as $columnName => $sort) {
            if ($this->isInvalidString($columnName)) {
                throw new InvalidArgumentColumnsException(
                    'Column name must be a string of length greater than 1.',
                );
            }

            if ($this->isInvalidString($sort)) {
                throw new InvalidArgumentColumnsException(
                    'The sort statement must be a string and its value must be "ASC" or "DESC".',
                );
            }

            $sort = $this->formatSortOperator($sort);
            $formattedColumns[$columnName] = $sort;
        }

        return $formattedColumns;
    }

    private function isInvalidString(mixed $columnName): bool
    {
        return !is_string($columnName) || empty($columnName);
    }

    private function formatSortOperator(string $sort): string
    {
        $sort = mb_strtoupper($sort);

        if (!in_array($sort, self::SQL_SORT_OPERATORS)) {
            throw new InvalidArgumentColumnsException(
                'The sort statement must be a string and its value must be "ASC" or "DESC".',
            );
        }

        return self::SQL_SORT_OPERATORS[$sort];
    }

    public function toSql(): string
    {
        $columns = $this->getColumnsWithSortStatement($this->columns);
        $columnsToSql = implode(', ', $columns);

        return "{$this->sql->toSql()} ORDER BY ${columnsToSql}";
    }

    private function getColumnsWithSortStatement(array $columns): array
    {
        $columnsSql = [];

        foreach ($columns as $columnName => $sort) {
            $columnsSql[] = "${columnName} ${sort}";
        }

        return $columnsSql;
    }
}
