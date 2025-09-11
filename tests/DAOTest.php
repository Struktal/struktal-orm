<?php

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericEntity;
use struktal\ORM\GenericEntityDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

test("Correct DAO objects", function() {
    expect(SimpleEntity::dao())->toBeInstanceOf(SimpleEntityDAO::class)
        ->and(ExtendedEntity::dao())->toBeInstanceOf(ExtendedEntityDAO::class)
        ->and(ComplexEntity::dao())->toBeInstanceOf(ComplexEntityDAO::class);
});
