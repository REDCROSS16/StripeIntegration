<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DateManagementTrait;
use App\Entity\Traits\SoftDeletableInterface;
use App\Entity\Traits\SoftDeletableTrait;
use App\ENUM\InvoiceStatus;
use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'payments', targetEntity: Payment::class, cascade: ['persist'], orphanRemoval: true)]
    private ?Payment $payment = null;

    #[ORM\Column(name: 'order', type: Types::STRING, length: 255, nullable: true)]
    private ?string $order = null;

    #[ORM\Column(name: 'amount', type: Types::DECIMAL, precision: 20, scale: 2, nullable: false, options: ['unsigned' => true])]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?float $amount = null;

    #[ORM\Column(name: 'status', type: Types::SMALLINT, nullable: false, enumType: InvoiceStatus::class, options: ['unsigned' => true])]
    private ?InvoiceStatus $status = null;

    #[ORM\Column(name: 'data', type: Types::JSON, nullable: false)]
    private ?array $data = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

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
     * @return InvoiceStatus|null
     */
    public function getStatus(): ?InvoiceStatus
    {
        return $this->status;
    }

    /**
     * @param InvoiceStatus $status
     * @return Invoice
     */
    public function setStatus(InvoiceStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @return void
     */
    public function setData(?array $data):void
    {
        $this->data = $data;
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
}
