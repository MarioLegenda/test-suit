<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\HomeModel;
use App\ToolsBundle\Helpers\ResponseParameters;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use RandomLib;

class HomeController extends ContainerAware
{
    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     */
    public function homeAction() {
        $templating = $this->container->get('templating');
        $authorization = $this->container->get('security.authorization_checker');

        $genericProfileModel = new HomeModel($authorization, $this->container->get('security.context')->getToken()->getUser());
        $genericProfileModel->runModel();

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('model', $genericProfileModel);
        return $templating->renderResponse('AppAuthorizedBundle:Home:home.html.twig', $responseParameters->getParameters());
    }
}
