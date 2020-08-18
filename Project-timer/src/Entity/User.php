<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="cet user existe déjà"
 * )
 */

class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(name="email", type="string", unique=true)
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;


    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class)
     */
    private $teams;


    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->teams = new ArrayCollection();
        //$this->ListGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }
    public function setTeams(Group $teams)
    {
        if(!$this->teams->contains($teams)){
            $this->teams[] = $teams;
            $teams->setUser($this);
        }
    }

    public function addTeam(Group $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setUser($this);
        }

        return $this;
    }

    public function removeTeam(Group $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getUser() === $this) {
                $team->setUser(null);
            }
        }

        return $this;
    }
    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }



    
}
