<?php

namespace struktal\ORM;

use \DateTime;
use \DateTimeImmutable;
use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionNamedType;

class GenericEntity extends internal\GenericObject {
    private static array $dao = [];

    public ?int $id;
    public DateTimeImmutable $created;
    public DateTimeImmutable $updated;

    public function __construct() {
        $this->id = null;
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
    }

    /**
     * Returns the data access object for this class
     * @return GenericEntityDAO
     */
    public static function dao(): GenericEntityDAO {
        if(!(array_key_exists(get_called_class(), self::$dao))) {
            if(class_exists(get_called_class() . "DAO")) {
                $daoClassName = get_called_class() . "DAO";
                self::$dao[get_called_class()] = new $daoClassName(get_called_class());
            } else {
                trigger_error("DAO for class " . get_called_class() . " requested, but not found", E_USER_WARNING);
            }
        }

        return self::$dao[get_called_class()];
    }

    /**
     * Imports data from an array to the object
     * @param array $data
     * @return void
     */
    public function fromArray(array $data): void {
        $reflection = new ReflectionClass($this);

        foreach($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyType = $property->getType();
            $propertyName = $property->getName();

            if(!array_key_exists($propertyName, $data)) {
                trigger_error("Property \"{$propertyName}\" does not exist in data array", E_USER_WARNING);
                continue;
            }

            if($propertyType instanceof ReflectionNamedType) {
                if($data[$propertyName] === null && $propertyType->allowsNull()) {
                    $this->$propertyName = null;
                    continue;
                }

                if($propertyType->getName() === DateTime::class) {
                    $this->$propertyName = DateTime::createFromFormat("Y-m-d H:i:s.v", $data[$propertyName]);
                    continue;
                } else if($propertyType->getName() === DateTimeImmutable::class) {
                    $this->$propertyName = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.v", $data[$propertyName]);
                    continue;
                } else if(!$propertyType->isBuiltin()) {
                    try {
                        $propertyReflection = new ReflectionClass($propertyType->getName());
                        if($propertyReflection->isSubclassOf(GenericEntity::class)) {
                            $dao = call_user_func([$propertyType->getName(), "dao"]);

                            $object = $dao->getObject([
                                "id" => $data[$propertyName]
                            ]);

                            $this->$propertyName = $object;
                            continue;
                        } else if($propertyReflection->isEnum() && $propertyReflection->implementsInterface(ORMEnum::class)) {
                            $this->$propertyName = call_user_func([$propertyType->getName(), "tryFrom"], $data[$propertyName]);
                            continue;
                        }
                    } catch(\ReflectionException $e) {
                        trigger_error("Could not reflect property type \"" . $propertyType->getName() . "\" for property \"" . $propertyName . "\": " . $e->getMessage(), E_USER_WARNING);
                    }
                }
            }

            $this->$propertyName = $data[$propertyName];
        }
    }

    /**
     * Exports the object's data to an array
     * @return array
     */
    public function toArray(): array {
        $classProperties = get_object_vars($this);
        $data = [];
        foreach($classProperties as $property => $value) {
            $data[$property] = $this->$property;
        }

        return $data;
    }

    /**
     * Returns the object's ID
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Sets the object's ID
     * @param int $id
     */
    private function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Returns the object's creation date
     * @return DateTimeImmutable
     */
    public function getCreated(): DateTimeImmutable {
        return $this->created;
    }

    /**
     * Sets the object's creation date
     * @param DateTimeImmutable $created
     */
    public function setCreated(DateTimeImmutable $created): void {
        $this->created = $created;
    }

    /**
     * Returns the object's last update date
     * @return DateTimeImmutable
     */
    public function getUpdated(): DateTimeImmutable {
        return $this->updated;
    }

    /**
     * Sets the object's last update date
     * @param DateTimeImmutable $updated
     */
    public function setUpdated(DateTimeImmutable $updated): void {
        $this->updated = $updated;
    }
}
