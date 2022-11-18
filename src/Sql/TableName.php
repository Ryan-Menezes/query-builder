<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Exceptions\InvalidArgumentTableNameException;
use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Utils\Str;

class TableName implements SqlInterface
{
    private const SQL_ALIASES_STATEMENT = ' AS ';

    private string $tableName;
    private string $aliases = '';

    public function __construct(string $tableName)
    {
        $tableName = $this->formatTableName($tableName);

        $this->tableName = $this->extractTableName($tableName);
        $this->aliases = $this->extractAliases($tableName);
    }

    private function formatTableName(string $tableName): Str
    {
        $tableName = $this->removeBacktick($tableName);
        $tableName = trim($tableName);

        if($this->isNotValidTableName($tableName)) {
            throw new InvalidArgumentTableNameException('Table name is invalid');
        }

        return new Str($tableName);
    }

    private function isNotValidTableName(string $tableName): bool
    {
        return empty($tableName);
    }

    private function extractTableName(Str $tableName): string
    {
        $tableNameWithoutAlias = $tableName->before(self::SQL_ALIASES_STATEMENT);

        return empty($tableNameWithoutAlias) ? (string)$tableName : $tableNameWithoutAlias;
    }

    private function extractAliases(Str $tableName): string
    {
        $aliases = $tableName->after(self::SQL_ALIASES_STATEMENT);

        return $aliases;
    }

    private function removeBacktick(string $value): string
    {
        return str_ireplace('`', '', $value);
    }

    public function __toString(): string
    {
        $tableNameToString = "`{$this->getTableName()}`";

        if($this->hasAliases()) {
            $tableNameToString = "${tableNameToString} AS `{$this->getAliases()}`";
        }

        return $tableNameToString;
    }

    private function hasAliases(): bool
    {
        return !empty($this->getAliases());
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getAliases(): string
    {
        return $this->aliases;
    }
}
