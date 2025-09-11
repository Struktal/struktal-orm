<?php

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericEntity;
use struktal\ORM\GenericEntityDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

test("Correct DAOFilter terms", function(DAOFilter $daoFilter, mixed $index, Query $expected) {
    $actualTerm = $daoFilter->generateSqlTerm($index);
    $actualValues = $daoFilter->values($index);

    expect($actualTerm)->toBe($expected->getSql())
        ->and($actualValues)->toBe($expected->getParameters());
})->with("daoFilter");
