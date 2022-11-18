<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Utils\Str;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Interfaces\SqlInterface;

class Column implements SqlInterface
{
    private const SQL_ALIASES_STATEMENT = ' AS ';
    private const SQL_DOT = '.';

    private string $tableName = '';
    private string $name;
    private string $aliases = '';

    public function __construct(string $columnName)
    {
        $this->formatNameAndAliases($columnName);
    }

    private function formatNameAndAliases(string $columnName): void
    {
        $columnName = $this->formatColumnName($columnName);

        $this->tableName = $this->extractTableName($columnName);
        $this->name = $this->extractName($columnName);
        $this->aliases = $this->extractAliases($columnName);
    }

    private function  formatColumnName(string $columnName): Str
    {
        $columnName = $this->removeBacktick($columnName);
        $columnName = trim($columnName);

        if ($this->isNotValidColumnName($columnName)) {
            throw new InvalidArgumentColumnException('Column name is invalid');
        }

        return new Str($columnName);
    }

    private function removeBacktick(string $value): string
    {
        return str_ireplace('`', '', $value);
    }

    private function isNotValidColumnName(string $columnName): bool
    {
        return empty($columnName);
    }

    private function extractTableName(Str $columnName): string
    {
        $tableName = $columnName->before(self::SQL_DOT);

        return $tableName;
    }

    private function extractName(Str $columnName): string
    {
        $name = $columnName->after(self::SQL_DOT);

        if(empty($name)) {
            $name = $columnName->before(self::SQL_ALIASES_STATEMENT);
        } else {
            $columnName = new Str($name);
            $name = $columnName->before(self::SQL_ALIASES_STATEMENT);
        }

        $name = empty($name) ? $columnName->getValue() : $name;

        return $name;
    }

    private function extractAliases(Str $columnName): string
    {
        $aliases = $columnName->after(self::SQL_ALIASES_STATEMENT);

        return $aliases;
    }

    public function __toString(): string
    {
        $fieldToString = "`{$this->getName()}`";

        if($this->hasTableName()) {
            $fieldToString = "`{$this->getTableName()}`.${fieldToString}";
        }

        if($this->hasAliases()) {
            $fieldToString = "${fieldToString} AS `{$this->getAliases()}`";
        }

        return $fieldToString;
    }

    private function hasTableName(): bool
    {
        return !empty($this->getTableName());
    }

    private function hasAliases(): bool
    {
        return !empty($this->getAliases());
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAliases(): string
    {
        return $this->aliases;
    }
}
