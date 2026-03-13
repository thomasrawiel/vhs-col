<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Event;

class BeforeTablesAreCheckedEvent
{
    public function __construct(private array $tableFields)
    {
    }

    public function getTableFields(): array
    {
        return $this->tableFields;
    }

    public function setTableFields(array $tableFields): void
    {
        $this->tableFields = $tableFields;
    }
}