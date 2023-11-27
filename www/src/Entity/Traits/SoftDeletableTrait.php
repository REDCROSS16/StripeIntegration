<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package App\Entity
 */
trait SoftDeletableTrait
{
    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, nullable: false, options: ['unsigned' => true, 'default' => 1])]
    private bool $isActive = false;

    #[ORM\Column(name: 'is_deleted', type: Types::BOOLEAN, nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private bool $isDeleted = false;

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return EntityInterface
     */
    public function setIsActive(bool $isActive): EntityInterface
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     * @return EntityInterface
     */
    public function setIsDeleted(bool $isDeleted): EntityInterface
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
