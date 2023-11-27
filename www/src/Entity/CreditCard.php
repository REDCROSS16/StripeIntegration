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

use App\Entity\Traits\DateManagementTrait;
use App\Entity\Traits\SoftDeletableInterface;
use App\Entity\Traits\SoftDeletableTrait;
use App\Repository\CreditCardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreditCard
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CreditCardRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ORM\UniqueConstraint(name: 'token', columns: ['token'])]
#[UniqueEntity(fields: ['token'], message: 'error.validation.phone.already_exists')]
#[ORM\Table('`card`')]
class CreditCard implements EntityInterface, SoftDeletableInterface
{
    use DateManagementTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'card_id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 180, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(name: 'pan', type: Types::STRING, length: 16, unique: true, nullable: false)]
    #[Assert\Length(min: 16, max: 16)]
    private ?string $pan = null;

    #[ORM\Column(name: 'expiration', type: Types::STRING, length: 4, nullable: false)]
    private ?string $expiration = null;

    #[ORM\Column(name: 'cvv', type: Types::STRING, length: 4, nullable: false)]
    private ?string $cvv = null;

    #[ORM\Column(name: 'token', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $token = null;

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

    /**
     * @return string|null
     */
    public function getPAN(): ?string
    {
        return $this->pan;
    }

    /**
     * @param string $pan
     * @return $this
     */
    public function setPan(string $pan): self
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

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return self
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
