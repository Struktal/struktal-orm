<?php

use jensostertag\DatabaseObjects\Database\Database;
use jensostertag\DatabaseObjects\Database\Query;
use jensostertag\DatabaseObjects\GenericObject;
use jensostertag\DatabaseObjects\GenericObjectDAO;
use jensostertag\DatabaseObjects\DAOFilter;
use jensostertag\DatabaseObjects\DAOFilterOperator;

test("Correct DAOFilter terms", function(DAOFilter $daoFilter, mixed $index, Query $expected) {
    $actualTerm = $daoFilter->generateSqlTerm($index);
    $actualValues = $daoFilter->values($index);

    expect($actualTerm)->toBe($expected->getSql())
        ->and($actualValues)->toBe($expected->getParameters());
})->with("daoFilter");
