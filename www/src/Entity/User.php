<?php

namespace App\Entity;

use App\ENUM\Roles;
use App\Entity\Traits\DateManagementTrait;
use App\Entity\Traits\SoftDeletableInterface;
use App\Entity\Traits\SoftDeletableTrait;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface, SoftDeletableInterface
{
    use DateManagementTrait;
    use SoftDeletableTrait;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'user_id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 180, unique: true, nullable: true)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    #[Assert\NotBlank(message: 'error.validation.field.required')]
    #[Assert\Email(message: 'error.validation.email.incorrect', mode: 'html5')]
    #[Assert\Regex(pattern: '/^.*@[a-z]{2,10}\.[a-z]{2,}$/', message: 'error.validation.email.incorrect')]
    private ?string $email = null;

    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 50, nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    #[Assert\NotBlank(message: 'error.validation.field.required')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'error.validation.field.length.min', maxMessage: 'error.validation.field.length.max')]
    private ?string $firstName = null;

    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 50, nullable: false)]
    #[Assert\NotNull(message: 'error.validation.field.required')]
    #[Assert\NotBlank(message: 'error.validation.field.required')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'error.validation.field.length.min', maxMessage: 'error.validation.field.length.max')]
    private ?string $lastName = null;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 20, unique: true, nullable: true)]
    #[Assert\Regex('/(\+)[0-9]{7,}$/')]
    private ?string $phone = null;

    #[ORM\Column(name: 'roles', type: Types::JSON, nullable: false)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private array $roles = [Roles::ROLE_USER];

    #[ORM\Column(name: 'password', type: Types::STRING, nullable: false)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CreditCard::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $cards;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Invoice::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $invoices;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?: $this->phone;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return void
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = array_unique(array_merge($roles, ['ROLE_USER']));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, CreditCard>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    /**
     * @param CreditCard $card
     * @return $this
     */
    public function addCard(CreditCard $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * @param Payment $payment
     * @return $this
     */
    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->cards->add($payment);
            $payment->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->cards->add($invoice);
            $invoice->setUser($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function eraseCredentials(): self
    {
        return $this;
    }
}
