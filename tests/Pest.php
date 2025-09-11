<?php

require_once(__DIR__ . "/../vendor/autoload.php");

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericEntity;
use struktal\ORM\GenericEntityDAO;
use struktal\ORM\DAOFilter;
use struktal\ORM\DAOFilterOperator;

class SimpleEntity extends GenericEntity {}
class SimpleEntityDAO extends GenericEntityDAO {}
$newSimpleEntity = new SimpleEntity();
$existingSimpleEntity = new SimpleEntity();
$existingSimpleEntity->id = 1;
$existingSimpleEntity->created = new DateTimeImmutable("2000-01-01 00:00:00");
$existingSimpleEntity->updated = new DateTimeImmutable("2000-01-01 00:00:00");

class ExtendedEntity extends GenericEntity {
    public string $name;
    public int $age;
}
class ExtendedEntityDAO extends GenericEntityDAO {}
$newExtendedEntity = new ExtendedEntity();
$newExtendedEntity->name = "John Doe";
$newExtendedEntity->age = 30;
$existingExtendedEntity = new ExtendedEntity();
$existingExtendedEntity->id = 2;
$existingExtendedEntity->created = new DateTimeImmutable("2000-01-01 00:00:00");
$existingExtendedEntity->updated = new DateTimeImmutable("2000-01-01 00:00:00");
$existingExtendedEntity->name = "Jane Doe";
$existingExtendedEntity->age = 25;

class ComplexEntity extends GenericEntity {
    public string $name;
    public DateTimeImmutable $birthdate;
    public float $height;
    public bool $active;
}
class ComplexEntityDAO extends GenericEntityDAO {}
$newComplexEntity = new ComplexEntity();
$newComplexEntity->name = "John Doe";
$newComplexEntity->birthdate = new DateTimeImmutable("2000-01-01 00:00:00");
$newComplexEntity->height = 1.75;
$newComplexEntity->active = true;
$existingComplexEntity = new ComplexEntity();
$existingComplexEntity->id = 3;
$existingComplexEntity->created = new DateTimeImmutable("2000-01-01 00:00:00");
$existingComplexEntity->updated = new DateTimeImmutable("2000-01-01 00:00:00");
$existingComplexEntity->name = "Jane Doe";
$existingComplexEntity->birthdate = new DateTimeImmutable("2000-01-01 00:00:00");
$existingComplexEntity->height = 1.65;
$existingComplexEntity->active = false;

dataset("upsert", [
    "simpleInsert" => [
        $existingSimpleEntity,
        new Query(
            "UPDATE `SimpleEntity` SET `updated` = :updated WHERE `id` = :id",
            [
                "updated" => $existingSimpleEntity->updated,
                "id" => $existingSimpleEntity->id
            ]
        )
    ],
    "simpleUpdate" => [
        $existingSimpleEntity,
        new Query(
            "UPDATE `SimpleEntity` SET `updated` = :updated WHERE `id` = :id",
            [
                "updated" => $existingSimpleEntity->updated,
                "id" => $existingSimpleEntity->id
            ]
        )
    ],
    "extendedInsert" => [
        $existingExtendedEntity,
        new Query(
            "UPDATE `ExtendedEntity` SET `updated` = :updated, `name` = :name, `age` = :age WHERE `id` = :id",
            [
                "updated" => $existingExtendedEntity->updated,
                "name" => $existingExtendedEntity->name,
                "age" => $existingExtendedEntity->age,
                "id" => $existingExtendedEntity->id
            ]
        )
    ],
    "extendedUpdate" => [
        $existingExtendedEntity,
        new Query(
            "UPDATE `ExtendedEntity` SET `updated` = :updated, `name` = :name, `age` = :age WHERE `id` = :id",
            [
                "updated" => $existingExtendedEntity->updated,
                "name" => $existingExtendedEntity->name,
                "age" => $existingExtendedEntity->age,
                "id" => $existingExtendedEntity->id
            ]
        )
    ],
    "complexInsert" => [
        $existingComplexEntity,
        new Query(
            "UPDATE `ComplexEntity` SET `updated` = :updated, `name` = :name, `birthdate` = :birthdate, `height` = :height, `active` = :active WHERE `id` = :id",
            [
                "updated" => $existingComplexEntity->updated,
                "name" => $existingComplexEntity->name,
                "birthdate" => $existingComplexEntity->birthdate,
                "height" => $existingComplexEntity->height,
                "active" => $existingComplexEntity->active,
                "id" => $existingComplexEntity->id
            ]
        )
    ],
    "complexUpdate" => [
        $existingComplexEntity,
        new Query(
            "UPDATE `ComplexEntity` SET `updated` = :updated, `name` = :name, `birthdate` = :birthdate, `height` = :height, `active` = :active WHERE `id` = :id",
            [
                "updated" => $existingComplexEntity->updated,
                "name" => $existingComplexEntity->name,
                "birthdate" => $existingComplexEntity->birthdate,
                "height" => $existingComplexEntity->height,
                "active" => $existingComplexEntity->active,
                "id" => $existingComplexEntity->id
            ]
        )
    ]
]);

