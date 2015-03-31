<?php

namespace App\ToolsBundle\Tests\CommandPattern;


use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandFactory;

class CommandPatternTest extends \PHPUnit_Framework_TestCase
{
    public function testPaginationContent()
    {
        $content = array(
            'start' => 1,
            'end' => 3
        );

        $context = new CommandContext();
        $context->addParam('pagination-content', $content);

        $this->assertTrue($context->hasParam('pagination-content'),
            'CommandPatternTest::testPaginationContent()-> CommandContext::hasParam() returned false but had to return true');
        $this->assertInternalType('array', $context->getParam('pagination-content'),
            'CommandPatternTest::testPattern()-> CommandContext::getParam() had to return array');

        $command = CommandFactory::construct('user-pagination')->getCommand();

        $this->assertInstanceOf('App\\ToolsBundle\\Helpers\\Command\\Commands\\UserPaginationCommand', $command,
            'CommandPatternTest::testPaginationContent()-> CommandFactory did not create UserPaginationCommand');

        $command->execute($context);

        $this->assertTrue($command->isValid(), 'CommandPatternTest::testPaginationContent()-> UserPaginationCommand::isValid() returned false but had to return true');
    }

    public function testFilterContent() {
        $usernameFilter = array(
            'filterType' => 'username-filter',
            'key' => 'username',
            'username' => 'whitepostmail@gmail.com'
        );

        $personalFilter = array(
            'filterType' => 'personal-filter',
            'key' => 'personal',
            'personal' => array(
                'name' => 'Mario',
                'lastname' => 'Škrlec'
            )
        );

        $context = new CommandContext();
        $context->addParam('filtering-content', $personalFilter);

        $this->assertTrue($context->hasParam('filtering-content'),
            'CommandPatternTest::testFilterContent()-> CommandContext::hasParam() returned false but had to return true');
        $this->assertInternalType('array', $context->getParam('filtering-content'),
            'CommandPatternTest::testFilterContent()-> CommandContext::getParam() had to return array');

        $command = CommandFactory::construct('user-filter')->getCommand();

        $this->assertInstanceOf('App\\ToolsBundle\\Helpers\\Command\\Commands\\UserFilterCommand', $command,
            'CommandPatternTest::testPaginationContent()-> CommandFactory did not create UserPaginationCommand');

        $command->execute($context);

        $this->assertTrue($command->isValid(), 'CommandPatternTest::testFilterContent()-> UserFilterCommand::isValid() returned false but had to return true');
    }

    public function testUserInfoContent() {
        $content = array(
            'id' => 1
        );

        $context = new CommandContext();
        $context->addParam('user-info-content', $content);

        $this->assertTrue($context->hasParam('user-info-content'),
            'CommandPatternTest::testUserInfoContent()-> CommandContext::hasParam() returned false but had to return true');
        $this->assertInternalType('array', $context->getParam('user-info-content'),
            'CommandPatternTest::testUserInfoContent()-> CommandContext::getParam() had to return array');

        $command = CommandFactory::construct('user-info')->getCommand();

        $this->assertInstanceOf('App\\ToolsBundle\\Helpers\\Command\\Commands\\UserInfoCommand', $command,
            'CommandPatternTest::testUserInfoContent()-> CommandFactory did not create UserInfoCommand');

        $command->execute($context);

        $this->assertTrue($command->isValid(), 'CommandPatternTest::testUserInfoContent()-> UserInfoCommand::isValid() returned false but had to return true');
    }

    public function testValidUserContent() {
        $content = array(
            'userPermissions' => array(),
            'name' => 'Mario',
            'lastname' => 'Legenda',
            'username' => 'whitepostmail@gmail.com',
            'userPassword' => 'digital1986',
            'userPassRepeat' => 'digital1986',
            'years_of_experience' => 4,
            'fields' => 'web development',
            'programming_languages' => 'php, javascript',
            'tools' => 'git',
            'future_plans' => 'no plans',
            'description' => 'no description',
        );

        $context = new CommandContext();
        $context->addParam('valid-user-content', $content);

        $this->assertTrue($context->hasParam('valid-user-content'),
            'CommandPatternTest::testValidUserContent()-> CommandContext::hasParam() returned false but had to return true');
        $this->assertInternalType('array', $context->getParam('valid-user-content'),
            'CommandPatternTest::testVAlidUserContent()-> CommandContext::getParam() had to return array');

        $command = CommandFactory::construct('valid-user')->getCommand();

        $this->assertInstanceOf('App\\ToolsBundle\\Helpers\\Command\\Commands\\ValidUserCommand', $command,
            'CommandPatternTest::testValidUserContent()-> CommandFactory did not create UserInfoCommand');

        $command->execute($context);

        $this->assertTrue($command->isValid(), 'CommandPatternTest::testValidUserContent()-> ValidUserCommand::isValid() returned false but had to return true');
    }

    public function testValidTestCommand() {
        $content = array(
            'test_name' => 'javascript',
            'test_solvers' => array(
                'whitepostmail@gmail.com',
                'zrinka@gmail.com'
            ),
            'remarks' => 'no remarks'
        );

        $context = new CommandContext();
        $context->addParam('create-test-content', $content);

        $this->assertTrue($context->hasParam('create-test-content'),
            'CommandPatternTest::testValidTestContent()-> CommandContext::hasParam() returned false but had to return true');
        $this->assertInternalType('array', $context->getParam('create-test-content'),
            'CommandPatternTest::testValidTestContent()-> CommandContext::getParam() had to return array');

        $command = CommandFactory::construct('valid-test')->getCommand();

        $this->assertInstanceOf('App\\ToolsBundle\\Helpers\\Command\\Commands\\ValidTestCommand', $command,
            'CommandPatternTest::testValidTestContent()-> CommandFactory did not create ValidTestCommand');

        $command->execute($context);

        $this->assertTrue($command->isValid(), 'CommandPatternTest::testValidTestContent()-> ValidTestCommand::isValid() returned false but had to return true');
    }

    public function testModifiedTestCommand() {
        $content = array(
            'test_control_id' => 1,
            'test_name' => 'javascript',
            'test_solvers' => array(
                'whitepostmail@gmail.com',
                'zrinka@gmail.com'
            ),
            'remarks' => 'no remarks'
        );

        $context = new CommandContext();
        $context->addParam('modify-test-content', $content);

        $this->assertTrue($context->hasParam('modify-test-content'),
            'CommandPatternTest::testModifiedTestContent()-> CommandContext::hasParam() returned false but had to return true');
        $this->assertInternalType('array', $context->getParam('modify-test-content'),
            'CommandPatternTest::testModifiedTestContent()-> CommandContext::getParam() had to return array');

        $command = CommandFactory::construct('valid-modified-test')->getCommand();

        $this->assertInstanceOf('App\\ToolsBundle\\Helpers\\Command\\Commands\\ModifyTestCommand', $command,
            'CommandPatternTest::testModifiedTestContent()-> CommandFactory did not create ModifyTestCommand');

        $command->execute($context);

        $this->assertTrue($command->isValid(), 'CommandPatternTest::testModifiedTestContent()-> ;ModifyTestCommand::isValid() returned false but had to return true');
    }
} 