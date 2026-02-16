<?php

namespace struktal\ORM\Schema;

class SchemaEvolution extends \struktal\ORM\GenericEntity {
    public string $evolution = "";
    public ?\DateTimeImmutable $executed = null;
}
