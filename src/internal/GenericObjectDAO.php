<?php

namespace struktal\ORM\internal;

use struktal\DatabaseObjects\ORMEnumObject;
use struktal\ORM\Database\Database;
use struktal\ORM\Database\Query;
use struktal\ORM\GenericEntity;
use struktal\ORM\ORMEnum;
use \PDO;
use \PDOStatement;

use \DateTime;
use \DateTimeImmutable;
use \DateTimeInterface;

abstract class GenericObjectDAO {
    private string $CLASS_INSTANCE = "";

    public function __construct($CLASS_INSTANCE) {
        $this->CLASS_INSTANCE = $CLASS_INSTANCE;
    }

    protected function getClassInstance(): string {
        return $this->CLASS_INSTANCE;
    }

    /**
     * Saves an object with its current attributes to the database
     * @param GenericObject $object
     * @return bool
     */
    public function save(GenericObject $object): bool {
        if($this->tableExists($this->getClassInstance())) {
            $query = $this->generateUpsertSql($object);
            $stmt = Database::getConnection()->prepare($query->getSql());

            foreach($query->getParameters() as $parameter => $value) {
                $this->bindValue($stmt, $parameter, $value);
            }

            $stmt->execute();

            return true;
        } else {
            trigger_error("Trying to save " . get_class($object) . ", but table does not exist", E_USER_WARNING);
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
            $query = $this->generateDeleteSql($object);
            $stmt = Database::getConnection()->prepare($query->getSql());

            foreach($query->getParameters() as $parameter => $value) {
                $this->bindValue($stmt, $parameter, $value);
            }

            $stmt->execute();

            return true;
        } else {
            trigger_error("Trying to delete " . get_class($object) . ", but table does not exist", E_USER_WARNING);
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
        if($this->tableExists($this->getClassInstance())) {
            $query = $this->generateQuerySql($filter, $orderBy, $orderAsc, $limit, $offset);
            $stmt = Database::getConnection()->prepare($query->getSql());

            foreach($query->getParameters() as $parameter => $value) {
                $this->bindValue($stmt, $parameter, $value);
            }

            $stmt->execute();

            $objects = [];
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $object = new ($this->getClassInstance())();
                $object->fromArray($result);
                $objects[] = $object;
            }

            return $objects;
        } else {
            trigger_error("Trying to get " . $this->getClassInstance() . ", but table does not exist", E_USER_WARNING);
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
        $tableName = str_replace("\\", "\\\\", $tableName); // Escape backslashes from namespaced class names
        $stmt->bindValue(":tableName", $tableName);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Binds a value to a parameter in a prepared statement
     * @param PDOStatement $statement
     * @param string       $parameter
     * @param mixed        $value
     * @return void
     */
    public function bindValue(PDOStatement $statement, string $parameter, mixed $value): void {
        if($value instanceof GenericEntity) {
            $statement->bindValue(":{$parameter}", $value->id, PDO::PARAM_INT);
        } else if($value instanceof ORMEnumObject) {
            $statement->bindValue(":{$parameter}", $value->value, PDO::PARAM_INT);
        } else if($value instanceof ORMEnum) {
            $statement->bindValue(":{$parameter}", $value->value, PDO::PARAM_INT);
        } else if($value instanceof DateTime || $value instanceof DateTimeImmutable) {
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

    public abstract function generateUpsertSql(GenericObject $object): Query;

    public abstract function generateDeleteSql(GenericObject $object): Query;

    public function generateQuerySql(array $filter = [], string $orderBy = "id", bool $orderAsc = true, int $limit = -1, int $offset = 0): Query {
        $bindParameters = [];

        // Base search
        $sql = "SELECT * FROM `{$this->getClassInstance()}`";

        // Filter
        if(count($filter) > 0) {
            $sql .= " WHERE ";
            foreach($filter as $key => $value) {
                if($value instanceof \struktal\ORM\DAOFilter) {
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
