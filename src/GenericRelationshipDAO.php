<?php

namespace struktal\ORM;

use struktal\ORM\Database\Query;
use struktal\ORM\internal\GenericObject;
use struktal\ORM\internal\GenericObjectDAO;

abstract class GenericRelationshipDAO extends GenericObjectDAO {
    private function getRelationshipType(): RelationshipType {
        return call_user_func([$this->getClassInstance(), "getRelationshipType"]);
    }

    protected function createRelationshipObject(GenericEntity $producer, GenericEntity $consumer, array $additionalValues = []): GenericObject {
        $relationship = new ($this->getClassInstance())();
        $relationship->setProducer($producer);
        $relationship->setConsumer($consumer);
        return $relationship;
    }

    private function saveMultipleRelationships(GenericEntity $producer, array $consumers): bool {
        $success = true;
        $savedRelationships = [];
        foreach($consumers as $consumer) {
            $relationship = $this->createRelationshipObject($consumer, $producer);
            $success = $this->save($relationship);
            if(!$success) {
                break;
            } else {
                $savedRelationships[] = $relationship;
            }
        }

        if(!$success) {
            foreach($savedRelationships as $relationship) {
                $this->delete($relationship);
            }

            return false;
        }

        return true;
    }

    public function get(GenericEntity $producer): GenericEntity|array|null {
        $relationships = $this->getObjects([
            "producer" => $producer
        ]);
        $consumers = [];
        foreach($relationships as $relationship) {
            $consumers[] = $relationship->getConsumer();
        }
        $relationshipType = $this->getRelationshipType();
        if($relationshipType === RelationshipType::ONE_TO_ONE) {
            return $consumers[0] ?? null;
        } elseif($relationshipType === RelationshipType::ONE_TO_MANY) {
            return $consumers;
        }

        return null;
    }

    public function set(GenericEntity $producer, GenericEntity|array $consumer): bool {
        $relationshipType = $this->getRelationshipType();
        if($relationshipType === RelationshipType::ONE_TO_ONE && !$consumer instanceof GenericEntity) {
            if(is_array($consumer)) {
                throw new \InvalidArgumentException("Consumer must be a single GenericEntity for ONE_TO_ONE relationships");
            }

            $consumer = [ $consumer ];
        }

        $existingRelationships = $this->getObjects([
            "producer" => $producer
        ]);
        foreach($existingRelationships as $relationship) {
            $this->delete($relationship);
        }

        return $this->saveMultipleRelationships($producer, $consumer);
    }

    public function add(GenericEntity $producer, GenericEntity|array $consumer): bool {
        $relationshipType = $this->getRelationshipType();
        if($relationshipType === RelationshipType::ONE_TO_ONE) {
            throw new \InvalidArgumentException("Cannot add consumers to a ONE_TO_ONE relationship, use set() instead");
        }

        if($consumer instanceof GenericEntity) {
            $consumer = [ $consumer ];
        }

        return $this->saveMultipleRelationships($producer, $consumer);
    }

    public function remove(GenericEntity $producer, GenericEntity|array $consumer): bool {
        $relationshipType = $this->getRelationshipType();
        if($relationshipType === RelationshipType::ONE_TO_ONE) {
            throw new \InvalidArgumentException("Cannot remove consumers from a ONE_TO_ONE relationship, use delete() instead");
        }

        if($consumer instanceof GenericEntity) {
            $consumer = [ $consumer ];
        }

        $success = true;
        foreach($consumer as $entity) {
            $relationship = $this->getObject([
                "producer" => $producer,
                "consumer" => $entity
            ]);
            if($relationship instanceof GenericRelationship) {
                if(!$this->delete($relationship)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * Saves an object with its current attributes to the database
     * @param GenericObject $object
     * @return bool
     */
    public function save(GenericObject $object): bool {
        if(!$object instanceof GenericRelationship) {
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
        if(!$object instanceof GenericRelationship) {
            return false;
        }

        return parent::delete($object);
    }

    public function generateUpsertSql(GenericObject $object): Query {
        if(!$object instanceof GenericRelationship) {
            throw new \InvalidArgumentException("Object must be an instance of GenericRelationship");
        }

        $objectProperties = get_object_vars($object);

        $existingRelationship = $this->getObject($objectProperties);
        if($existingRelationship instanceof GenericObject) {
            return new Query("SELECT NULL LIMIT 0", []);
        }

        $insert = true;
        $relationshipType = $this->getRelationshipType();
        if($relationshipType === RelationshipType::ONE_TO_ONE) {
            $existingRelationship = $this->getObject([
                "producer" => $object->getProducer()
            ]);
            $insert = !$existingRelationship instanceof GenericRelationship;
        }

        $bindParameters = [];

        $sql = ($insert ? "INSERT INTO " : "UPDATE ") . "`{$this->getClassInstance()}` SET ";
        foreach($objectProperties as $property => $value) {
            if(!$insert && ($property === "producer")) {
                continue;
            }

            $sql .= "`{$property}` = :{$property}, ";
            $bindParameters[$property] = $value;
        }
        $sql = substr($sql, 0, -2);
        if(!$insert) {
            $sql .= " WHERE `producer` = :producer";
            $bindParameters["producer"] = $object->getProducer();
        }

        return new Query($sql, $bindParameters);
    }

    public function generateDeleteSql(GenericObject $object): Query {
        if(!$object instanceof GenericRelationship) {
            throw new \InvalidArgumentException("Object must be an instance of GenericRelationship");
        }

        $sql = "DELETE FROM `{$this->getClassInstance()}` WHERE `producer` = :producer AND `consumer` = :consumer";
        $bindParameters = [
            "producer" => $object->getProducer(),
            "consumer" => $object->getConsumer()
        ];

        return new Query($sql, $bindParameters);
    }
}
