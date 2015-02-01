<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.1.2015.
 * Time: 3:23
 */

namespace App\PublicBundle\Controller;


use App\ToolsBundle\Entity\Administrator;
use App\PublicBundle\Forms\AdministratorType;
use App\PublicBundle\Helpers\InstallHelper;
use App\PublicBundle\Helpers\ResponseParameters;
use App\PublicBundle\Helpers\SimpleFormHelper;
use App\PublicBundle\Models\InstallModel;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallController extends ContainerAware
{
    public function installAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $em = $doctrine->getManager();
        $installHelper = new InstallHelper($em);

    }

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

        $administrator = new Administrator();
        $form = $simpleForm->buildForm (
            $this->container->get('form.factory'),
            $administrator,
            new AdministratorType(),
            $request
        );

        $installModel = new InstallModel($em);
        $templating = $this->container->get('templating');

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('form', $form->createView());
        $responseParameters->addParameter('cssClasses', $installModel->createViewClasses());
        return $templating->renderResponse('AppPublicBundle:Installation:installation.html.twig', $responseParameters->getParameters());
    }
} 