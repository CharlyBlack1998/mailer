<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @UniqueEntity("email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $surname = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $patronymic = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="json")
     */
    private ?array $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sender")
     */
    private Collection $sentMessages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="recipient")
     */
    private Collection $receivedMessages;

    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }

    public function addReceivedMessage(Message $message): self
    {
        if (!$this->getReceivedMessages()->contains($message)) {
            $this->getReceivedMessages()->add($message);
            $message->setRecipient($this);
        }

        return $this;
    }

    public function addSentMessage(Message $message): self
    {
        if (!$this->getSentMessages()->contains($message)) {
            $this->getSentMessages()->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $message): self
    {
        if ($this->getReceivedMessages()->contains($message)) {
            $this->getReceivedMessages()->removeElement($message);
            $message->setRecipient(null);
        }

        return $this;
    }

    public function removeSentMessage(Message $message): self
    {
        if ($this->getSentMessages()->contains($message)) {
            $this->getSentMessages()->removeElement($message);
            $message->setSender(null);
        }

        return $this;
    }

    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function setSentMessages(Collection $sentMessages): self
    {
        $this->clearSentMessages();
        foreach ($sentMessages as $sentMessage) {
            $this->addSentMessage($sentMessage);
        }

        return $this;
    }

    public function clearSentMessages(): self
    {
        /** @var Message $sentMessage */
        foreach ($this->getSentMessages() as $sentMessage) {
            $this->removeSentMessage($sentMessage);
        }

        return $this;
    }

    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function setReceivedMessages(Collection $receivedMessages): self
    {
        $this->clearReceivedMessages();
        foreach ($receivedMessages as $receivedMessage) {
            $this->addReceivedMessage($receivedMessage);
        }

        return $this;
    }

    public function clearReceivedMessages(): self
    {
        /** @var Message $receivedMessage */
        foreach ($this->getReceivedMessages() as $receivedMessage) {
            $this->removeReceivedMessage($receivedMessage);
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): self
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return $this->name;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->email;
    }
}
