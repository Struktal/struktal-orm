<?php

use struktal\DatabaseObjects\Database\Database;
use struktal\DatabaseObjects\Database\Query;
use struktal\DatabaseObjects\GenericObject;
use struktal\DatabaseObjects\GenericObjectDAO;
use struktal\DatabaseObjects\DAOFilter;
use struktal\DatabaseObjects\DAOFilterOperator;

test("Correct upsert queries", function(GenericObject $object, Query $expected) {
    $actual = $object::dao()->generateUpsertSql($object);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("upsert");
