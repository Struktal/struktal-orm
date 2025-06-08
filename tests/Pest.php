<?php

include "src/database/Database.class.php";
include "src/database/Query.class.php";
include "src/DAOFilterOperator.enum.php";
include "src/DAOFilter.class.php";
include "src/GenericObject.class.php";
include "src/GenericObjectDAO.class.php";

use struktal\DatabaseObjects\Database\Database;
use struktal\DatabaseObjects\Database\Query;
use struktal\DatabaseObjects\GenericObject;
use struktal\DatabaseObjects\GenericObjectDAO;
use struktal\DatabaseObjects\DAOFilter;
use struktal\DatabaseObjects\DAOFilterOperator;

class SimpleObject extends GenericObject {}
class SimpleObjectDAO extends GenericObjectDAO {}
$newSimpleObject = new SimpleObject();
$existingSimpleObject = new SimpleObject();
$existingSimpleObject->id = 1;
$existingSimpleObject->created = new DateTimeImmutable("2000-01-01 00:00:00");
$existingSimpleObject->updated = new DateTimeImmutable("2000-01-01 00:00:00");

class ExtendedObject extends GenericObject {
    public string $name;
    public int $age;
}
class ExtendedObjectDAO extends GenericObjectDAO {}
$newExtendedObject = new ExtendedObject();
$newExtendedObject->name = "John Doe";
$newExtendedObject->age = 30;
$existingExtendedObject = new ExtendedObject();
$existingExtendedObject->id = 2;
$existingExtendedObject->created = new DateTimeImmutable("2000-01-01 00:00:00");
$existingExtendedObject->updated = new DateTimeImmutable("2000-01-01 00:00:00");
$existingExtendedObject->name = "Jane Doe";
$existingExtendedObject->age = 25;

class ComplexObject extends GenericObject {
    public string $name;
    public DateTimeImmutable $birthdate;
    public float $height;
    public bool $active;
}
class ComplexObjectDAO extends GenericObjectDAO {}
$newComplexObject = new ComplexObject();
$newComplexObject->name = "John Doe";
$newComplexObject->birthdate = new DateTimeImmutable("2000-01-01 00:00:00");
$newComplexObject->height = 1.75;
$newComplexObject->active = true;
$existingComplexObject = new ComplexObject();
$existingComplexObject->id = 3;
$existingComplexObject->created = new DateTimeImmutable("2000-01-01 00:00:00");
$existingComplexObject->updated = new DateTimeImmutable("2000-01-01 00:00:00");
$existingComplexObject->name = "Jane Doe";
$existingComplexObject->birthdate = new DateTimeImmutable("2000-01-01 00:00:00");
$existingComplexObject->height = 1.65;
$existingComplexObject->active = false;

dataset("upsert", [
    "simpleInsert" => [
        $existingSimpleObject,
        new Query(
            "UPDATE `SimpleObject` SET `updated` = :updated WHERE `id` = :id",
            [
                "updated" => $existingSimpleObject->updated,
                "id" => $existingSimpleObject->id
            ]
        )
    ],
    "simpleUpdate" => [
        $existingSimpleObject,
        new Query(
            "UPDATE `SimpleObject` SET `updated` = :updated WHERE `id` = :id",
            [
                "updated" => $existingSimpleObject->updated,
                "id" => $existingSimpleObject->id
            ]
        )
    ],
    "extendedInsert" => [
        $existingExtendedObject,
        new Query(
            "UPDATE `ExtendedObject` SET `updated` = :updated, `name` = :name, `age` = :age WHERE `id` = :id",
            [
                "updated" => $existingExtendedObject->updated,
                "name" => $existingExtendedObject->name,
                "age" => $existingExtendedObject->age,
                "id" => $existingExtendedObject->id
            ]
        )
    ],
    "extendedUpdate" => [
        $existingExtendedObject,
        new Query(
            "UPDATE `ExtendedObject` SET `updated` = :updated, `name` = :name, `age` = :age WHERE `id` = :id",
            [
                "updated" => $existingExtendedObject->updated,
                "name" => $existingExtendedObject->name,
                "age" => $existingExtendedObject->age,
                "id" => $existingExtendedObject->id
            ]
        )
    ],
    "complexInsert" => [
        $existingComplexObject,
        new Query(
            "UPDATE `ComplexObject` SET `updated` = :updated, `name` = :name, `birthdate` = :birthdate, `height` = :height, `active` = :active WHERE `id` = :id",
            [
                "updated" => $existingComplexObject->updated,
                "name" => $existingComplexObject->name,
                "birthdate" => $existingComplexObject->birthdate,
                "height" => $existingComplexObject->height,
                "active" => $existingComplexObject->active,
                "id" => $existingComplexObject->id
            ]
        )
    ],
    "complexUpdate" => [
        $existingComplexObject,
        new Query(
            "UPDATE `ComplexObject` SET `updated` = :updated, `name` = :name, `birthdate` = :birthdate, `height` = :height, `active` = :active WHERE `id` = :id",
            [
                "updated" => $existingComplexObject->updated,
                "name" => $existingComplexObject->name,
                "birthdate" => $existingComplexObject->birthdate,
                "height" => $existingComplexObject->height,
                "active" => $existingComplexObject->active,
                "id" => $existingComplexObject->id
            ]
        )
    ]
]);

