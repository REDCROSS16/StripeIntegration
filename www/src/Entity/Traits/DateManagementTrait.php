<?php

/**
 * Created by PhpStorm.
 * User: zhuralex172
 * Date: 26.09.22
 * Time: 17:30
 * Project: ledums
 */

declare(strict_types=1);

namespace App\Entity;

use App\Service\DateTime\DateTimeWrapper;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @package App\Entity
 */
trait DateManagementTrait
{
    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @return void
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = DateTimeWrapper::getCurrentMoment();
        $this->updatedAt = DateTimeWrapper::getCurrentMoment(); //todo:
    }

    /**
     * @return void
     * @throws Exception
     */
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = DateTimeWrapper::getCurrentMoment(); // todo:
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return EntityInterface
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): EntityInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     * @return EntityInterface
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): EntityInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
