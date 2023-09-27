<?php
namespace NeosRulez\Neos\PasswordReset\Domain\Model;

/*
 * This file is part of the NeosRulez.Neos.PasswordReset package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Token
{

    /**
     * @var array
     * @Flow\Inject
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     */
    protected $token;

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @var bool
     */
    protected $valid = true;

    /**
     * @return bool
     */
    public function isValid()
    {
        $created = $this->created;

        $now = new \DateTime();

        $origin = new \DateTimeImmutable($created->format('d.m.Y H:i:s'));
        $target = new \DateTimeImmutable($now->format('d.m.Y H:i:s'));

        $interval = $origin->diff($target);

        $minutes = $interval->i;
        if($minutes < $this->settings['tokenLifetime'] && $this->valid) {
            return true;
        }
        return false;
    }

    /**
     * @param bool $valid
     * @return void
     */
    public function setInvalid(bool $valid = false)
    {
        $this->valid = $valid;
    }

    /**
     * @var \Neos\Neos\Domain\Model\User
     * @ORM\ManyToOne(cascade={"persist"})
     * @ORM\Column(unique=false)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $user;

    /**
     * @return \Neos\Neos\Domain\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Neos\Neos\Domain\Model\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @var \DateTime
     */
    protected $created;


    public function __construct() {
        $this->created = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

}
