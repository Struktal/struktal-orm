<?php

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericEntity;
use struktal\ORM\GenericEntityDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

test("Correct upsert queries", function(GenericEntity $object, Query $expected) {
    $actual = $object::dao()->generateUpsertSql($object);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("upsert");
