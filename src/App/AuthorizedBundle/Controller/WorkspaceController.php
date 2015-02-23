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
    public function workspaceTemplateAction($testName, $testId) {
        $templating = $this->container->get('templating');
        $authorization = $this->container->get('security.authorization_checker');
        $doctrine = $this->container->get('doctrine');


        $genericProfileModel = new WorkspaceModel(
            $authorization,
            $this->container->get('security.context')->getToken()->getUser()
        );

        $genericProfileModel->populateWithClojure(function($context) use($doctrine, $testId) {
            $testRepo = new TestRepository($doctrine);

            $testControl = $testRepo->getTestControlById($testId);
            $testRange = $testRepo->getTestRange($testId);

            $context->setProperty('test-name', $testControl->getTestName());
            $context->setProperty('test-id', $testControl->getTestControlId());
            $context->setProperty('min', $testRange['min']);
            $context->setProperty('max', $testRange['max']);
        });

        $genericProfileModel->runModel();

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('model', $genericProfileModel);


        $responseParameters->addParameter('model', $genericProfileModel);
        return $templating->renderResponse('AppAuthorizedBundle:Workspace:workspace.html.twig', $responseParameters->getParameters());
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
        $test->setCreated(new \DateTime());
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

    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     */
    public function getTestAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $contents = (array)json_decode($request->getContent(), true);
        $id = $contents[0];

        $testRepo = new TestRepository($doctrine);
        $test = $testRepo->getTestById($id);

        if($test === null) {
            return BadAjaxResponse::init('')->getResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $responseParameters->addParameter('test', $test->getTestSerialized());
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }
} 