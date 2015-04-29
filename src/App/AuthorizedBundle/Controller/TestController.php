<?php

namespace App\AuthorizedBundle\Controller;

use App\ToolsBundle\Helpers\Factories\DoctrineEntityFactory;
use App\ToolsBundle\Helpers\Observer\Exceptions\ObserverException;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\AdaptedResponse;

use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\TestRepository;
use App\ToolsBundle\Repositories\UserRepository;
use App\ToolsBundle\Repositories\Query\Connection;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\Query;
use RandomLib;

use RCE\Builder\Builder;
use RCE\ContentEval;
use RCE\Filters\Exist;
use RCE\Filters\BeString;
use RCE\Filters\BeArray;
use RCE\Filters\BeInteger;

class TestController extends ContainerAware
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
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/create-test
     *
     * Client:
     *     Method: Test.createTest()
     *     Namespace: test.createTest
     */
    public function createTestAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('scenario'), new BeArray('scenario'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $builder = new Builder($content['scenario']);
        $builder->build(
            $builder->expr()->hasTo(new Exist('condition'), new BeString('condition')),
            $builder->expr()->hasTo(new Exist('data'), new BeArray('data'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $testControl = DoctrineEntityFactory::initiate('TestControl')->with($content['scenario']['data'])->create();

        $toValidate = array($testControl);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', $errors);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        try {

            $content['scenario']['data']['user_id'] = $this->container->get('security.context')->getToken()->getUser()->getUserId();
            $testRepo = new TestRepository($this->connection);
            $testRepo->createTest($content);
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

        $content = new ResponseParameters();
        $content->addParameter("success", true);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/delete-test
     *
     * Client:
     *     Method: Test.deleteTest()
     *     Namespace: test.deleteTest
     */
    public function deleteTestAction() {
        $request = $this->container->get('request');
        $security = $this->container->get('security.context');

        $content = (array)json_decode($request->getContent());

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $testRepo = new TestRepository($this->connection);
            $testRepo->deleteTestSuitById($content['test_control_id']);

            $user = $security->getToken()->getUser();
            $basicTestInfo = $testRepo->getTestsList($user->getUserId());
        }
        catch(QueryException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('tests', $basicTestInfo);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");

    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/get-permission-type
     *     Client:
     *          Method: Test.getTestPermissionType
     *          Namespace: test.getPermissionType
     */
    public function getPermissionTypeAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $testRepo = new TestRepository($this->connection);

            $permission = $testRepo->getPermissionTypeByTestId($content['test_control_id']);
        }
        catch(ObserverException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage()  . $e->getTraceAsString());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage() . $e->getTraceAsString());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $content = new ResponseParameters();
        $content->addParameter('permission', $permission);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, 'OK');
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/get-tests-listing
     *     Client:
     *         Method: Test.getTestsListing()
     *         Namespace: test.getTestsListing
     */
    public function getTestsListingAction() {
        $security = $this->container->get('security.context');

        $testRepo = new TestRepository($this->connection);
        $userRepo = new UserRepository($this->connection);

        $userId = $security->getToken()->getUser()->getUserId();
        $basicTestInfo = $testRepo->getTestsList($userId, $userRepo);

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('tests', $basicTestInfo);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/get-permitted-tests
     *     Client:
     *         Method: Test.getPaginatedPermittedTests()
     *         Namespace: test.getPermittedTests
     */
    public function getPermittedTestsAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);

        $builder->build(
            $builder->expr()->hasTo(new Exist('start'), new BeInteger('start')),
            $builder->expr()->hasTo(new Exist('end'), new BeInteger('end'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $testRepo = new TestRepository($this->connection);

            $userId = $this->container->get('security.token_storage')->getToken()->getUser()->getUserId();
            $tests = $testRepo->getPermittedTestsByUserId($userId);
        }
        catch(QueryException $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage() . $e->getTraceAsString());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $content = new ResponseParameters();
        $content->addParameter('tests', $tests);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, 'OK');
    }

    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/get-permitted-users
     *
     * Client:
     *     Method: Test.getPermittedUsers()
     *     Namespace: test.getPermittedUsers
     */
    public function getPermittedTestUsersAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent());

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }


        try {
            $testRepo = new TestRepository($this->connection);

            $restrictedUsers = $testRepo->getRestrictedTestsByTestControlId($content['test_control_id']);
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $content = new ResponseParameters();
        $content->addParameter('users', $restrictedUsers);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, 'OK');
    }


    /**
     * @Security("has_role('ROLE_TEST_CREATOR')")
     *
     * Route: /test-managment/get-assigned-users-ids
     *     Client:
     *         Method: Test.getAssignedTestUsersIds()
     *         Namespace: test.assignedUsersIds
     */
    public function getAssignedUsersIdsAction() {
        $request = $this->container->get('request');

        $content = json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('test_control_id'), new BeInteger('test_control_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        try {
            $userRepo = new UserRepository($this->connection);

            $ids = $userRepo->getAssignedUsersByTestId($content['test_control_id']);
        }
        catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse(400, 'BAD');
        }

        $content = new ResponseParameters();
        $content->addParameter('ids', $ids);

        $response = new AdaptedResponse();
        $response->setContent($content);
        return $response->sendResponse(200, 'OK');
    }
} 