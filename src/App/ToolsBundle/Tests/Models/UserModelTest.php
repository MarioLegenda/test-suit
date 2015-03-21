<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 21.3.2015.
 * Time: 10:56
 */

namespace App\ToolsBundle\Tests\Models;


use App\AuthorizedBundle\Models\UserModel;

class UserModelTest extends \PHPUnit_Framework_TestCase
{
    private $content = array(
        'personal' => array(

        ),
        'username' => array(
            'filterType' => 'username-filter',
            'key' => 'username',
            'username' => 'whitepostmail@gmail.com'
        )
    );

    public function testIfValid() {
        $model = new UserModel();
        $content = $this->content['username'];

        $isValid = $model->requestContentMode($content)->extractType()->isContentValid();

        $this->assertTrue($isValid, 'testIfValid(): UserModel::isContentValid() has to return true');
    }
} 