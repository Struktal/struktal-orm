<?php

namespace jensostertag\DatabaseObjects\Database;

class Query {
    private string $sql;
    private array $parameters;

    public function __construct(string $sql, array $params = []) {
        $this->sql = $sql;
        $this->parameters = $params;
    }

    public function getSql(): string {
        return $this->sql;
    }

    public function getParameters(): array {
        return $this->parameters;
    }
}
