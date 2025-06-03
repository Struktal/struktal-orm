<?php

namespace jensostertag\DatabaseObjects;

enum DAOFilterOperator {
    case EQUALS;
    case NOT_EQUALS;
    case GREATER_THAN;
    case LESS_THAN;
    case GREATER_THAN_EQUALS;
    case LESS_THAN_EQUALS;
    case LIKE;
    case IN;
    case NOT_IN;

    function generateSqlTerm(mixed $index, string $property, mixed $value): string {
        $sql = "";
        switch($this) {
            case self::EQUALS:
                if($value === null) {
                    $sql .= "`{$property}` IS NULL";
                } else {
                    $sql .= "`{$property}` = :{$index}";
                }
                break;
            case self::NOT_EQUALS:
                if($value === null) {
                    $sql .= "`{$property}` IS NOT NULL";
                } else {
                    $sql .= "`{$property}` != :{$index}";
                }
                break;
            case self::GREATER_THAN:
                $sql = "`{$property}` > :{$index}";
                break;
            case self::LESS_THAN:
                $sql = "`{$property}` < :{$index}";
                break;
            case self::GREATER_THAN_EQUALS:
                $sql = "`{$property}` >= :{$index}";
                break;
            case self::LESS_THAN_EQUALS:
                $sql = "`{$property}` <= :{$index}";
                break;
            case self::LIKE:
                $sql = "`{$property}` LIKE :{$index}";
                break;
            case self::IN:
            case self::NOT_IN:
                if(is_array($value)) {
                    $inList = "";
                    $includesNull = false;
                    foreach($value as $arrayKey => $arrayValue) {
                        if($arrayValue !== null) {
                            $inList .= ":{$index}_{$arrayKey}, ";
                        } else {
                            $includesNull = true;
                        }
                    }
                    if(strlen($inList) > 0) {
                        $inList = substr($inList, 0, -2);
                    }

                    if($inList !== "") {
                        if($includesNull) {
                            $sql .= "(`{$property}` IS " . ($this === self::NOT_IN ? "NOT " : "") . "NULL " . ($this === self::NOT_IN ? "AND " : "OR ");
                        }
                        $sql .= "`{$property}` " . ($this === self::NOT_IN ? "NOT " : "") . "IN (";
                        $sql .= $inList;
                        $sql .= ")";
                        if($includesNull) {
                            $sql .= ")";
                        }
                    } else if($includesNull) {
                        $sql .= "`{$property}` IS " . ($this === self::NOT_IN ? "NOT " : "") . "NULL";
                    } else {
                        if($this === self::IN) {
                            $sql .= "1 = 0"; // All values have to be in empty array, always false
                        } else if($this === self::NOT_IN) {
                            $sql .= "1 = 1"; // All values must not be in empty array, always true
                        }
                    }
                } else if($value === null) {
                    $sql .= "`{$property}` IS " . ($this === self::NOT_IN ? "NOT " : "") . "NULL";
                } else {
                    $sql .= "`{$property}` " . ($this === self::NOT_IN ? "NOT " : "") . "IN (:{$index})";
                }
                break;
        }

        return $sql;
    }

    function values(mixed $index, string $property, mixed $value): array {
        switch($this) {
            case self::EQUALS:
            case self::NOT_EQUALS:
            case self::GREATER_THAN:
            case self::LESS_THAN:
            case self::GREATER_THAN_EQUALS:
            case self::LESS_THAN_EQUALS:
            case self::LIKE:
                if($value !== null) {
                    return [
                        $index => $value
                    ];
                }
                break;
            case self::IN:
            case self::NOT_IN:
                if(is_array($value)) {
                    $values = [];
                    foreach($value as $arrayKey => $arrayValue) {
                        if($arrayValue !== null) {
                            $values[$index . "_" . $arrayKey] = $arrayValue;
                        }
                    }

                    return $values;
                } else if($value === null) {
                    return [];
                } else {
                    return [
                        $property => $value
                    ];
                }
                break;
        }

        return [];
    }
}
