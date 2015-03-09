<?php

namespace App\AuthorizedBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserListingAction() {
        $client = static::createClient();

        $client->request('POST', '/user-managment/user-info', array(), array(), array(), array('id' => "1"));
        $request = $client->getRequest();
        $content = $request->getContent();

        $this->assertArrayHasKey('id', $content, 'There is no id key');
        $this->assertNotEmpty($content['id'], 'id string is empty');
        $this->assertInternalType('numeric', $content['id'], 'id has to be a number');

        $response = $client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertThat($statusCode, $this->logicalOr(
            $this->assertEquals($statusCode, 302),
            $this->assertEquals($statusCode, 200))
        );
    }
} 