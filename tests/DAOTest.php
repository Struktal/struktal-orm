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

test("Correct DAO objects", function() {
    expect(SimpleObject::dao())->toBeInstanceOf(SimpleObjectDAO::class)
        ->and(ExtendedObject::dao())->toBeInstanceOf(ExtendedObjectDAO::class)
        ->and(ComplexObject::dao())->toBeInstanceOf(ComplexObjectDAO::class);
});

test("Correct insert queries", function() use ($newSimpleObject, $newExtendedObject, $newComplexObject) {
    $simpleQuery = SimpleObject::dao()->generateUpsertSql($newSimpleObject);
    $extendedQuery = ExtendedObject::dao()->generateUpsertSql($newExtendedObject);
    $complexQuery = ComplexObject::dao()->generateUpsertSql($newComplexObject);

    $simpleInsertExpectedSql = "INSERT INTO `SimpleObject` SET `id` = :id, `created` = :created, `updated` = :updated";
    $simpleInsertExpectedParameters = [
        "id" => null,
        "created" => $newSimpleObject->created,
        "updated" => $newSimpleObject->updated
    ];
    $extendedInsertExpectedSql = "INSERT INTO `ExtendedObject` SET `id` = :id, `created` = :created, `updated` = :updated, `name` = :name, `age` = :age";
    $extendedInsertExpectedParameters = [
        "id" => null,
        "created" => $newExtendedObject->created,
        "updated" => $newExtendedObject->updated,
        "name" => $newExtendedObject->name,
        "age" => $newExtendedObject->age
    ];
    $complexInsertExpectedSql = "INSERT INTO `ComplexObject` SET `id` = :id, `created` = :created, `updated` = :updated, `name` = :name, `birthdate` = :birthdate, `height` = :height, `active` = :active";
    $complexInsertExpectedParameters = [
        "id" => null,
        "created" => $newComplexObject->created,
        "updated" => $newComplexObject->updated,
        "name" => $newComplexObject->name,
        "birthdate" => $newComplexObject->birthdate,
        "height" => $newComplexObject->height,
        "active" => $newComplexObject->active
    ];

    expect($simpleQuery->getSql())->toBe($simpleInsertExpectedSql)
        ->and($extendedQuery->getSql())->toBe($extendedInsertExpectedSql)
        ->and($complexQuery->getSql())->toBe($complexInsertExpectedSql)
        ->and($simpleQuery->getParameters())->toBe($simpleInsertExpectedParameters)
        ->and($extendedQuery->getParameters())->toBe($extendedInsertExpectedParameters)
        ->and($complexQuery->getParameters())->toBe($complexInsertExpectedParameters);
});

test("Correct update queries", function() use ($existingSimpleObject, $existingExtendedObject, $existingComplexObject) {
    $simpleQuery = SimpleObject::dao()->generateUpsertSql($existingSimpleObject);
    $extendedQuery = ExtendedObject::dao()->generateUpsertSql($existingExtendedObject);
    $complexQuery = ComplexObject::dao()->generateUpsertSql($existingComplexObject);

    $simpleUpdateExpectedSql = "UPDATE `SimpleObject` SET `updated` = :updated WHERE `id` = :id";
    $simpleUpdateExpectedParameters = [
        "updated" => $existingSimpleObject->updated,
        "id" => $existingSimpleObject->id
    ];
    $extendedUpdateExpectedSql = "UPDATE `ExtendedObject` SET `updated` = :updated, `name` = :name, `age` = :age WHERE `id` = :id";
    $extendedUpdateExpectedParameters = [
        "updated" => $existingExtendedObject->updated,
        "name" => $existingExtendedObject->name,
        "age" => $existingExtendedObject->age,
        "id" => $existingExtendedObject->id
    ];
    $complexUpdateExpectedSql = "UPDATE `ComplexObject` SET `updated` = :updated, `name` = :name, `birthdate` = :birthdate, `height` = :height, `active` = :active WHERE `id` = :id";
    $complexUpdateExpectedParameters = [
        "updated" => $existingComplexObject->updated,
        "name" => $existingComplexObject->name,
        "birthdate" => $existingComplexObject->birthdate,
        "height" => $existingComplexObject->height,
        "active" => $existingComplexObject->active,
        "id" => $existingComplexObject->id
    ];

    expect($simpleQuery->getSql())->toBe($simpleUpdateExpectedSql)
        ->and($extendedQuery->getSql())->toBe($extendedUpdateExpectedSql)
        ->and($complexQuery->getSql())->toBe($complexUpdateExpectedSql)
        ->and($simpleQuery->getParameters())->toBe($simpleUpdateExpectedParameters)
        ->and($extendedQuery->getParameters())->toBe($extendedUpdateExpectedParameters)
        ->and($complexQuery->getParameters())->toBe($complexUpdateExpectedParameters);
});
