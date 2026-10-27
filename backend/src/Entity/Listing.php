<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ListingRepository::class)]
class Listing
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const CONDITIONS = ['new' => 'new', 'like_new' => 'like_new', 'used' => 'used', 'old' => 'old'];

    public const PAYMENT_OPTIONS = [
        'cash' => 'cash', 'ideal' => 'ideal', 'bank_transfer' => 'bank_transfer', 'creditcard' => 'creditcard',
        'paypal' => 'paypal', 'tikkie' => 'tikkie', 'apple_google_pay' => 'apple_google_pay',
        'klarna' => 'klarna', 'bancontact' => 'bancontact', 'crypto' => 'crypto',
    ];

    public const DELIVERY_OPTIONS = [
        'pickup' => 'pickup', 'postnl' => 'postnl', 'dhl' => 'dhl', 'dpd' => 'dpd', 'ups' => 'ups',
        'gls' => 'gls', 'courier' => 'courier', 'seller_delivers' => 'seller_delivers',
    ];

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank, Assert\Length(max: 150)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $description = '';

    /** Price in euro cents. */
    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private int $priceCents = 0;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: self::CONDITIONS)]
    private string $itemCondition = 'used';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?City $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    #[Assert\Count(min: 1)]
    #[Assert\All([new Assert\Choice(choices: self::PAYMENT_OPTIONS)])]
    private array $paymentOptions = [];

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    #[Assert\Count(min: 1)]
    #[Assert\All([new Assert\Choice(choices: self::DELIVERY_OPTIONS)])]
    private array $deliveryOptions = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $v): static { $this->title = $v; return $this; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $v): static { $this->description = $v; return $this; }
    public function getPriceCents(): int { return $this->priceCents; }
    public function setPriceCents(int $v): static { $this->priceCents = $v; return $this; }
    public function getPrice(): float { return $this->priceCents / 100; }
    public function setPrice(?float $v): static { $this->priceCents = (int) round(($v ?? 0) * 100); return $this; }
    public function getItemCondition(): string { return $this->itemCondition; }
    public function setItemCondition(string $v): static { $this->itemCondition = $v; return $this; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $v): static { $this->category = $v; return $this; }
    public function getCity(): ?City { return $this->city; }
    public function setCity(?City $v): static { $this->city = $v; return $this; }
    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $v): static { $this->owner = $v; return $this; }
    public function getPaymentOptions(): array { return $this->paymentOptions; }
    public function setPaymentOptions(array $v): static { $this->paymentOptions = array_values($v); return $this; }
    public function getDeliveryOptions(): array { return $this->deliveryOptions; }
    public function setDeliveryOptions(array $v): static { $this->deliveryOptions = array_values($v); return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $v): static { $this->image = $v; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $v): static { $this->status = $v; return $this; }
    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function __toString(): string { return $this->title; }
}
