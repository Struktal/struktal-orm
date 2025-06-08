<?php

use struktal\DatabaseObjects\Database\Database;
use struktal\DatabaseObjects\Database\Query;
use struktal\DatabaseObjects\GenericObject;
use struktal\DatabaseObjects\GenericObjectDAO;
use struktal\DatabaseObjects\DAOFilter;
use struktal\DatabaseObjects\DAOFilterOperator;

test("Correct DAOFilter terms", function(DAOFilter $daoFilter, mixed $index, Query $expected) {
    $actualTerm = $daoFilter->generateSqlTerm($index);
    $actualValues = $daoFilter->values($index);

    expect($actualTerm)->toBe($expected->getSql())
        ->and($actualValues)->toBe($expected->getParameters());
})->with("daoFilter");
