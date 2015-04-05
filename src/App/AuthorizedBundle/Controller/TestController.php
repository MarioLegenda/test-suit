<?php

namespace App\AuthorizedBundle\Controller;

use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandFactory;
use App\ToolsBundle\Helpers\Command\Filters\Exists;
use App\ToolsBundle\Helpers\Factories\DoctrineEntityFactory;
use App\ToolsBundle\Helpers\Factory\Parameters;
use App\ToolsBundle\Helpers\Observer\Exceptions\ObserverException;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\AdaptedResponse;

use App\ToolsBundle\Repositories\TestRepository;
use App\ToolsBundle\Repositories\UserRepository;
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

        $content = (array)json_decode($request->getContent(), true);

        $context = new CommandContext();
        $context->addParam('create-test-content', $content);

        $command = CommandFactory::construct('valid-test')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $testControl = DoctrineEntityFactory::initiate('TestControl')->with($content)->create();

        $toValidate = array($testControl);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', $errors);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
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
            return $response->sendResponse(400, "BAD");
        }

        try {
            $testRepo = new TestRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $testRepo->createAssignedTests(
                $testControl->getTestControlId(),
                $content['test_solvers']
            );
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        $currentTest = $testRepo->getTestByIdentifier($testControl->getIdentifier());

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $redirectUrl = '/test-managment/create-test/' . \URLify::filter($currentTest['test_name']) . '/' . $currentTest['test_control_id'];
        $responseParameters->addParameter('redirectUrl', $redirectUrl);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function modifyTestAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent());

        $context = new CommandContext();
        $context->addParam('create-test-content', $content);

        $command = CommandFactory::construct('valid-test')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $testRepo = new TestRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $testRepo->updateTestById($content['test_control_id'], $content);
        }
        catch(ObserverException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $content = new ResponseParameters();
        $content->addParameter('success', true);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, 'OK');
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function getTestPermissionsAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent());

        $context = new CommandContext();
        $context->addParam('filters', array(
            new Exists('test_control_id')
        ));
        $context->addParam('evaluate-data', $content);

        $command = CommandFactory::construct('configurable')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $testRepo = new TestRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $permittedUsers = $testRepo->getPermittedUsers($content['test_control_id']);
        }
        catch(ObserverException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $content = new ResponseParameters();
        $content->addParameter('test_permittions', $permittedUsers);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, 'OK');
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

        $context = new CommandContext();
        $context->addParam('filters', array(
            new Exists('test_control_id')
        ));
        $context->addParam('evaluate-data', $content);

        $command = CommandFactory::construct('configurable')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));
        $basicTestInfo = $testRepo->getBasicTestInformationById($content['test_control_id']);


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

        $context = new CommandContext();
        $context->addParam('id-content', $content);

        $command = CommandFactory::construct('generic-id-check')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $testRepo = new TestRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $testRepo->deleteTestById($content['id']);
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $user = $security->getToken()->getUser();
        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));
        $basicTestInfo = $testRepo->getBasicTestInformation($user->getUserId(), $userRepo);

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('tests', $basicTestInfo);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");

    }
} 