<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Event;

class GetTableImageFields
{
    public function __construct(private string $table, private array $fields)
    {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(string $field): void
    {
        $this->fields[] = $field;
        $this->fields = array_unique($this->fields);
    }

    public function removeField(string $field): void
    {
        $key = array_search($field, $this->fields, true);
        unset($this->fields[$key]);
    }
}