dataset("delete", [
    "simpleDelete" => [
        $existingSimpleEntity,
        new Query(
            "DELETE FROM `SimpleEntity` WHERE `id` = :id",
            [
                "id" => $existingSimpleEntity->id
            ]
        )
    ],
    "extendedDelete" => [
        $existingExtendedEntity,
        new Query(
            "DELETE FROM `ExtendedEntity` WHERE `id` = :id",
            [
                "id" => $existingExtendedEntity->id
            ]
        )
    ],
    "complexDelete" => [
        $existingComplexEntity,
        new Query(
            "DELETE FROM `ComplexEntity` WHERE `id` = :id",
            [
                "id" => $existingComplexEntity->id
            ]
        )
    ]
]);

dataset("select", [
    "simpleGetAll" => [
        SimpleEntity::class,
        [],
        new Query(
            "SELECT * FROM `SimpleEntity` ORDER BY `id` ASC",
            []
        )
    ],
    "complexGetAll" => [
        ComplexEntity::class,
        [],
        new Query(
            "SELECT * FROM `ComplexEntity` ORDER BY `id` ASC",
            []
        )
    ],
    "simpleGetWithSimpleFilter" => [
        SimpleEntity::class,
        [
            "filter" => [
                "id" => $existingSimpleEntity->getId()
            ]
        ],
        new Query(
            "SELECT * FROM `SimpleEntity` WHERE `id` = :id ORDER BY `id` ASC",
            [
                "id" => $existingSimpleEntity->getId()
            ]
        )
    ],
    "complexGetWithSimpleFilterAndOrder" => [
        ComplexEntity::class,
        [
            "filter" => [
                "id" => $existingComplexEntity->getId()
            ],
            "orderBy" => "name",
            "orderAsc" => false
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `id` = :id ORDER BY `name` DESC",
            [
                "id" => $existingComplexEntity->getId()
            ]
        )
    ],
    "complexGetWithSimpleFilterAndLimit" => [
        ComplexEntity::class,
        [
            "filter" => [
                "id" => $existingComplexEntity->getId()
            ],
            "limit" => 1
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `id` = :id ORDER BY `id` ASC LIMIT 1 OFFSET 0",
            [
                "id" => $existingComplexEntity->getId()
            ]
        )
    ],
    "complexGetWithComplexFilter" => [
        ComplexEntity::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexEntity->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexEntity->height
                )
            ]
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `name` != :0 AND `height` > :1 ORDER BY `id` ASC",
            [
                0 => $existingComplexEntity->name,
                1 => $existingComplexEntity->height
            ]
        )
    ],
    "complexGetWithComplexFilterAndOrder" => [
        ComplexEntity::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexEntity->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexEntity->height
                )
            ],
            "orderBy" => "birthdate",
            "orderAsc" => true
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `name` != :0 AND `height` > :1 ORDER BY `birthdate` ASC",
            [
                0 => $existingComplexEntity->name,
                1 => $existingComplexEntity->height
            ]
        )
    ],
    "complexGetWithComplexFilterAndLimit" => [
        ComplexEntity::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexEntity->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexEntity->height
                )
            ],
            "limit" => 5,
            "offset" => 10
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `name` != :0 AND `height` > :1 ORDER BY `id` ASC LIMIT 5 OFFSET 10",
            [
                0 => $existingComplexEntity->name,
                1 => $existingComplexEntity->height
            ]
        )
    ],
    "complexGetWithMixedFilter" => [
        ComplexEntity::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexEntity->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexEntity->height
                ),
                "active" => true
            ]
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `name` != :0 AND `height` > :1 AND `active` = :active ORDER BY `id` ASC",
            [
                0 => $existingComplexEntity->name,
                1 => $existingComplexEntity->height,
                "active" => true
            ]
        )
    ],
    "complexGetWithMixedFilterAndOrder" => [
        ComplexEntity::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexEntity->name
                ),
                "active" => true,
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexEntity->height
                )
            ],
            "orderBy" => "birthdate",
            "orderAsc" => false
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `name` != :0 AND `active` = :active AND `height` > :1 ORDER BY `birthdate` DESC",
            [
                0 => $existingComplexEntity->name,
                "active" => true,
                1 => $existingComplexEntity->height
            ]
        )
    ],
    "complexGetWithMixedFilterAndLimit" => [
        ComplexEntity::class,
        [
            "filter" => [
                "active" => true,
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexEntity->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexEntity->height
                )
            ],
            "limit" => 3,
            "offset" => 1
        ],
        new Query(
            "SELECT * FROM `ComplexEntity` WHERE `active` = :active AND `name` != :0 AND `height` > :1 ORDER BY `id` ASC LIMIT 3 OFFSET 1",
            [
                "active" => true,
                0 => $existingComplexEntity->name,
                1 => $existingComplexEntity->height
            ]
        )
    ]
]);