dataset("delete", [
    "simpleDelete" => [
        $existingSimpleObject,
        new Query(
            "DELETE FROM `SimpleObject` WHERE `id` = :id",
            [
                "id" => $existingSimpleObject->id
            ]
        )
    ],
    "extendedDelete" => [
        $existingExtendedObject,
        new Query(
            "DELETE FROM `ExtendedObject` WHERE `id` = :id",
            [
                "id" => $existingExtendedObject->id
            ]
        )
    ],
    "complexDelete" => [
        $existingComplexObject,
        new Query(
            "DELETE FROM `ComplexObject` WHERE `id` = :id",
            [
                "id" => $existingComplexObject->id
            ]
        )
    ]
]);

dataset("select", [
    "simpleGetAll" => [
        SimpleObject::class,
        [],
        new Query(
            "SELECT * FROM `SimpleObject` ORDER BY `id` ASC",
            []
        )
    ],
    "complexGetAll" => [
        ComplexObject::class,
        [],
        new Query(
            "SELECT * FROM `ComplexObject` ORDER BY `id` ASC",
            []
        )
    ],
    "simpleGetWithSimpleFilter" => [
        SimpleObject::class,
        [
            "filter" => [
                "id" => $existingSimpleObject->getId()
            ]
        ],
        new Query(
            "SELECT * FROM `SimpleObject` WHERE `id` = :id ORDER BY `id` ASC",
            [
                "id" => $existingSimpleObject->getId()
            ]
        )
    ],
    "complexGetWithSimpleFilterAndOrder" => [
        ComplexObject::class,
        [
            "filter" => [
                "id" => $existingComplexObject->getId()
            ],
            "orderBy" => "name",
            "orderAsc" => false
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `id` = :id ORDER BY `name` DESC",
            [
                "id" => $existingComplexObject->getId()
            ]
        )
    ],
    "complexGetWithSimpleFilterAndLimit" => [
        ComplexObject::class,
        [
            "filter" => [
                "id" => $existingComplexObject->getId()
            ],
            "limit" => 1
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `id` = :id ORDER BY `id` ASC LIMIT 1 OFFSET 0",
            [
                "id" => $existingComplexObject->getId()
            ]
        )
    ],
    "complexGetWithComplexFilter" => [
        ComplexObject::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexObject->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexObject->height
                )
            ]
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `name` != :0 AND `height` > :1 ORDER BY `id` ASC",
            [
                0 => $existingComplexObject->name,
                1 => $existingComplexObject->height
            ]
        )
    ],
    "complexGetWithComplexFilterAndOrder" => [
        ComplexObject::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexObject->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexObject->height
                )
            ],
            "orderBy" => "birthdate",
            "orderAsc" => true
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `name` != :0 AND `height` > :1 ORDER BY `birthdate` ASC",
            [
                0 => $existingComplexObject->name,
                1 => $existingComplexObject->height
            ]
        )
    ],
    "complexGetWithComplexFilterAndLimit" => [
        ComplexObject::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexObject->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexObject->height
                )
            ],
            "limit" => 5,
            "offset" => 10
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `name` != :0 AND `height` > :1 ORDER BY `id` ASC LIMIT 5 OFFSET 10",
            [
                0 => $existingComplexObject->name,
                1 => $existingComplexObject->height
            ]
        )
    ],
    "complexGetWithMixedFilter" => [
        ComplexObject::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexObject->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexObject->height
                ),
                "active" => true
            ]
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `name` != :0 AND `height` > :1 AND `active` = :active ORDER BY `id` ASC",
            [
                0 => $existingComplexObject->name,
                1 => $existingComplexObject->height,
                "active" => true
            ]
        )
    ],
    "complexGetWithMixedFilterAndOrder" => [
        ComplexObject::class,
        [
            "filter" => [
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexObject->name
                ),
                "active" => true,
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexObject->height
                )
            ],
            "orderBy" => "birthdate",
            "orderAsc" => false
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `name` != :0 AND `active` = :active AND `height` > :1 ORDER BY `birthdate` DESC",
            [
                0 => $existingComplexObject->name,
                "active" => true,
                1 => $existingComplexObject->height
            ]
        )
    ],
    "complexGetWithMixedFilterAndLimit" => [
        ComplexObject::class,
        [
            "filter" => [
                "active" => true,
                new DAOFilter(
                    DAOFilterOperator::NOT_EQUALS,
                    "name",
                    $existingComplexObject->name
                ),
                new DAOFilter(
                    DAOFilterOperator::GREATER_THAN,
                    "height",
                    $existingComplexObject->height
                )
            ],
            "limit" => 3,
            "offset" => 1
        ],
        new Query(
            "SELECT * FROM `ComplexObject` WHERE `active` = :active AND `name` != :0 AND `height` > :1 ORDER BY `id` ASC LIMIT 3 OFFSET 1",
            [
                "active" => true,
                0 => $existingComplexObject->name,
                1 => $existingComplexObject->height
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
