<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Interfaces\SqlInterface;
use Stringable;

class Column implements SqlInterface
{
    private string $name;
    private string $nickname = '';
    private const SQL_AS_STATEMENT = ' AS ';

    public function __construct(string|Stringable $columnName)
    {
        $this->formatNameAndNickname($columnName);
    }

    private function formatNameAndNickname(string|Stringable $columnName): void
    {
        $columnName = $this->removeBacktickFromBeginningAndEndOfString($columnName);

        $this->name = $this->extractName($columnName);
        $this->nickname = $this->extractNickname($columnName);

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
        $name = $this->getStringBeforeSqlAsStatement($columnName);
        $name = empty($name) ? $columnName : $name;

        return $this->removeBacktickFromBeginningAndEndOfString($name, '`');
    }

    private function getStringBeforeSqlAsStatement(string $columnName): string
    {
        $positionSqlAsStatement = $this->getPositionSqlAsStatement($columnName);
        if($positionSqlAsStatement === false) {
            return '';
        }

        $stringBeforeSqlAsStatement = mb_substr($columnName, 0, $positionSqlAsStatement);
        $stringBeforeSqlAsStatement = str_ireplace(self::SQL_AS_STATEMENT, '', $stringBeforeSqlAsStatement);

        return $stringBeforeSqlAsStatement;
    }

    private function getPositionSqlAsStatement(string $columnName): int|bool
    {
        $positionSqlAsStatement = mb_stripos($columnName, self::SQL_AS_STATEMENT);

        return $positionSqlAsStatement;
    }

    private function extractNickname(string $columnName): string
    {
        $nickname = $this->getStringAfterSqlAsStatement($columnName);

        return $this->removeBacktickFromBeginningAndEndOfString($nickname, '`');
    }

    private function getStringAfterSqlAsStatement(string $columnName): string
    {
        $positionSqlAsStatement = $this->getPositionSqlAsStatement($columnName);
        if($positionSqlAsStatement === false) {
            return '';
        }

        $stringAfterSqlAsStatement = mb_substr($columnName, $positionSqlAsStatement);
        $stringAfterSqlAsStatement = str_ireplace(self::SQL_AS_STATEMENT, '', $stringAfterSqlAsStatement);

        return $stringAfterSqlAsStatement;
    }

    public function __toString(): string
    {
        if($this->hasNickname()) {
            return "`{$this->getName()}` AS `{$this->getNickname()}`";
        }

        return "`{$this->getName()}`";
    }

    private function hasNickname(): bool
    {
        return !empty($this->getNickname());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }
}
