<?php

namespace jensostertag\DatabaseObjects;

class DAOFilter {
    public DAOFilterOperator $operator;
    public string $property;
    public mixed $value;

    public function __construct(DAOFilterOperator $operator, string $property, mixed $value) {
        $this->operator = $operator;
        $this->property = $property;
        $this->value = $value;
    }

    public function generateSqlTerm(mixed $index): string {
        return $this->operator->generateSqlTerm($index, $this->property, $this->value);
    }

    public function values(mixed $index): array {
        return $this->operator->values($index, $this->property, $this->value);
    }
}
