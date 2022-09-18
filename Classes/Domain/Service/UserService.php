<?php
namespace NeosRulez\Neos\PasswordReset\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

use Neos\Neos\Domain\Model\User as User;

/**
 *
 * @Flow\Scope("singleton")
 */
class UserService extends \Neos\Neos\Domain\Service\UserService {

    /**
     * @param int $length
     * @return string
     */
    public function generatePassword(int $length = 8):string
    {
        $chars = '23456789bcdfhkmnprstvzBCDFHJKLMNPRSTVZ';
        $shuffled = str_shuffle($chars);
        $result = mb_substr($shuffled, 0, $length);
        return $result;
    }

}
