<?php

namespace struktal\ORM\internal;

use struktal\DatabaseObjects\InheritedType;
use struktal\ORM\ORMEnum;
use \DateTime;
use \DateTimeImmutable;
use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionNamedType;

abstract class GenericObject {
    private static array $dao = [];

    /**
     * Returns the data access object for this class
     * @return GenericObjectDAO
     */
    public static function dao(): GenericObjectDAO {
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
                        $propertyClassName = $propertyType->getName();

                        $propertyAttributes = $propertyReflection->getAttributes();
                        foreach($propertyAttributes as $attribute) {
                            if($attribute->getName() === InheritedType::class) {
                                if(count($attribute->getArguments()) > 0) {
                                    $inheritedTypeClass = $attribute->getArguments()[0];
                                    if(class_exists($inheritedTypeClass) && is_subclass_of($inheritedTypeClass, $propertyClassName)) {
                                        $propertyClassName = $inheritedTypeClass;
                                    } else {
                                        trigger_error("Inherited type class \"" . $inheritedTypeClass . "\" does not exist or is not a subclass of \"" . $propertyClassName . "\"", E_USER_WARNING);
                                    }
                                } else {
                                    trigger_error("InheritedType attribute on property \"" . $propertyName . "\" does not have a class argument", E_USER_WARNING);
                                }
                            }
                        }

                        if($propertyReflection->isSubclassOf(GenericObject::class)) {
                            $dao = call_user_func([$propertyClassName, "dao"]);

                            $object = $dao->getObject([
                                "id" => $data[$propertyName]
                            ]);

                            $this->$propertyName = $object;
                            continue;
                        } else if($propertyReflection->isEnum() && $propertyReflection->implementsInterface(ORMEnum::class)) {
                            $this->$propertyName = call_user_func([$propertyClassName, "tryFrom"], $data[$propertyName]);
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
}
