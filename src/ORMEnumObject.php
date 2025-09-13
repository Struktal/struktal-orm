<?php

namespace struktal\DatabaseObjects;

abstract class ORMEnumObject {
    /** @var int[] $cases */
    private static array $cases = [];

    public readonly string $name;
    public readonly int $value;

    public function __construct(string $name) {
        if(!array_key_exists($name, self::$cases)) {
            throw new \InvalidArgumentException("No enum case with name $name");
        }

        $this->name = $name;
        $this->value = self::$cases[$name];
    }

    public static function cases(): array {
        $cases = [];
        foreach(self::$cases as $name => $value) {
            $cases[] = new static($name);
        }
        return $cases;
    }

    public static function from(int $value): self {
        foreach(self::$cases as $name => $caseValue) {
            if($caseValue === $value) {
                return new static($name);
            }
        }

        throw new \InvalidArgumentException("No enum case with value $value");
    }

    public static function tryFrom(int $value): ?self {
        foreach(self::$cases as $name => $caseValue) {
            if($caseValue === $value) {
                return new static($name);
            }
        }

        return null;
    }
}
