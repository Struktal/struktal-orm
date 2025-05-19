<?php

use jensostertag\DatabaseObjects\Database\Database;
use jensostertag\DatabaseObjects\Database\Query;
use jensostertag\DatabaseObjects\GenericObject;
use jensostertag\DatabaseObjects\GenericObjectDAO;
use jensostertag\DatabaseObjects\DAOFilter;
use jensostertag\DatabaseObjects\DAOFilterOperator;

test("Correct DAO objects", function() {
    expect(SimpleObject::dao())->toBeInstanceOf(SimpleObjectDAO::class)
        ->and(ExtendedObject::dao())->toBeInstanceOf(ExtendedObjectDAO::class)
        ->and(ComplexObject::dao())->toBeInstanceOf(ComplexObjectDAO::class);
});
