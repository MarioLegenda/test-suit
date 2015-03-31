<?php

namespace App\ToolsBundle\Tests\Factories;


use App\ToolsBundle\Helpers\Factories\DoctrineEntityFactory;

class DoctrineEntityFactoryTest  extends \PHPUnit_Framework_TestCase
{
    public function testUserCreation() {
        $userData = array(
            'name' => 'Mario',
            'lastname' => 'Legenda',
            'username' => 'whitepostmail@gmail.com',
            'userPassword' => 'digital1986',
            'userPassRepeat' => 'digital1986'
        );

        $user = DoctrineEntityFactory::initiate('User')->with($userData)->create();

        $this->assertInstanceOf('App\\ToolsBundle\\Entity\\User', $user,
            'DoctrineEntityFactoryTest::testUserCreation()-> User entity could not be created');

        $this->assertEquals('Mario', $user->getName(),
            'DoctrineEntityFactoryTest::testUserCreation()-> User::getName() did not return the correct name');

        $this->assertEquals('Legenda', $user->getLastname(),
            'DoctrineEntityFactoryTest::testUserCreation()-> User::getLastname() did not return the correct lastname');

        $this->assertEquals('whitepostmail@gmail.com', $user->getUsername(),
            'DoctrineEntityFactoryTest::testUserCreation()-> User::getUsername() did not return the correct username');

        $this->assertEquals('digital1986', $user->getPassword(),
            'DoctrineEntityFactoryTest::testUserCreation()-> User::getPassword() did not return the correct password');
    }

    public function testUserInfoCreation() {
        $userInfoData = array(
            'fields' => 'web development',
            'programming_languages' => 'php, javascript',
            'tools' => 'git',
            'years_of_experience' => 4,
            'future_plans' => 'No plans',
            'description' => 'No description'
        );

        $userInfo = DoctrineEntityFactory::initiate('UserInfo')->with($userInfoData)->create();

        $this->assertInstanceOf('App\\ToolsBundle\\Entity\\UserInfo', $userInfo,
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> User entity could not be created');

        $this->assertEquals('web development', $userInfo->getFields(),
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> UserInfo::getTools did not return correct data');

        $this->assertEquals('php, javascript', $userInfo->getProgrammingLanguages(),
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> UserInfo::getProgrammingLanguages() did not return the correct data');

        $this->assertEquals('git', $userInfo->getTools(),
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> UserInfo::getTools() did not return the correct data');

        $this->assertEquals(4, $userInfo->getYearsOfExperience(),
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> UserInfo::getYearsOfExperience() did not return the correct data');

        $this->assertEquals('No plans', $userInfo->getFuturePlans(),
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> UserInfo::getFuturePlans() did not return the correct data');

        $this->assertEquals('No description', $userInfo->getDescription(),
            'DoctrineEntityFactoryTest::testUserInfoCreation()-> UserInfo::getDescription() did not return the correct data');
    }

    public function testTestControlCreation() {
        $test = array(
            'test_name' => 'javascript',
            'test_solvers' => array(
                'whitepostmail@gmail.com',
                'zrinka@gmail.com'
            ),
            'remarks' => 'no remarks'
        );

        $testControl = DoctrineEntityFactory::initiate('TestControl')->with($test)->create();

        $this->assertEquals('javascript', $testControl->getTestName(),
            'DoctrineEntityFactoryTest::testTestControlCreation()-> TestControl::getTestName() did not return the correct value');

        $this->assertInternalType('array', $testControl->getVisibility(),
            'DoctrineEntityFactoryTest::testTestControlCreation()-> TestControl::getTestSolvers() did not return an array');

        $this->assertEquals('no remarks', $testControl->getRemarks(),
            'DoctrineEntityFactoryTest::testTestControlCreation()->  TestControl::getRemarks() did not return the correct value');

    }
} 