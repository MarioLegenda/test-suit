<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\HomeModel;
use App\ToolsBundle\Helpers\ResponseParameters;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends ContainerAware
{
    public function homeAction() {
        $templating = $this->container->get('templating');
        $security = $this->container->get('security.context');

        $genericProfileModel = new HomeModel($security);
        $genericProfileModel->runModel();

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('model', $genericProfileModel);
        return $templating->renderResponse('AppAuthorizedBundle:Home:home.html.twig', $responseParameters->getParameters());
    }
}
