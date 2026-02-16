<?php

namespace struktal\ORM\schema;

class SchemaEvolution extends \struktal\ORM\GenericEntity {
    public string $evolution = "";
    public ?\DateTimeImmutable $executed = null;
}
