<?php

namespace App\AuthorizedBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TestControllerTest  extends WebTestCase
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

    public function testCreateTestAction() {
        /*$content = array(
            'test_name' => 'fuck you',
            'test_solvers' => array(
                'whitepostmail@gmail.com',
                'zrinka@gmail.com'
            ),
            'remarks' => 'no remarks'
        );

        $this->client->request('POST', '/test-managment/create-test', array(), array(), array(
            'CONTENT_TYPE' => 'application/json'
        ), json_encode($content));

        $request = $this->client->getRequest();
        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode,
            'UserControllerTest::testCreateTestAction()-> /test-managment/create-test failed with status code ' . $statusCode);*/
    }
} 