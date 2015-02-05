<?php

namespace App\PublicBundle\Controller;

use App\PublicBundle\Models\InstallModel;
use App\ToolsBundle\Helpers\InstallHelper;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UnAuthController extends ContainerAware
{
    public function authAction() {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_authorized_home'), 302);
        }

        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        /**
        1. Check if database exists. If it doesn't, procedd immediately to installment
         */

        $em = $doctrine->getManager();
        $installHelper = new InstallHelper($em);

        // provjerava da li je baza i tablica napravljena te provjerava da li postoje upisani useri
        if( ! $installHelper->isAppInstalled() OR ! $installHelper->doesAppHasAdmin()) {
            $templating = $this->container->get('templating');

            return $templating->renderResponse('AppPublicBundle:Login:uninstalled.html.twig');
        }

        /*$options = [
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];
        var_dump(password_hash('sashapopara', PASSWORD_BCRYPT, $options));
die();*/


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