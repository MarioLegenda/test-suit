<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 21.4.2015.
 * Time: 14:01
 */

namespace App\ToolsBundle\Tests\Mapper;


use App\ToolsBundle\Repositories\Query\Mapper\Mapper;
use App\ToolsBundle\Repositories\Query\Mapper\Maps\UserInfoMap;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    private $map = array(
        'name' => 'Mario',
        'lastname' => 'Škrlec',
        'username' => 'whitepostmail@gmail.com',
        'fields' => '',
        'programming_languages' => '',
        'tools' => '',
        'years_of_experience' => '',
        'future_plans' => '',
        'description' => ''
    );

    public function testMapper() {
        $mapper = new Mapper();
        $mapper->attach(new UserInfoMap($this->map));
        $mapper->notify();

        var_dump($mapper->offsetGetMapped(0));
        die();
    }
} 