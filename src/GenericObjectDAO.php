<?php

namespace struktal\ORM;

use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use \PDO;
use \PDOStatement;

use \DateTime;
use \DateTimeImmutable;
use \DateTimeInterface;

class GenericObjectDAO {
    private string $CLASS_INSTANCE = "";

    public function __construct($CLASS_INSTANCE) {
        $this->CLASS_INSTANCE = $CLASS_INSTANCE;
    }

    public function getObjectName(): string {
        return $this->CLASS_INSTANCE;
    }

    /**
     * Saves an object with its current attributes to the database
     * @param GenericObject $object
     * @return bool
     */
    public function save(GenericObject $object): bool {
        if($this->tableExists($this->CLASS_INSTANCE)) {
            $insert = $object->getId() === null;

            $query = $this->generateUpsertSql($object);
            $stmt = Database::getConnection()->prepare($query->getSql());

            foreach($query->getParameters() as $parameter => $value) {
                $this->bindValue($stmt, $parameter, $value);
            }

            $stmt->execute();

            if($insert) {
                $object->id = Database::getConnection()->lastInsertId();
            }

            return true;
        } else {
            error_log("Trying to save " . get_class($object) . ", but table does not exist");
        }

        return false;
    }

    /**
     * Deletes an object from the database
     * @param GenericObject $object
     * @return bool
     */
    public function delete(GenericObject $object): bool {
        if($this->tableExists(get_class($object))) {
            $tableName = get_class($object);
            if($object->getId() !== null) {
                $query = $this->generateDeleteSql($object);
                $stmt = Database::getConnection()->prepare($query->getSql());

                foreach($query->getParameters() as $parameter => $value) {
                    $this->bindValue($stmt, $parameter, $value);
                }

                $stmt->execute();

                return true;
            } else {
                error_log("Trying to delete " . get_class($object) . ", but id is null");
            }
        } else {
            error_log("Trying to delete " . get_class($object) . ", but table does not exist");
        }

        return false;
    }

    /**
     * Returns an object from the database
     * The object will be returned as an instance of the corresponding class
     * @param array  $filter
     * @param string $orderBy
     * @param bool   $orderAsc
     * @param int    $offset
     * @return GenericObject|null
     */
    public function getObject(array $filter, string $orderBy = "id", bool $orderAsc = true, int $offset = 0): ?GenericObject {
        $objects = $this->getObjects($filter, $orderBy, $orderAsc, 1, $offset);
        if(count($objects) > 0) {
            return $objects[0];
        }

        return null;
    }

    /**
     * Returns multiple objects from the database at once
     * The objects will be returned as an array of instances of the corresponding class
     * @param array  $filter
     * @param string $orderBy
     * @param bool   $orderAsc
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getObjects(array $filter = [], string $orderBy = "id", bool $orderAsc = true, int $limit = -1, int $offset = 0): array {
        if($this->tableExists($this->CLASS_INSTANCE)) {
            $query = $this->generateQuerySql($filter, $orderBy, $orderAsc, $limit, $offset);
            $stmt = Database::getConnection()->prepare($query->getSql());

            foreach($query->getParameters() as $parameter => $value) {
                $this->bindValue($stmt, $parameter, $value);
            }

            $stmt->execute();

            $objects = [];
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $object = new $this->CLASS_INSTANCE();
                $object->fromArray($result);
                $objects[] = $object;
            }

            return $objects;
        } else {
            error_log("Trying to get " . $this->CLASS_INSTANCE . ", but table does not exist");
        }

        return [];
    }

    /**
     * Checks whether the table for the specified class exists
     * @param string $tableName
     * @return bool
     */
    public function tableExists(string $tableName): bool {
        $stmt = Database::getConnection()->prepare("SHOW TABLES LIKE :tableName");
        $stmt->bindValue(":tableName", $tableName);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Binds a value to a parameter in a prepared statement
     * @param PDOStatement $statement
     * @param string        $parameter
     * @param mixed         $value
     * @return void
     */
    public function bindValue(PDOStatement $statement, string $parameter, mixed $value): void {
        if($value instanceof DateTime || $value instanceof DateTimeImmutable) {
            $date = $value->format(DateTimeInterface::RFC3339_EXTENDED);
            $statement->bindValue(":{$parameter}", $date, PDO::PARAM_STR);
        } else if(is_bool($value)) {
            $statement->bindValue(":{$parameter}", $value, PDO::PARAM_BOOL);
        } else if(is_int($value)) {
            $statement->bindValue(":{$parameter}", $value, PDO::PARAM_INT);
        } else if(is_null($value)) {
            $statement->bindValue(":{$parameter}", $value, PDO::PARAM_NULL);
        } else {
            $statement->bindValue(":{$parameter}", $value, PDO::PARAM_STR);
        }
    }

    public function generateUpsertSql(GenericObject $object): Query {
        $objectProperties = get_object_vars($object);
        $insert = $object->getId() === null;

        $bindParameters = [];

        $sql = ($insert ? "INSERT INTO " : "UPDATE ") . "`{$this->CLASS_INSTANCE}` SET ";
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
            $bindParameters["id"] = $object->getId();
        }

        return new Query($sql, $bindParameters);
    }

    public function generateDeleteSql(GenericObject $object): Query {
        $sql = "DELETE FROM `{$this->CLASS_INSTANCE}` WHERE `id` = :id";
        $bindParameters = [
            "id" => $object->getId()
        ];

        return new Query($sql, $bindParameters);
    }

    public function generateQuerySql(array $filter = [], string $orderBy = "id", bool $orderAsc = true, int $limit = -1, int $offset = 0): Query {
        $bindParameters = [];

        // Base search
        $sql = "SELECT * FROM `" . $this->CLASS_INSTANCE . "`";

        // Filter
        if(count($filter) > 0) {
            $sql .= " WHERE ";
            foreach($filter as $key => $value) {
                if($value instanceof DAOFilter) {
                    $sql .= $value->generateSqlTerm($key) . " AND ";
                    foreach($value->values($key) as $property => $propertyValue) {
                        $bindParameters[$property] = $propertyValue;
                    }
                } else {
                    if($value === null) {
                        $sql .= "`{$key}` IS NULL AND ";
                    } else {
                        $sql .= "`{$key}` = :{$key} AND ";
                        $bindParameters[$key] = $value;
                    }
                }
            }
            $sql = substr($sql, 0, -5);
        }

        // Order
        $strippedOrderBy = strip_tags($orderBy);
        $sql .= " ORDER BY `{$strippedOrderBy}` " . ($orderAsc ? "ASC" : "DESC");

        // Limit and offset
        if($limit >= 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return new Query($sql, $bindParameters);
    }
}
