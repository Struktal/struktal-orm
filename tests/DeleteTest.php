<?php

use jensostertag\DatabaseObjects\Database\Database;
use jensostertag\DatabaseObjects\Database\Query;
use jensostertag\DatabaseObjects\GenericObject;
use jensostertag\DatabaseObjects\GenericObjectDAO;
use jensostertag\DatabaseObjects\DAOFilter;
use jensostertag\DatabaseObjects\DAOFilterOperator;

test("Correct delete queries", function(GenericObject $object, Query $expected) {
    $actual = $object::dao()->generateDeleteSql($object);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("delete");
