<?php

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericObject;
use struktal\ORM\GenericObjectDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

test("Correct delete queries", function(GenericObject $object, Query $expected) {
    $actual = $object::dao()->generateDeleteSql($object);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("delete");
