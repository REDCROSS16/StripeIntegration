<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 21.11.2023
 * Time: 11:41
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DateManagementTrait;
use App\Entity\Traits\SoftDeletableInterface;
use App\Entity\Traits\SoftDeletableTrait;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Payment
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Payment implements EntityInterface, SoftDeletableInterface
{
    use DateManagementTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'payment_id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?User $user = null;

    #[ORM\OneToOne(targetEntity: Invoice::class)]
    #[ORM\JoinColumn(name: 'invoice_id', referencedColumnName: 'invoice_id', unique: true, nullable: false)]
    private ?Invoice $invoice = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    private ?string $status = null;

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
     * @return Payment
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Invoice|null
     */
    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    /**
     * @param Invoice|null $invoice
     * @return Payment
     */
    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Payment
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
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
     * @return Payment
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
