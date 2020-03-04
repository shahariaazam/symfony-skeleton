<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    use UuidTraits;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=36, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_tos_accepted = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profile_picture = 'http://lorempixel.com/800/800/abstract';

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $timezone = 'UTC';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_logged_in_at;

    /**
     * @Gedmo\Slug(fields={"first_name", "last_name"})
     *
     * @ORM\Column(type="string", length=80, unique=true)
     */
    private $user_slug;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // $this->password = null;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getIsTosAccepted(): ?bool
    {
        return $this->is_tos_accepted;
    }

    public function setIsTosAccepted(bool $is_tos_accepted): self
    {
        $this->is_tos_accepted = $is_tos_accepted;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLastLoggedInAt(): ?\DateTimeInterface
    {
        return $this->last_logged_in_at;
    }

    public function setLastLoggedInAt(?\DateTimeInterface $last_logged_in_at): self
    {
        $this->last_logged_in_at = $last_logged_in_at;

        return $this;
    }

    public function getUserSlug(): ?string
    {
        return $this->user_slug;
    }

    public function setUserSlug(string $user_slug): self
    {
        $this->user_slug = $user_slug;

        return $this;
    }
}
