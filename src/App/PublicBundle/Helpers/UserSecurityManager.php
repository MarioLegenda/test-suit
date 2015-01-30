<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 27.1.2015.
 * Time: 23:27
 */

namespace App\PublicBundle\Helpers;


use App\PublicBundle\Helpers\Exceptions\ModelException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSecurityManager
{
    private static $objectContext;

    private $encoder;

    public static function initEncoder($encoder) {
        if(!self::$objectContext instanceof UserSecurityManager) {
            self::$objectContext = new UserSecurityManager($encoder);
        }

        return self::$objectContext;
    }

    private function __construct($encoder) {
        $this->encoder = $encoder;
    }

    public static function encodePassword(UserInterface $user) {
        $password = $user->getPassword();
        if(empty($password) OR $password === null OR ! is_string($password)) {
            throw new ModelException("UserSecurityManager: password on the user object is null or not set to a string value");
        }

        return self::$objectContext->encoder->encodePassword($user, $user->getPassword());
    }
} 