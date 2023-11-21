<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 21.11.2023
 * Time: 12:44
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ENUM\CardStatus;
use App\Traits\Entity\DateManagementTrait;
use Doctrine\DBAL\Types\Types;

/**
 * Class CreditCard
 * @package App\Entity
 */
class CreditCard implements EntityInterface
{
    use DateManagementTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'card_id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?User $user = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 180, nullable: false)]
    protected ?string $name = null;

    #[ORM\Column(name: 'pan', type: Types::INTEGER, length: 16, unique: true, nullable: false)]
    protected ?int $pan = null;  //todo:create PAN

    #[ORM\Column(name: 'expiration', type: Types::STRING, length: 4, nullable: false)]
    protected ?string $expiration = null; // Срок действия карты format: yymm

    #[ORM\Column(name: 'cvv', type: Types::STRING, length: 4, nullable: false)]
    protected ?string $cvv = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 255, nullable: false)]
    protected string $status;

    #[ORM\Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    protected string $token;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getPAN(): ?string
    {
        return $this->pan;
    }

//    public function getPANNumber(): string
//    {
//        if (!isset($this->pan)) {
//            return '';
//        }
//
//        return $this->getPAN()->getNumber();
//    }

    public function setPan(int $pan): self
    {
        $this->pan = $pan;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpiration(): string
    {
        return $this->expiration;
    }

    /**
     * @param string $expiration
     * @return void
     */
    public function setExpiration(string $expiration): void
    {
        $this->expiration = $expiration;
    }

    /**
     * @return string
     */
    public function getCvv(): string
    {
        return $this->cvv;
    }

    /**
     * @param string $cvv
     * @return void
     */
    public function setCvv(string $cvv): void
    {
        $this->cvv = $cvv;
    }


}