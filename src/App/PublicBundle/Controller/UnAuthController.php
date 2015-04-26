<?php

namespace App\PublicBundle\Controller;

use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Helpers\InstallHelper;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UnAuthController extends ContainerAware
{
    private $connection;

    public function __construct() {
        $this->connection = new Connection(array(
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'dbname' => 'suit',
            'user' => 'root',
            'password' => 'digital1986',
            'persistant' => true
        ));
    }

    public function authAction() {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_authorized_home'));
        }

        $installHelper = new InstallHelper($this->connection);

        if( ! $installHelper->isAppInstalled()) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_install_test_suit'), 302);
        }

        /*$options = [
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];
        var_dump(password_hash('sashapopara', PASSWORD_BCRYPT, $options));
die();*/


        $request = $this->container->get('request');

        $session = $request->getSession();
        $templating = $this->container->get('templating');
        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $templating->renderResponse(
            'AppPublicBundle:Login:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }
} 