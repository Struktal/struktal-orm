<?php

use jensostertag\DatabaseObjects\Database\Database;
use jensostertag\DatabaseObjects\Database\Query;
use jensostertag\DatabaseObjects\GenericObject;
use jensostertag\DatabaseObjects\GenericObjectDAO;
use jensostertag\DatabaseObjects\DAOFilter;
use jensostertag\DatabaseObjects\DAOFilterOperator;

test("Correct select queries", function(string $objectClass, array $generationArgs, Query $expected) {
    /** @var class-string<GenericObjectDAO> $objectClass */
    $actual = call_user_func_array($objectClass::dao()->generateQuerySql(...), $generationArgs);

    expect($actual->getSql())->toBe($expected->getSql())
        ->and($actual->getParameters())->toBe($expected->getParameters());
})->with("select");
