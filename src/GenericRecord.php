<?php

namespace struktal\ORM;

use \DateTimeImmutable;

abstract class GenericRecord extends internal\GenericObject {
    public mixed $key = null;
    public mixed $value = null;
}
