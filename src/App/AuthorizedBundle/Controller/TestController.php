<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\HomeModel;
use App\ToolsBundle\Entity\TestControl;
use App\ToolsBundle\Entity\Test;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\BadAjaxResponse;

use App\ToolsBundle\Repositories\TestRepository;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\Query;
use RandomLib;

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
        $factory = new RandomLib\Factory();
        $generator = $factory->getMediumStrengthGenerator();

        $testControl = new TestControl();
        $testControl->setIdentifier($generator->generateString(32));
        $testControl->setTestName($formValues['test_name']);
        $testControl->setVisibility($formValues['test_solvers']);
        $testControl->setRemarks($formValues['remarks']);

        $toValidate = array($testControl);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            return BadAjaxResponse::init(null, $errors)->getResponse();
        }

        $test = new Test();
        $test->setCreated(new \DateTime());
        $test->setTestSerialized(serialize(array()));
        $test->setIsFinished(0);

        $testControl->setTest($test);
        $testControl->setUser($this->container->get('security.context')->getToken()->getUser());

        try {
            $em = $doctrine->getManager();
            $em->persist($testControl);
            $em->flush();
        } catch(\Exception $e) {
            return BadAjaxResponse::init('Something went wrong with creating a test. Please, refresh the page and try again')->getResponse();
        }

        $testRepo = new TestRepository($doctrine);
        $currentTest = $testRepo->getTestByIdentifier($testControl->getIdentifier());


        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $redirectUrl = '/create-test/' . \URLify::filter($currentTest['test_name']) . '/' . $currentTest['test_id'];
        $responseParameters->addParameter('redirectUrl', $redirectUrl);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }
} 