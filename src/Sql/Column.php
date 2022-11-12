<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Utils\Str;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Interfaces\SqlInterface;
use Stringable;

class Column implements SqlInterface
{
    private const SQL_ALIASES_STATEMENT = ' AS ';
    private const SQL_DOT = '.';

    private string|Stringable $tableName = '';
    private string|Stringable $name;
    private string|Stringable $aliases = '';

    public function __construct(string|Stringable $columnName)
    {
        $this->formatNameAndAliases($columnName);
    }

    private function formatNameAndAliases(string|Stringable $columnName): void
    {
        $columnName = $this->removeBacktickFromBeginningAndEndOfString($columnName);
        $columnName = new Str($columnName);

        $this->tableName = $this->extractTableName($columnName);
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

    private function extractTableName(Str $columnName): string
    {
        $tableName = $columnName->before(self::SQL_DOT);

        return $this->removeBacktickFromBeginningAndEndOfString($tableName, '`');
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

        return $this->removeBacktickFromBeginningAndEndOfString($name, '`');
    }

    private function extractAliases(Str $columnName): string
    {
        $aliases = $columnName->after(self::SQL_ALIASES_STATEMENT);

        return $this->removeBacktickFromBeginningAndEndOfString($aliases, '`');
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
