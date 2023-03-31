<?php

namespace App\Entity;

use App\Repository\OfficeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfficeRepository::class)]
class Office
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $roomSize = null;

    #[ORM\Column]
    private ?int $deskSize = null;

    #[ORM\Column]
    private ?bool $canLeaveBelongings = null;

    #[ORM\Column(length: 20)]
    private ?string $brightness = null;

    #[ORM\Column(length: 50)]
    private ?string $networkQuality = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(length: 20)]
    private ?string $internetType = null;


    #[ORM\ManyToOne(inversedBy: 'offices')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\OneToMany(mappedBy: 'office', targetEntity: User::class)]
    private Collection $occupant;

    #[ORM\Column, ORM\JoinColumn(nullable: true)]
    private array $imageFilenames = [];

    #[ORM\OneToMany(mappedBy: 'office', targetEntity: Bookings::class)]
    private Collection $bookings;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: Kitchens::class, inversedBy: 'offices')]
    private Collection $kitchens;

    #[ORM\ManyToMany(targetEntity: Equipments::class, inversedBy: 'offices')]
    private Collection $equipments;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $location = null;

    public function __construct()
    {
        $this->occupant = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->kitchens = new ArrayCollection();
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomSize(): ?int
    {
        return $this->roomSize;
    }

    public function setRoomSize(int $roomSize): self
    {
        $this->roomSize = $roomSize;

        return $this;
    }

    public function getDeskSize(): ?int
    {
        return $this->deskSize;
    }

    public function setDeskSize(int $deskSize): self
    {
        $this->deskSize = $deskSize;

        return $this;
    }

    public function isCanLeaveBelongings(): ?bool
    {
        return $this->canLeaveBelongings;
    }

    public function setCanLeaveBelongings(bool $canLeaveBelongings): self
    {
        $this->canLeaveBelongings = $canLeaveBelongings;

        return $this;
    }

    public function getBrightness(): ?string
    {
        return $this->brightness;
    }

    public function setBrightness(string $brightness): self
    {
        $this->brightness = $brightness;

        return $this;
    }

    public function getNetworkQuality(): ?string
    {
        return $this->networkQuality;
    }

    public function setNetworkQuality(string $networkQuality): self
    {
        $this->networkQuality = $networkQuality;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getInternetType(): ?string
    {
        return $this->internetType;
    }

    public function setInternetType(string $internetType): self
    {
        $this->internetType = $internetType;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOccupant(): Collection
    {
        return $this->occupant;
    }

    public function addOccupant(User $occupant): self
    {
        if (!$this->occupant->contains($occupant)) {
            $this->occupant->add($occupant);
            $occupant->setOffice($this);
        }

        return $this;
    }

    public function removeOccupant(User $occupant): self
    {
        if ($this->occupant->removeElement($occupant)) {
            // set the owning side to null (unless already changed)
            if ($occupant->getOffice() === $this) {
                $occupant->setOffice(null);
            }
        }

        return $this;
    }

    public function getImageFilenames(): array
    {
        return $this->imageFilenames;
    }

    public function setImageFilenames(array $imageFilenames): self
    {
        $this->imageFilenames = $imageFilenames;

        return $this;
    }

    /**
     * @return Collection<int, Bookings>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Bookings $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setOffice($this);
        }

        return $this;
    }

    public function removeBooking(Bookings $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getOffice() === $this) {
                $booking->setOffice(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Kitchens>
     */
    public function getKitchens(): Collection
    {
        return $this->kitchens;
    }

    public function addKitchen(Kitchens $kitchen): self
    {
        if (!$this->kitchens->contains($kitchen)) {
            $this->kitchens->add($kitchen);
        }

        return $this;
    }

    public function removeKitchen(Kitchens $kitchen): self
    {
        $this->kitchens->removeElement($kitchen);

        return $this;
    }

    /**
     * @return Collection<int, Equipments>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipments $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipments $equipment): self
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }

    /*
    public function getLocation(): ?Address
    {
        return $this->location;
    }

    public function setLocation(?Address $location): self
    {
        $this->location = $location;

        return $this;
    }
    */
    public function getLocation(): ?Address
    {
        return $this->location;
    }

    public function setLocation(?Address $location): self
    {
        $this->location = $location;

        return $this;
    }
}
