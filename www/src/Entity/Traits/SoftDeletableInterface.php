<?php

namespace App\Entity\Traits;

use App\Entity\EntityInterface;

/**
 * @package App\Entity
 */
interface SoftDeletableInterface extends EntityInterface
{
    /**
     * @param bool $isActive
     * @return EntityInterface
     */
    public function setIsActive(bool $isActive): EntityInterface;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isDeleted
     * @return EntityInterface
     */
    public function setIsDeleted(bool $isDeleted): EntityInterface;

    /**
     * @return bool
     */
    public function getIsDeleted(): bool;
}

