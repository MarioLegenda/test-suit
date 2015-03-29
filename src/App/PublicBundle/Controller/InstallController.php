<?php

namespace App\PublicBundle\Controller;


use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Helpers\BadAjaxResponse;
use App\ToolsBundle\Helpers\Factory\Parameters;
use App\ToolsBundle\Helpers\InstallHelper;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\BadAjaxRequest;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use App\ToolsBundle\Repositories\UserRepository;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallController extends ContainerAware
{
    public function signUpAction() {
        $doctrine = $this->container->get('doctrine');

        $em = $doctrine->getManager();
        $installHelper = new InstallHelper($em);

        if($installHelper->isAppInstalled() AND $installHelper->doesAppHasAdmin()) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('suit-up'), 302);
        }

        $templating = $this->container->get('templating');
        return $templating->renderResponse('AppPublicBundle:Installation:installation.html.twig');
    }

    public function installAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');
        $em = $doctrine->getManager();
        $encoder = $this->container->get('security.password_encoder');

        $installHelpers = new InstallHelper($em);

        if( $installHelpers->isAppInstalled() AND $installHelpers->doesAppHasAdmin() ) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_authorized_home'), 302);
        }


        $formValues = (array)json_decode($request->getContent());

        $user = new User();
        $user->setName($formValues['name']);
        $user->setLastname($formValues['lastname']);
        $user->setUsername($formValues['username']);
        $user->setPassword($formValues['userPassword']);
        $user->setPassRepeat($formValues['userPassRepeat']);

        $validator = $this->container->get('validator');
        $constraintVioliationList = $validator->validate($user);

        if(count($constraintVioliationList) > 0) {
            $errors = array();
            for($i = 0; $i < count($constraintVioliationList); $i++) {
                $errors["errors"][] = $constraintVioliationList->get($i)->getMessage();
            }

            return BadAjaxResponse::init(null, $errors)->getResponse();
        }



        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine,
            'security' => $encoder
        )));
        try {
            $userRepo->createUser($user, array('ROLE_USER_MANAGER', 'ROLE_TEST_CREATOR', 'ROLE_TEST_SOLVER'));
            $userRepo->saveUser();
        } catch(RepositoryException $e) {
            return BadAjaxResponse::init('Something went wrong. Please, refresh the page and try again')->getResponse();
        } catch(\Exception $e) {
            return BadAjaxResponse::init('Something went wrong. Please, refresh the page and try again')->getResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }
} 