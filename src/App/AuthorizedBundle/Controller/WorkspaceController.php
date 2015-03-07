<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\WorkspaceModel;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\Exceptions\JsonFormatterException;
use App\ToolsBundle\Helpers\BadAjaxResponse;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Helpers\TestJsonFormatter;
use App\ToolsBundle\Repositories\TestRepository;
use App\ToolsBundle\Entity\Test;

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

        $contents = (array)json_decode($request->getContent(), true);
        $testId = $contents['id'];

        $testRepo = new TestRepository($doctrine);
        $testControl = $testRepo->getTestControlById($testId);
        $testRange = $testRepo->getTestRange($testId);

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('test', array(
            'test_name' => $testControl->getTestName(),
            'min' => $testRange['min'],
            'max' => $testRange['max']
        ));
        $responseParameters->addParameter('success', true);

        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function saveTestAction($testControlId) {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $data = (array)json_decode($request->getContent(), true);

        $testRepo = new TestRepository($doctrine);

        $testControl = $testRepo->getTestControlById($testControlId);
        $test = new Test();
        $test->setTestControl($testControl);
        $test->setTestSerialized($data);

        $em = $doctrine->getManager();

        try {
            $em->persist($test);
            $em->flush();
        } catch(\Exception $e) {
            return BadAjaxResponse::init('Something went wrong. Please, refresh the page and try again')->getResponse();
        }


        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function finishTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $contents = (array)json_decode($request->getContent(), true);
        $id = $contents['id'];

        $testRepo = new TestRepository($doctrine);

        try {
            $testRepo->finishTest($id);
        } catch(\Exception $e) {
            return BadAjaxResponse::init($e->getMessage())->getResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function getTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $contents = (array)json_decode($request->getContent(), true);
        $testId = $contents['test_id'];
        $testControlId = (array_key_exists('test_control_id', $contents)) ? $contents['test_control_id'] : null;

        $testRepo = new TestRepository($doctrine);
        $test = $testRepo->getTestById($testId, $testControlId);

        if($test === null) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('success', true);
            return GoodAjaxRequest::init($responseParameters)->getResponse(205);
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $responseParameters->addParameter('test', json_decode($test['test']->getTestSerialized()));
        $responseParameters->addParameter('range', $test['range']);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
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

        $testRepo = new TestRepository($doctrine);

        try {
            $testRepo->modifyTestById($id, $content);
        } catch(\Exception $e) {
            return BadAjaxResponse::init('Something went wrong. Please, refresh the page and try again')->getResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function deleteQuestionAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $contents = (array)json_decode($request->getContent(), true);
        $id = $contents['id'];

        $testRepo = new TestRepository($doctrine);

        try {
            $testRepo->deleteQuestionById($id);
        } catch(\Exception $e) {
            return BadAjaxResponse::init($e->getMessage())->getResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }
} 