<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Interfaces\SqlInterface;
use Stringable;

class Column implements SqlInterface
{
    private string $name;
    private string $aliases = '';
    private const SQL_ALIASES_STATEMENT = ' AS ';

    public function __construct(string|Stringable $columnName)
    {
        $this->formatNameAndAliases($columnName);
    }

    private function formatNameAndAliases(string|Stringable $columnName): void
    {
        $columnName = $this->removeBacktickFromBeginningAndEndOfString($columnName);

        $this->name = $this->extractName($columnName);
        $this->aliases = $this->extractAliases($columnName);

        if (empty($this->name)) {
            throw new InvalidArgumentColumnException('Past column name cannot be empty');
        }
    }

    private function removeBacktickFromBeginningAndEndOfString(string $value): string
    {
        return trim($value, '`');
    }

    private function extractName(string $columnName): string
    {
        $name = $this->getStringBeforeSqlAliasesStatement($columnName);
        $name = empty($name) ? $columnName : $name;

        return $this->removeBacktickFromBeginningAndEndOfString($name, '`');
    }

    private function getStringBeforeSqlAliasesStatement(string $columnName): string
    {
        $positionSqlAliasesStatement = $this->getPositionSqlAliasesStatement($columnName);
        if($positionSqlAliasesStatement === false) {
            return '';
        }

        $stringBeforeSqlAliasesStatement = mb_substr($columnName, 0, $positionSqlAliasesStatement);
        $stringBeforeSqlAliasesStatement = str_ireplace(self::SQL_ALIASES_STATEMENT, '', $stringBeforeSqlAliasesStatement);

        return $stringBeforeSqlAliasesStatement;
    }

    private function getPositionSqlAliasesStatement(string $columnName): int|bool
    {
        $positionSqlAliasesStatement = mb_stripos($columnName, self::SQL_ALIASES_STATEMENT);

        return $positionSqlAliasesStatement;
    }

    private function extractAliases(string $columnName): string
    {
        $aliases = $this->getStringAfterSqlAliasesStatement($columnName);

        return $this->removeBacktickFromBeginningAndEndOfString($aliases, '`');
    }

    private function getStringAfterSqlAliasesStatement(string $columnName): string
    {
        $positionSqlAliasesStatement = $this->getPositionSqlAliasesStatement($columnName);
        if($positionSqlAliasesStatement === false) {
            return '';
        }

        $stringAfterSqlAliasesStatement = mb_substr($columnName, $positionSqlAliasesStatement);
        $stringAfterSqlAliasesStatement = str_ireplace(self::SQL_ALIASES_STATEMENT, '', $stringAfterSqlAliasesStatement);

        return $stringAfterSqlAliasesStatement;
    }

    public function __toString(): string
    {
        if($this->hasAliases()) {
            return "`{$this->getName()}` AS `{$this->getAliases()}`";
        }

        return "`{$this->getName()}`";
    }

    private function hasAliases(): bool
    {
        return !empty($this->getAliases());
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
