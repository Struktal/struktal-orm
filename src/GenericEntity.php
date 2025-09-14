<?php

namespace struktal\ORM;

use \DateTimeImmutable;

abstract class GenericEntity extends internal\GenericObject {
    public ?int $id;
    public DateTimeImmutable $created;
    public DateTimeImmutable $updated;

    public function __construct() {
        $this->id = null;
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
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
