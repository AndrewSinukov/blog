<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail is already used")
 * @UniqueEntity(fields="username", message="This username is already used")
 */
class User implements AdvancedUserInterface, \Serializable
{
    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=8, max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=4, max=50)
     */
    private $fullName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MicroPost", mappedBy="likedBy")
     */
    private $postsLiked;

    /**
     * @var array
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MicroPost", mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="following")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="followers")
     * @ORM\JoinTable(name="following",
     *     joinColumns={
     *         @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")
     *     }
     * )
     */
    private $following;

    /**
     * @ORM\Column(type="string", nullable=true, length=30)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserPreferences", cascade={"persist"})
     */
    private $preferences;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->postsLiked = new ArrayCollection();
        $this->roles = [self::ROLE_USER];
        $this->enabled = false;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {

    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->enabled,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->password, $this->enabled] = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     * @return User
     */
    public function setFullName($fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @param mixed $username
     * @return User
     */
    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @return Collection
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    /**
     * @return Collection
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    /**
     * @param User $userToFollow
     */
    public function follow(User $userToFollow)
    {
        if ($this->getFollowing()->contains($userToFollow)) {
            return;
        }

        $this->getFollowing()->add($userToFollow);
    }

    /**
     * @return Collection
     */
    public function getPostsLiked(): Collection
    {
        return $this->postsLiked;
    }

    /**
     * @return mixed
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param mixed $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     * @return User
     */
    public function setEnabled($enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return UserPreferences|null
     */
    public function getPreferences(): ?UserPreferences
    {
        return $this->preferences;
    }

    /**
     * @param mixed $preferences
     * @return User
     */
    public function setPreferences($preferences): self
    {
        $this->preferences = $preferences;

        return $this;
    }
}