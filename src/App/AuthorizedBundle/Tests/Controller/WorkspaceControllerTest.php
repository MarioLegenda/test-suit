<?php

namespace App\AuthorizedBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WorkspaceControllerTest  extends WebTestCase
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

    public function testWorkspaceDataAction() {
        $content = array(
            'id' => 20
        );

        $this->client->request('POST', '/test-managment/workspace-data', array(), array(), array(
            'CONTENT_TYPE' => 'application/json'
        ), json_encode($content));

        $request = $this->client->getRequest();
        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode,
            'WorkspaceControllerTest::testWorkspaceDataAction()-> /user-managment/workspace-data failed with status code ' . $statusCode);
    }

    public function testGetTestAction() {
        $content = array(
            'test_id' => 0,
            'test_control_id' => 20
        );

        $this->client->request('POST', '/test-managment/get-test', array(), array(), array(
            'CONTENT_TYPE' => 'application/json'
        ), json_encode($content));

        $request = $this->client->getRequest();
        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode,
            'WorkspaceControllerTest::testGetTestAction()-> /user-managment/get-test failed with status code ' . $statusCode);
    }
} 