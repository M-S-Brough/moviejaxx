<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $releaseYear = null;

    #[ORM\Column(length: 255)]
    private ?string $director = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cast = null;


    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $runningTime = null;

    #[ORM\OneToOne(inversedBy: 'yes', cascade: ['persist', 'remove'])]
    private ?Review $review = null;

    // Non-persisted field for handling new review data from the form
    private $newReview;

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

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    // Getter and setter for the newReview field
    public function getNewReview(): ?Review
    {
        return $this->newReview;
    }

    public function setNewReview(?Review $newReview): self
    {
        $this->newReview = $newReview;

        return $this;
    }
}
