<?php

namespace FOS\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
    }

    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/delete');
    }

}
