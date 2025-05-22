<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Type("integer")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Serializer\Type("string")]
    private ?string $title = null;

    #[ORM\Column]
    #[Serializer\Type("integer")]
    private ?int $releaseYear = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Type("string")]
    private ?string $director = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Serializer\Type("string")]
    private ?string $cast = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Type("string")]
    private ?string $image = null;

    #[ORM\Column]
    #[Serializer\Type("integer")]
    private ?int $runningTime = null;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: Review::class, cascade: ['persist', 'remove'])]
    #[Serializer\Type("ArrayCollection<App\Entity\Review>")]
    #[Serializer\MaxDepth(1)]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    // Getters and setters for all fields with serializer annotations

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(int $releaseYear): self
    {
        $this->releaseYear = $releaseYear;
        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): self
    {
        $this->director = $director;
        return $this;
    }

    public function getCast(): ?string
    {
        return $this->cast;
    }

    public function setCast(?string $cast): self
    {
        $this->cast = $cast;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getRunningTime(): ?int
    {
        return $this->runningTime;
    }

    public function setRunningTime(int $runningTime): self
    {
        $this->runningTime = $runningTime;
        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setMovie($this);
        }
        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getMovie() === $this) {
                $review->setMovie(null);
            }
        }
        return $this;
    }
}
