<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\HomeModel;
use App\ToolsBundle\Entity\TestControl;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\BadAjaxResponse;

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
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $formValues = (array)json_decode($request->getContent());
        $testControl = new TestControl();
        $testControl->setTestName($formValues['test_name']);
        $testControl->setVisibility($formValues['test_solvers']);
        $testControl->setRemarks($formValues['remarks']);

        $toValidate = array($testControl);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            return BadAjaxResponse::init(null, $errors)->getResponse();
        }

        $testControl->setUser($this->container->get('security.context')->getToken()->getUser());

        $em = $doctrine->getManager();
        $em->persist($testControl);
        $em->flush();

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }
} 