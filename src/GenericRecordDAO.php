<?php

namespace struktal\ORM;

use struktal\ORM\Database\Query;
use struktal\ORM\internal\GenericObject;
use struktal\ORM\internal\GenericObjectDAO;

abstract class GenericRecordDAO extends GenericObjectDAO {
    public function get(mixed $key): mixed {
        $record = $this->getObject([
            "key" => $key
        ]);
        if($record instanceof GenericRecord) {
            return $record->value;
        }

        return null;
    }

    public function set(mixed $key, mixed $value): bool {
        $record = new ($this->getClassInstance())();
        $record->key = $key;
        $record->value = $value;

        return $this->save($record);
    }

    /**
     * Saves an object with its current attributes to the database
     * @param GenericObject $object
     * @return bool
     */
    public function save(GenericObject $object): bool {
        if(!$object instanceof GenericRecord) {
            return false;
        }

        return parent::save($object);
    }

    /**
     * Deletes an object from the database
     * @param GenericObject $object
     * @return bool
     */
    public function delete(GenericObject $object): bool {
        if(!$object instanceof GenericRecord) {
            return false;
        }

        return parent::delete($object);
    }

    public function generateUpsertSql(GenericObject $object): Query {
        if(!$object instanceof GenericRecord) {
            throw new \InvalidArgumentException("Object must be an instance of GenericRecord");
        }

        $objectProperties = get_object_vars($object);
        $existingRecord = $this->getObject([
            "key" => $object->key
        ]);
        $insert = !$existingRecord instanceof GenericRecord;

        $bindParameters = [];

        $sql = ($insert ? "INSERT INTO " : "UPDATE ") . "`{$this->getClassInstance()}` SET ";
        foreach($objectProperties as $property => $value) {
            if(!$insert && ($property === "key")) {
                continue;
            }

            $sql .= "`{$property}` = :{$property}, ";
            $bindParameters[$property] = $value;
        }
        $sql = substr($sql, 0, -2);
        if(!$insert) {
            $sql .= " WHERE `key` = :key";
            $bindParameters["key"] = $object->key;
        }

        return new Query($sql, $bindParameters);
    }

    public function generateDeleteSql(GenericObject $object): Query {
        if(!$object instanceof GenericRecord) {
            throw new \InvalidArgumentException("Object must be an instance of GenericRecord");
        }

        $sql = "DELETE FROM `{$this->getClassInstance()}` WHERE `key` = :key";
        $bindParameters = [
            "id" => $object->key
        ];

        return new Query($sql, $bindParameters);
    }
}
