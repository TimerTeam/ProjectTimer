<?php

namespace App\Entity;

use App\Repository\TimerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimerRepository::class)
 */
class Timer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Project::class, cascade={"persist", "remove"})
     */
    private $project;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $horodateDebut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $horodateFin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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

    public function getHorodateDebut(): ?string
    {
        return $this->horodateDebut;
    }

    public function setHorodateDebut(string $horodateDebut): self
    {
        $this->horodateDebut = $horodateDebut;

        return $this;
    }

    public function getHorodateFin(): ?string
    {
        return $this->horodateFin;
    }

    public function setHorodateFin(string $horodateFin): self
    {
        $this->horodateFin = $horodateFin;

        return $this;
    }
}
