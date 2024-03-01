<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['conversation:read-all'])]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'conversations')]
    private Collection $profile;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'conversation')]
    #[Groups(['conversation:read-all'])]
    private Collection $message;

    public function __construct()
    {
        $this->profile = new ArrayCollection();
        $this->message = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getProfile(): Collection
    {
        return $this->profile;
    }

    public function addProfile(Profile $profile): static
    {
        if (!$this->profile->contains($profile)) {
            $this->profile->add($profile);
        }

        return $this;
    }

    public function removeProfile(Profile $profile): static
    {
        $this->profile->removeElement($profile);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessage(): Collection
    {
        return $this->message;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->message->contains($message)) {
            $this->message->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->message->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }
}
