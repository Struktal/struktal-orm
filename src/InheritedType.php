<?php

namespace struktal\DatabaseObjects;

#[\Attribute]
class InheritedType {
    public string $realTypeClass;

    public function __construct(string $realTypeClass) {
        $this->realTypeClass = $realTypeClass;
    }
}