dataset("daoFilter", [
    "equalsString" => [
        new DAOFilter(DAOFilterOperator::EQUALS, "name", "John Doe"),
        0,
        new Query(
            "`name` = :0",
            ["0" => "John Doe"]
        )
    ],
    "equalsNumber" => [
        new DAOFilter(DAOFilterOperator::EQUALS, "age", 30),
        0,
        new Query(
            "`age` = :0",
            ["0" => 30]
        )
    ],
    "equalsNull" => [
        new DAOFilter(DAOFilterOperator::EQUALS, "name", null),
        0,
        new Query(
            "`name` IS NULL",
            []
        )
    ],
    "notEqualsString" => [
        new DAOFilter(DAOFilterOperator::NOT_EQUALS, "name", "John Doe"),
        0,
        new Query(
            "`name` != :0",
            ["0" => "John Doe"]
        )
    ],
    "notEqualsNumber" => [
        new DAOFilter(DAOFilterOperator::NOT_EQUALS, "age", 30),
        0,
        new Query(
            "`age` != :0",
            ["0" => 30]
        )
    ],
    "notEqualsNull" => [
        new DAOFilter(DAOFilterOperator::NOT_EQUALS, "name", null),
        0,
        new Query(
            "`name` IS NOT NULL",
            []
        )
    ],
    "greaterThan" => [
        new DAOFilter(DAOFilterOperator::GREATER_THAN, "age", 30),
        0,
        new Query(
            "`age` > :0",
            ["0" => 30]
        )
    ],
    "lessThan" => [
        new DAOFilter(DAOFilterOperator::LESS_THAN, "age", 30),
        0,
        new Query(
            "`age` < :0",
            ["0" => 30]
        )
    ],
    "greaterThanEquals" => [
        new DAOFilter(DAOFilterOperator::GREATER_THAN_EQUALS, "age", 30),
        0,
        new Query(
            "`age` >= :0",
            ["0" => 30]
        )
    ],
    "lessThanEquals" => [
        new DAOFilter(DAOFilterOperator::LESS_THAN_EQUALS, "age", 30),
        0,
        new Query(
            "`age` <= :0",
            ["0" => 30]
        )
    ],
    "like" => [
        new DAOFilter(DAOFilterOperator::LIKE, "name", "%John%"),
        0,
        new Query(
            "`name` LIKE :0",
            ["0" => "%John%"]
        )
    ],
    "inArrayWithStrings" => [
        new DAOFilter(DAOFilterOperator::IN, "name", ["John Doe", "Jane Doe"]),
        0,
        new Query(
            "`name` IN (:0_0, :0_1)",
            ["0_0" => "John Doe", "0_1" => "Jane Doe"]
        )
    ],
    "inArrayWithNumbers" => [
        new DAOFilter(DAOFilterOperator::IN, "age", [25, 30]),
        0,
        new Query(
            "`age` IN (:0_0, :0_1)",
            ["0_0" => 25, "0_1" => 30]
        )
    ],
    "inArrayWithOneNull" => [
        new DAOFilter(DAOFilterOperator::IN, "name", [null]),
        0,
        new Query(
            "`name` IS NULL",
            []
        )
    ],
    "inArrayWithMultipleNulls" => [
        new DAOFilter(DAOFilterOperator::IN, "name", [null, null]),
        0,
        new Query(
            "`name` IS NULL",
            []
        )
    ],
    "inArrayWithMixed" => [
        new DAOFilter(DAOFilterOperator::IN, "name", ["John Doe", null, "Jane Doe", 30]),
        0,
        new Query(
            "(`name` IS NULL OR `name` IN (:0_0, :0_2, :0_3))",
            ["0_0" => "John Doe", "0_2" => "Jane Doe", "0_3" => 30]
        )
    ],
    "inArrayWithEmpty" => [
        new DAOFilter(DAOFilterOperator::IN, "name", []),
        0,
        new Query(
            "1 = 0",
            []
        )
    ],
    "notInArrayWithStrings" => [
        new DAOFilter(DAOFilterOperator::NOT_IN, "name", ["John Doe", "Jane Doe"]),
        0,
        new Query(
            "`name` NOT IN (:0_0, :0_1)",
            ["0_0" => "John Doe", "0_1" => "Jane Doe"]
        )
    ],
    "notInArrayWithNumbers" => [
        new DAOFilter(DAOFilterOperator::NOT_IN, "age", [25, 30]),
        0,
        new Query(
            "`age` NOT IN (:0_0, :0_1)",
            ["0_0" => 25, "0_1" => 30]
        )
    ],
    "notInArrayWithOneNull" => [
        new DAOFilter(DAOFilterOperator::NOT_IN, "name", [null]),
        0,
        new Query(
            "`name` IS NOT NULL",
            []
        )
    ],
    "notInArrayWithMultipleNulls" => [
        new DAOFilter(DAOFilterOperator::NOT_IN, "name", [null, null]),
        0,
        new Query(
            "`name` IS NOT NULL",
            []
        )
    ],
    "notInArrayWithMixed" => [
        new DAOFilter(DAOFilterOperator::NOT_IN, "name", ["John Doe", null, "Jane Doe", 30]),
        0,
        new Query(
            "(`name` IS NOT NULL AND `name` NOT IN (:0_0, :0_2, :0_3))",
            ["0_0" => "John Doe", "0_2" => "Jane Doe", "0_3" => 30]
        )
    ],
    "notInArrayWithEmpty" => [
        new DAOFilter(DAOFilterOperator::NOT_IN, "name", []),
        0,
        new Query(
            "1 = 1",
            []
        )
    ],
]);
