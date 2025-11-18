<?php

namespace struktal\ORM;

abstract class GenericRelationship extends internal\GenericObject {
    protected static RelationshipType $relationshipType = RelationshipType::ONE_TO_ONE;
    public static function getRelationshipType(): RelationshipType {
        return static::$relationshipType;
    }

    public abstract function getProducer(): GenericEntity;
    public abstract function setProducer(GenericEntity $producer): void;
    public abstract function getConsumer(): GenericEntity;
    public abstract function setConsumer(GenericEntity $consumer): void;
}
