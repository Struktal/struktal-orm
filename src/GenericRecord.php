<?php

namespace struktal\ORM;

abstract class GenericRecord extends internal\GenericObject {
    public abstract function getKey(): mixed;
    public abstract function setKey(mixed $key): void;
    public abstract function getValue(): mixed;
    public abstract function setValue(mixed $value): void;
}
