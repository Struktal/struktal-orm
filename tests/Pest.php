<?php

include "src/database/Database.class.php";
include "src/database/Query.class.php";
include "src/DAOFilterOperator.enum.php";
include "src/DAOFilter.class.php";
include "src/GenericObject.class.php";
include "src/GenericObjectDAO.class.php";

use jensostertag\DatabaseObjects\Database\Database;
use jensostertag\DatabaseObjects\Database\Query;
use jensostertag\DatabaseObjects\GenericObject;
use jensostertag\DatabaseObjects\GenericObjectDAO;
use jensostertag\DatabaseObjects\DAOFilter;
use jensostertag\DatabaseObjects\DAOFilterOperator;

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
    ]
]);
