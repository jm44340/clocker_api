<?php

namespace App\Entity;

use App\Repository\EntriesRepository;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Users;
use App\Repository\UsersRepository;

use App\Entity\Projects;
use App\Repository\UsersProjects;


/**
 * @ORM\Entity(repositoryClass=EntriesRepository::class)
 */
class Entries
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=projects::class, inversedBy="entries")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=users::class, inversedBy="entries")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?projects
    {
        return $this->project;
    }

    public function setProject(?projects $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getUser(): ?users
    {
        return $this->user;
    }

    public function setUser(?users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
