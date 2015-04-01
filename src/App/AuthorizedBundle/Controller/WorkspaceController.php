<?php

namespace App\AuthorizedBundle\Controller;

use App\ToolsBundle\Helpers\Command\Filters\Exists;
use App\ToolsBundle\Helpers\Factory\Parameters;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Repositories\TestRepository;
use App\ToolsBundle\Entity\Test;
use App\ToolsBundle\Helpers\AdaptedResponse;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandFactory;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class WorkspaceController extends ContainerAware
{
    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function workspaceDataAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent(), true);

        $context = new CommandContext();
        $context->addParam('id-content', $content);

        $command = CommandFactory::construct('generic-id-check')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        try {
            $testRepo = new TestRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $testControl = $testRepo->getTestControlById($content['id']);
            $testRange = $testRepo->getTestRange($content['id']);
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('test', array(
            'test_name' => $testControl->getTestName(),
            'min' => $testRange['min'],
            'max' => $testRange['max']
        ));
        $responseParameters->addParameter('success', true);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function saveTestAction($testControlId) {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $data = (array)json_decode($request->getContent(), true);

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        $testControl = $testRepo->getTestControlById($testControlId);
        $test = new Test();
        $test->setTestControl($testControl);
        $test->setTestSerialized($data);

        $em = $doctrine->getManager();

        try {
            $em->persist($test);
            $em->flush();
        } catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array("Something went wrong. Please, refresh the page and try again"));

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
    public function finishTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

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

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        try {
            $testRepo->finishTest($content['id']);
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
    public function getTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $contents = (array)json_decode($request->getContent(), true);

        $context = new CommandContext();
        $context->addParam('filters', array(
            new Exists('test_id')
        ));
        $context->addParam('evaluate-data', $contents);

        $command = CommandFactory::construct('configurable')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter('error', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $testControlId = (array_key_exists('test_control_id', $contents)) ? $contents['test_control_id'] : null;
        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        $test = $testRepo->getTestById($contents['test_id'], $testControlId);

        if($test === null) {
            $content = new ResponseParameters();

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(205, "No content");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $responseParameters->addParameter('test', json_decode($test['test']->getTestSerialized()));
        $responseParameters->addParameter('range', $test['range']);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function updateTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $contents = (array)json_decode($request->getContent(), true);
        $id = $contents['id'];
        $content = $contents['test'];

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        try {
            $testRepo->modifyTestById($id, $content);
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
    public function deleteQuestionAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

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

        $testRepo = new TestRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        try {
            $testRepo->deleteQuestionById($content['id']);
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

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
} 