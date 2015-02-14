<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\HomeModel;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Helpers\ResponseParameters;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TestController extends ContainerAware
{
    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function createTestTemplateAction() {
        $templating = $this->container->get('templating');
        $authorization = $this->container->get('security.authorization_checker');

        $genericProfileModel = new HomeModel($authorization, $this->container->get('security.context')->getToken()->getUser());
        $genericProfileModel->runModel();

        $responseParameters = new ResponseParameters();

        $responseParameters->addParameter('model', $genericProfileModel);
        return $templating->renderResponse('AppAuthorizedBundle:Test:createTest.html.twig', $responseParameters->getParameters());
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function createTestAction() {
        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }
} 