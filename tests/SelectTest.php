<?php

use struktal\DatabaseObjects\Database\Database;
use struktal\DatabaseObjects\Database\Query;
use struktal\DatabaseObjects\GenericObject;
use struktal\DatabaseObjects\GenericObjectDAO;
use struktal\DatabaseObjects\DAOFilter;
use struktal\DatabaseObjects\DAOFilterOperator;

test("Correct select queries", function(string $objectClass, array $generationArgs, Query $expected) {
    /** @var class-string<GenericObjectDAO> $objectClass */
    $actual = call_user_func_array($objectClass::dao()->generateQuerySql(...), $generationArgs);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("select");
