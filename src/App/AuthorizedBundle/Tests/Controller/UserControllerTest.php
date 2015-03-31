<?php

namespace App\AuthorizedBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    private $client;

    public function __construct() {
        $this->client = static::createClient();

        $this->login();
    }

    private function login() {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken('whitepostmail@gmail.com', null, $firewall, array('ROLE_USER_MANAGER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testUserPaginatedAction() {

        $content = array(
            'start' => 1,
            'end' => 5
        );

        $this->client->request('POST', '/user-managment/user-list-paginated', array(), array(), array(
            'CONTENT_TYPE' => 'application/json'
        ), json_encode($content));

        $request = $this->client->getRequest();
        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode,
            'UserControllerTest::testUserPaginatedAction()-> /user-managment/user-list-paginated failed with status code ' . $statusCode);
    }

    public function testFilterAction() {
        $usernameContent = array(
            'filterType' => 'username-filter',
            'key' => 'username',
            'username' => 'whitepostmail@gmail.com'
        );

        $personalContent = array(
            'filterType' => 'personal-filter',
            'key' => 'personal',
            'personal' => array(
                'name' => 'Mario',
                'lastname' => 'Škrlec'
            )
        );

        $this->client->request('POST', '/user-managment/user-filter', array(), array(), array(
            'CONTENT_TYPE' => 'application/json'
        ), json_encode($personalContent));

        $request = $this->client->getRequest();
        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode,
            'UserControllerTest::testFilterAction()-> /user-managment/user-filter failed with status code ' . $statusCode);
    }

    public function testUserInfoAction() {
        $content = array(
            'id' => 1
        );

        $this->client->request('POST', '/user-managment/user-info', array(), array(), array(
            'CONTENT_TYPE' => 'application/json'
        ), json_encode($content));

        $request = $this->client->getRequest();
        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode,
            'UserControllerTest::testUserInfoAction()-> /user-managment/user-info failed with status code ' . $statusCode);
    }
} 