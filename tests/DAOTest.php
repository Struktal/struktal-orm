<?php

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericObject;
use struktal\ORM\GenericObjectDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

test("Correct DAO objects", function() {
    expect(SimpleObject::dao())->toBeInstanceOf(SimpleObjectDAO::class)
        ->and(ExtendedObject::dao())->toBeInstanceOf(ExtendedObjectDAO::class)
        ->and(ComplexObject::dao())->toBeInstanceOf(ComplexObjectDAO::class);
});
