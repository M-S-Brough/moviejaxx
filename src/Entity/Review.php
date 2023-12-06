<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reviewText = null;

    #[ORM\OneToOne(mappedBy: 'review', cascade: ['persist', 'remove'])]
    private ?Movie $yes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getReviewText(): ?string
    {
        return $this->reviewText;
    }

    public function setReviewText(string $reviewText): static
    {
        $this->reviewText = $reviewText;

        return $this;
    }

    public function getYes(): ?Movie
    {
        return $this->yes;
    }

    public function setYes(?Movie $yes): static
    {
        // unset the owning side of the relation if necessary
        if ($yes === null && $this->yes !== null) {
            $this->yes->setReview(null);
        }

        // set the owning side of the relation if necessary
        if ($yes !== null && $yes->getReview() !== $this) {
            $yes->setReview($this);
        }

        $this->yes = $yes;

        return $this;
    }
}
