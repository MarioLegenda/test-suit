<?php

namespace App\AuthorizedBundle\Controller;


use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\WorkspaceRepository;
use RCE\Filters\BeArray;
use RCE\Filters\BeString;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\AdaptedResponse;

use App\ToolsBundle\Repositories\Query\Connection;

use RCE\Builder\Builder;
use RCE\ContentEval;
use RCE\Filters\BeInteger;
use RCE\Filters\Exist;

class WorkspaceController extends ContainerAware
{
    private $connection;

    public function __construct() {
        $this->connection = new Connection(array(
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'dbname' => 'suit',
            'user' => 'root',
            'password' => 'digital1986',
            'persistant' => true
        ));
    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/create-answer
     *
     * Client:
     *     Method: Answer.createAnswer()
     *     Namespace: answer.createAnswer
     */
    public function createAnswerAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);
            $userId = $this->container->get('security.token_storage')->getToken()->getUser()->getUserId();

            if($workspaceRepo->getSolvingStatus($content['test_control_id'], $userId)) {

            }
            $workspaceRepo->createAnswer($content['test_control_id'], $userId);
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter("success", true);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/save-answer
     *
     * Client:
     *     Method: Workspace.saveAnswer()
     *     Namespace: workspace.saveAnswer
     */
    public function saveAnswerAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('answer_id'), new BeInteger('answer_id')),
            $builder->expr()->hasTo(new Exist('answer_serialized'), new BeArray('answer_serialized'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);
            $workspaceRepo->saveAnswer($content);

            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('success', true);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(200, "OK");
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/finish-test-solving
     *
     * Client:
     *     Method: Answer.finishTest()
     *     Namespace: answer.finishTest
     */
    public function finishSolvingTestAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('answer_control_id'), new BeInteger('answer_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);
            $workspaceRepo->finishSolvingTest($content['answer_control_id']);
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/get-solving-status
     *
     * Client:
     *     Method: Answer.getSolvingStatus()
     *     Namespace: answer.getSolvingStatus
     */
    public function getSolvingStatusAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $userId = $this->container->get('security.token_storage')->getToken()->getUser()->getUserId();
            $workspaceRepo = new WorkspaceRepository($this->connection);
            $status = $workspaceRepo->getSolvingStatus($content['test_control_id'], $userId);
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('status', $status);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }


    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/save-test
     *
     * Client:
     *     Method: Workspace.saveTest()
     *     Namespace: workspace.saveTest
     */
    public function saveTestAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id')),
            $builder->expr()->hasTo(new Exist('data'), new BeArray('data'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $testRepo = new WorkspaceRepository($this->connection);
            $content['data'] = json_encode($content['data']);
            $testRepo->saveTest($content);
        }
        catch(QueryException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }
        catch(\Exception $e) {
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
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/get-answer
     *
     * Client:
     *     Method: Answer.getAnswer()
     *     Namespace: answer.getAnswer
     */

    public function getAnswerAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('answers_id'), new BeInteger('answers_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);

            $answer = $workspaceRepo->getAnswerById($content['answers_id']);

            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('answer', $answer);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(200, "BAD");
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/workspace-data
     *
     * Client:
     *     Method: Workspace.workspaceData()
     *     Namespace: workspace.workspaceData
     */
    public function workspaceDataAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);

        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);

            $workspaceData = $workspaceRepo->getInitialWorkspaceData($content['test_control_id']);
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
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
            'test_name' => $workspaceData[0]['test_name'],
            'min' => $workspaceData[0]['min'],
            'max' => $workspaceData[0]['max']
        ));
        $responseParameters->addParameter('success', true);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/get-test
     *
     * Client:
     *     Method: Answer.initialAnswerData()
     *     Namespace: answer.initalAnswerData
     */
    public function initialAnswerDataAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);
        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);

            $answerData = $workspaceRepo->getInitialAnswerData($content['test_control_id']);

            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('test', $answerData);
            $responseParameters->addParameter('success', true);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(200, "OK");
        }
        catch(QueryException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }


    }

    /**
     * @Security("has_role('ROLE_TEST_SOLVER')")
     *
     * Route: /test-managment/get-test
     *
     * Client:
     *     Method: Workspace.getTest()
     *     Namespace: workspace.getTest
     */
    public function getTestAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_id'), new BeInteger('test_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter('error', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $testControlId = (array_key_exists('test_control_id', $content)) ? $content['test_control_id'] : null;

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);
            $test = $workspaceRepo->getTestById($content['test_id'], $testControlId);
        }
        catch(QueryException $e) {
            $content = new ResponseParameters();
            $content->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter('error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        if($test === null) {
            $content = new ResponseParameters();

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(205, "No content");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $responseParameters->addParameter('test', json_decode($test['test']['test_serialized']));
        $responseParameters->addParameter('range', $test['range']);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/update-test
     *
     * Client:
     *     Method: Workspace.updateTest()
     *     Namespace: workspace.updateTest
     */
    public function updateTestAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_id'), new BeInteger('test_id')),
            $builder->expr()->hasTo(new Exist('test_data'), new BeArray('test_data'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter('error', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        try {
            $testRepo = new WorkspaceRepository($this->connection);
            $testRepo->modifyTestById($content['test_id'], $content['test_data']);
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
     *
     * Route: /test-managment/delete-question
     *
     * Client:
     *     Method: Workspace.deleteQuestion()
     *     Namespace: workspace.deleteQuestion
     */
    public function deleteQuestionAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_id'), new BeInteger('test_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $workspaceRepo = new WorkspaceRepository($this->connection);
            $workspaceRepo->deleteQuestionById($content['test_id']);
        }
        catch(QueryException $e) {
            $content = new ResponseParameters();
            $content->addParameter("error", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("error", $e->getMessage());

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
     *
     * Route: /test-managment/finish-test
     *
     * Client:
     *     Method: Workspace.finishTest()
     *     Namespace: workspace.finishTest
     */
    public function finishTestAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id')),
            $builder->expr()->hasTo(new Exist('status'), new BeInteger('status'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $testRepo = new WorkspaceRepository($this->connection);

        try {
            $isSuccess = $testRepo->finishTest($content);

            if( ! $isSuccess) {
                $content = new ResponseParameters();
                $content->addParameter("not_finished", "Error: There are no questions in test. Create questions in the Workspace then finish test");

                $response = new AdaptedResponse();
                $response->setContent($content);
                return $response->sendResponse(400, "BAD");
            }
        }
        catch(QueryException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, "BAD");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }
} 