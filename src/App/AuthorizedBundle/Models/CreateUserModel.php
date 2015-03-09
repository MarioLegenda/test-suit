<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 2.2.2015.
 * Time: 22:06
 */

namespace App\AuthorizedBundle\Models;


use App\AuthorizedBundle\Models\Contracts\GenericModelInterface;
use App\ToolsBundle\Models\GenericModel;

class CreateUserModel
{
    private $userData = array();

    public function __construct(array $userFromRequest) {
        $this->userData = $userFromRequest;
    }

    public function areValidKeys() {
        $validKeys = array(
            'userPermissions',
            'name',
            'lastname',
            'username',
            'userPassword',
            'userPassRepeat',
            'years_of_experience',
            'fields',
            'programming_languages',
            'tools',
            'years_of_experience',
            'future_plans',
            'description',
        );

        foreach($validKeys as $key) {
            if( ! array_key_exists($key, $this->userData)) {
                return false;
            }
        }

        return true;
    }
} 