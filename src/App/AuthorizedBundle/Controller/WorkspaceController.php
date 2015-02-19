<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\WorkspaceModel;
use App\ToolsBundle\Helpers\ResponseParameters;

use App\ToolsBundle\Repositories\TestRepository;
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

            $test = $testRepo->getTestById($testId);

            $context->setProperty('test-name', $test->getTestName());
            $context->setProperty('test-id', $test->getTestId());
        });

        $genericProfileModel->runModel();

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('model', $genericProfileModel);


        $responseParameters->addParameter('model', $genericProfileModel);
        return $templating->renderResponse('AppAuthorizedBundle:Workspace:workspace.html.twig', $responseParameters->getParameters());
    }
} 