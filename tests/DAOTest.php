<?php

use struktal\DatabaseObjects\Database\Database;
use struktal\DatabaseObjects\Database\Query;
use struktal\DatabaseObjects\GenericObject;
use struktal\DatabaseObjects\GenericObjectDAO;
use struktal\DatabaseObjects\DAOFilter;
use struktal\DatabaseObjects\DAOFilterOperator;

test("Correct DAO objects", function() {
    expect(SimpleObject::dao())->toBeInstanceOf(SimpleObjectDAO::class)
        ->and(ExtendedObject::dao())->toBeInstanceOf(ExtendedObjectDAO::class)
        ->and(ComplexObject::dao())->toBeInstanceOf(ComplexObjectDAO::class);
});
