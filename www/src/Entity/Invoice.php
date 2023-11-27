<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DateManagementTrait;
use App\Entity\Traits\SoftDeletableInterface;
use App\Entity\Traits\SoftDeletableTrait;
use App\Repository\InvoiceRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Invoice
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Invoice implements EntityInterface, SoftDeletableInterface
{
    use DateManagementTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'invoice_id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?User $user = null;

    #[ORM\OneToOne(targetEntity: Payment::class)]
    #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'payment_id', unique: true, nullable: false)]
    private ?Payment $payment = null;

    #[ORM\Column(name: 'order_id', type: Types::STRING, length: 255, nullable: false)]
    private ?string $order = null;

    #[ORM\Column(name: 'amount', type: Types::DECIMAL, precision: 20, scale: 2, nullable: false, options: ['unsigned' => true])]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?float $amount = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 255, nullable: false)]
    private ?string $status = null;

    #[ORM\Column(name: 'signature', type: Types::STRING, length: 255, nullable: false)]
    private ?string $signature = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(targetEntity: CreditCard::class)]
    #[ORM\JoinColumn(name: 'card_id', referencedColumnName: 'card_id', unique: true, nullable: false)]
    private ?CreditCard $card = null;

    #[ORM\Column(name: 'is_bind', type: Types::BOOLEAN, nullable: false, options: ['unsigned' => true, 'default' => 1])]
    private ?bool $isBind = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment|null $payment
     * @return $this
     */
    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrder(): ?string
    {
        return $this->order;
    }

    /**
     * @param string|null $order
     * @return $this
     */
    public function setOrder(?string $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float|null $amount
     * @return $this
     */
    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Invoice
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param string|null $signature
     * @return void
     */
    public function setSignature(?string $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return CreditCard|null
     */
    public function getCard(): ?CreditCard
    {
        return $this->card;
    }

    /**
     * @param CreditCard|null $card
     * @return Invoice
     */
    public function setCard(?CreditCard $card): self
    {
        $this->card = $card;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsBind(): ?bool
    {
        return $this->isBind;
    }

    /**
     * @param bool|null $isBind
     * @return Invoice
     */
    public function setIsBind(?bool $isBind): self
    {
        $this->isBind = $isBind;

        return $this;
    }
}
