<?php

namespace App\Entity;

use App\Repository\BanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BanRepository::class)
 */
class Ban
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="bans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idPlayer;

    /**
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="bans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idServer;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $idOperator;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPerma;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagePassword;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isApplied;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fg_deleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_unbanned;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ban")
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPlayer(): ?Player
    {
        return $this->idPlayer;
    }

    public function setIdPlayer(?Player $idPlayer): self
    {
        $this->idPlayer = $idPlayer;

        return $this;
    }

    public function getIdServer(): ?Server
    {
        return $this->idServer;
    }

    public function setIdServer(?Server $idServer): self
    {
        $this->idServer = $idServer;

        return $this;
    }

    public function getIdOperator(): ?Player
    {
        return $this->idOperator;
    }

    public function setIdOperator(?Player $idOperator): self
    {
        $this->idOperator = $idOperator;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getIsPerma(): ?bool
    {
        return $this->isPerma;
    }

    public function setIsPerma(?bool $isPerma): self
    {
        $this->isPerma = $isPerma;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImagePassword(): ?string
    {
        return $this->imagePassword;
    }

    public function setImagePassword(?string $imagePassword): self
    {
        $this->imagePassword = $imagePassword;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getIsApplied(): ?bool
    {
        return $this->isApplied;
    }

    public function setIsApplied(bool $isApplied): self
    {
        $this->isApplied = $isApplied;

        return $this;
    }

    public function getFgDeleted(): ?bool
    {
        return $this->fg_deleted;
    }

    public function setFgDeleted(?bool $fg_deleted): self
    {
        $this->fg_deleted = $fg_deleted;

        return $this;
    }

    public function getIsUnbanned(): ?bool
    {
        return $this->is_unbanned;
    }

    public function setIsUnbanned(?bool $is_unbanned): self
    {
        $this->is_unbanned = $is_unbanned;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setBan($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBan() === $this) {
                $image->setBan(null);
            }
        }

        return $this;
    }
}
