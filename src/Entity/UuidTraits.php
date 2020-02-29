<?php
/**
 * UuidTraits class
 *
 * @package  App\Entity
 */


namespace App\Entity;

trait UuidTraits
{
    /**
     * @ORM\Column(type="string", length=36)
     */
    private $uuid;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setUuidValue(): void
    {
        $this->uuid = uuid_create(UUID_TYPE_DCE);
    }
}