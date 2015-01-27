<?php

namespace App\PublicBundle\Tests\Controller\Ajax;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

use App\PublicBundle\Entity\Administrator;
use App\PublicBundle\Entity\Role;

class InstallControllerTest extends WebTestCase
{
    public function testAdd() {
        $client = static::createClient();

        $container = $client->getContainer();
        $validator = $container->get('validator');

        $values = array(
            'name' => 'Mario',
            'lastname' => 'Škrlec',
            'username' => 'whitepostmail@gmail.com',
            'password' => 'digital1986',
            'pass_repeat' => 'digital1986',
            'dbusername' => 'root',
            'dbpassword' => 'digital1986',
            'dbpass_repeat' => 'digital1986'
        );

        $errors = array(
            'name' => '',
            'lastname' => '',
            'username' => '',
            'user_password' => '',
            'user_pass_repeate' => '',
            'db_username' => '',
            'db_pass' => '',
            'db_pass_repeate' => ''
        );

        $notBlank = new NotBlank();
        $notNull = new NotNull();
        $email = new Email();
        $min = new Length(array('min' => 8));

        $nameError = $validator->validate($values['name'], array($notBlank, $notNull));
        $this->assertEquals(count($nameError), 0);

        $lastnameError = $validator->validate($values['lastname'], array($notNull, $notBlank));
        $this->assertEquals(count($lastnameError), 0);

        $usernameError = $validator->validate($values['username'], array($notBlank, $notNull, $email));
        $this->assertEquals(count($usernameError), 0);

        $passwordError = $validator->validate($values['password'], array($notBlank, $notNull, $min));
        $this->assertEquals(count($passwordError), 0);

        $passRepeatError = $validator->validate($values['pass_repeat'], array($notNull, $notBlank, $min));
        $this->assertEquals(count($passRepeatError), 0);

        $identicalError = $validator->validate($values['password'], array($notBlank, $notNull, new IdenticalTo(array('value' => $values['pass_repeat']))));
        $this->assertEquals(count($identicalError), 0);

        $em = $container->get('doctrine')->getManager();
        $administrator = new Administrator();
        $administrator->setName('Mario');
        $administrator->setLastname('Legenda');
        $administrator->setUsername('whitepostmail@gmail.com');
        $administrator->setPassword('digital1986');
        $role_admin = new Role();
        $role_admin->setRole('ROLE_ADMIN');
        $role_admin->setAdministrator($administrator);
        $role_user = new Role();
        $role_user->setRole('ROLE_USER');
        $role_user->setAdministrator($administrator);
        $administrator->setRoles($role_admin);
        $administrator->setRoles($role_user);

        $em->persist($administrator);
        $em->persist($role_user);
        $em->persist($role_admin);
        $em->flush();

        /* The rest of the code just repeats itself */
    }
} 