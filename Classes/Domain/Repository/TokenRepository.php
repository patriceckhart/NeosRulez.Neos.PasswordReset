<?php
namespace NeosRulez\Neos\PasswordReset\Domain\Repository;

/*
 * This file is part of the NeosRulez.Neos.PasswordReset package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;
use Neos\Flow\Utility\Algorithms;

/**
 * @Flow\Scope("singleton")
 */
class TokenRepository extends Repository
{

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @param \Neos\Neos\Domain\Model\User $user
     * @return string
     */
    public function persist(\Neos\Neos\Domain\Model\User $user):string
    {
        $token = Algorithms::generateRandomToken(16);
        $newToken = new \NeosRulez\Neos\PasswordReset\Domain\Model\Token();
        $newToken->setToken($token);
        $newToken->setUser($user);
        $this->add($newToken);
        $this->persistenceManager->persistAll();
        return $token;
    }

    /**
     * @param \NeosRulez\Neos\PasswordReset\Domain\Model\Token $token
     * @return void
     */
    public function setInvalid(\NeosRulez\Neos\PasswordReset\Domain\Model\Token $token):void
    {
        $token->setInvalid();
        $this->update($token);
        $this->persistenceManager->persistAll();
    }

}
