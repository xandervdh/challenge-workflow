<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketsRepository::class)
 */
class Tickets
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $priority;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="tickets")
     */
    private $customer_id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="tickets")
     */
    private $assigned_to;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="ticket_id", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->customer_id = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?bool
    {
        return $this->priority;
    }

    public function setPriority(bool $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getCustomerId(): Collection
    {
        return $this->customer_id;
    }

    public function addCustomerId(Users $customerId): self
    {
        if (!$this->customer_id->contains($customerId)) {
            $this->customer_id[] = $customerId;
        }

        return $this;
    }

    public function removeCustomerId(Users $customerId): self
    {
        $this->customer_id->removeElement($customerId);

        return $this;
    }

    public function getAssignedTo(): ?Users
    {
        return $this->assigned_to;
    }

    public function setAssignedTo(?Users $assigned_to): self
    {
        $this->assigned_to = $assigned_to;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTicketId($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTicketId() === $this) {
                $comment->setTicketId(null);
            }
        }

        return $this;
    }
}
