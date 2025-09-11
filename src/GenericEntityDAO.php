<?php

namespace struktal\ORM;

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\internal\GenericObject;
use struktal\ORM\internal\GenericObjectDAO;

class GenericEntityDAO extends GenericObjectDAO {
    /**
     * Saves an object with its current attributes to the database
     * @param GenericObject $object
     * @return bool
     */
    public function save(GenericObject $object): bool {
        if(!$object instanceof GenericEntity) {
            return false;
        }

        $insert = $object->id === null;

        $success = parent::save($object);
        if(!$success) {
            return false;
        }

        if($insert) {
            $object->id = Database::getConnection()->lastInsertId();
        }

        return true;
    }

    /**
     * Deletes an object from the database
     * @param GenericObject $object
     * @return bool
     */
    public function delete(GenericObject $object): bool {
        if(!$object instanceof GenericEntity) {
            return false;
        }

        if($object->id === null) {
            trigger_error("Trying to delete " . get_class($object) . ", but id is null", E_USER_WARNING);
            return false;
        }

        return parent::delete($object);
    }

    public function generateUpsertSql(GenericObject $object): Query {
        if(!$object instanceof GenericEntity) {
            throw new \InvalidArgumentException("Object must be an instance of GenericEntity");
        }

        $objectProperties = get_object_vars($object);
        $insert = $object->id === null;

        $bindParameters = [];

        $sql = ($insert ? "INSERT INTO " : "UPDATE ") . "`{$this->getClassInstance()}` SET ";
        foreach($objectProperties as $property => $value) {
            if(!$insert && ($property === "id" || $property === "created")) {
                continue;
            }

            $sql .= "`{$property}` = :{$property}, ";
            $bindParameters[$property] = $value;
        }
        $sql = substr($sql, 0, -2);
        if(!$insert) {
            $sql .= " WHERE `id` = :id";
            $bindParameters["id"] = $object->id;
        }

        return new Query($sql, $bindParameters);
    }

    public function generateDeleteSql(GenericObject $object): Query {
        if(!$object instanceof GenericEntity) {
            throw new \InvalidArgumentException("Object must be an instance of GenericEntity");
        }

        $sql = "DELETE FROM `{$this->getClassInstance()}` WHERE `id` = :id";
        $bindParameters = [
            "id" => $object->getId()
        ];

        return new Query($sql, $bindParameters);
    }
}
