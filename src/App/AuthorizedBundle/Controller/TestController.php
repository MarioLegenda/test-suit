<?php

namespace App\AuthorizedBundle\Controller;

use App\ToolsBundle\Entity\TestControl;
use App\ToolsBundle\Entity\Test;
use App\ToolsBundle\Helpers\Factory\Parameters;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\BadAjaxResponse;
use App\ToolsBundle\Helpers\AdaptedResponse;

use App\ToolsBundle\Repositories\TestRepository;
use App\ToolsBundle\Repositories\UserRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\Query;
use RandomLib;

class TestController extends ContainerAware
{
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
        $testControl->setIsFinished(0);

        $toValidate = array($testControl);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $errors);

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $testControl->setUser($this->container->get('security.context')->getToken()->getUser());

        try {
            $em = $doctrine->getManager();
            $em->persist($testControl);
            $em->flush();
        } catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));
        $currentTest = $testRepo->getTestByIdentifier($testControl->getIdentifier());


        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $redirectUrl = '/test-managment/create-test/' . \URLify::filter($currentTest['test_name']) . '/' . $currentTest['test_control_id'];
        $responseParameters->addParameter('redirectUrl', $redirectUrl);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    public function modifyTestAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent());

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        try {
            $testRepo->updateTestById($content['test_control_id'], $content);
        } catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function getTestsBasicAction() {
        $doctrine = $this->container->get('doctrine');
        $security = $this->container->get('security.context');

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));
        $userId = $security->getToken()->getUser()->getUserId();
        $basicTestInfo = $testRepo->getBasicTestInformation($userId, $userRepo);

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('tests', $basicTestInfo);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function getTestBasicAction() {
        $doctrine = $this->container->get('doctrine');
        $security = $this->container->get('security.context');
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent());
        $id = $content['id'];

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));
        $basicTestInfo = $testRepo->getBasicTestInformationById($id);


        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('test', $basicTestInfo);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function deleteTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');
        $security = $this->container->get('security.context');

        $content = (array)json_decode($request->getContent());
        $id = $content['id'];

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        try {
            $testRepo->deleteTestById($id);
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $basicTestInfo = $testRepo->getBasicTestInformation($security->getToken()->getUser()->getUserId());

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('tests', $basicTestInfo);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");

    }
} 