<?php

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericObject;
use struktal\ORM\GenericObjectDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

test("Correct select queries", function(string $objectClass, array $generationArgs, Query $expected) {
    /** @var class-string<GenericObjectDAO> $objectClass */
    $actual = call_user_func_array($objectClass::dao()->generateQuerySql(...), $generationArgs);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("select");
