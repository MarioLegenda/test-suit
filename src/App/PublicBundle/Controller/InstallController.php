<?php

namespace App\PublicBundle\Controller;


use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Forms\UserType;
use App\ToolsBundle\Helpers\InstallHelper;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\SimpleFormHelper;
use App\PublicBundle\Models\InstallModel;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallController extends ContainerAware
{
    public function signUpAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $em = $doctrine->getManager();
        $installHelper = new InstallHelper($em);

        if($installHelper->isAppInstalled() AND $installHelper->doesAppHasAdmin()) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('suit-up'), 302);
        }

        $simpleForm = new SimpleFormHelper();

        $administrator = new User();
        $form = $simpleForm->buildForm (
            $this->container->get('form.factory'),
            $administrator,
            new UserType(),
            $request
        );

        $templating = $this->container->get('templating');

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('form', $form->createView());
        return $templating->renderResponse('AppPublicBundle:Installation:installation.html.twig', $responseParameters->getParameters());
    }
} 