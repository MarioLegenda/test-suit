<?php

namespace App\AuthorizedBundle\Controller;

use App\ToolsBundle\Helpers\AdaptedResponse;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\Factories\DoctrineEntityFactory;
use App\ToolsBundle\Helpers\ResponseParameters;

use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\UserRepository;
use App\ToolsBundle\Repositories\FilterRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

use RCE\Builder\Builder;
use RCE\ContentEval;
use RCE\Filters\Exist;
use RCE\Filters\BeString;
use RCE\Filters\BeArray;
use RCE\Filters\OptionalExists;
use RCE\Filters\BeInteger;

class UserController extends ContainerAware
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
     * @Security("has_role('ROLE_USER_MANAGER')")
     *
     * Route: /user-managment/create-user
     *
     * Client:
     *     Method: User.createUser()
     *     Namespace: user.createUser
     */
    public function createUserAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('name'), new BeString('name')),
            $builder->expr()->hasTo(new Exist('lastname'), new BeString('lastname')),
            $builder->expr()->hasTo(new Exist('username'), new BeString('username')),
            $builder->expr()->hasTo(new Exist('userPassword'), new BeString('userPassword')),
            $builder->expr()->hasTo(new Exist('userPassRepeat'), new BeString('userPassRepeat')),
            $builder->expr()->hasTo(new Exist('userPermissions'), new BeArray('userPermissions')),
            $builder->expr()->hasTo(new Exist('fields')),
            $builder->expr()->hasTo(new Exist('programming_languages')),
            $builder->expr()->hasTo(new Exist('tools')),
            $builder->expr()->hasTo(new Exist('years_of_experience')),
            $builder->expr()->hasTo(new Exist('future_plans')),
            $builder->expr()->hasTo(new Exist('description'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $responseParams = new ResponseParameters();
            $responseParams->addParameter("errors", array("Some form values are invalid. Refresh the current page and try again"));

            $response = new AdaptedResponse();
            $response->setContent($responseParams);
            return $response->sendResponse();
        }

        $permissionArrayfied = (array)$content['userPermissions'];
        $content['userPermissions'] = $permissionArrayfied;

        $user = DoctrineEntityFactory::initiate('User')->with($content)->create();
        $userInfo = DoctrineEntityFactory::initiate('UserInfo')->with($content)->create();

        $toValidate = array($user, $userInfo);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $responseParams = new ResponseParameters();
            $responseParams->addParameter("errors", $errors);

            $response = new AdaptedResponse();
            $response->setContent($responseParams);
            return $response->sendResponse();
        }

        try {
            $userRepo = new UserRepository($this->connection);

            $result = $userRepo->getUserByUsername($user->getUsername());

            if($result !== null) {
                $content = new ResponseParameters();
                $content->addParameter("errors", array("errors" => array("User with these credentials already exists")));

                $response = new AdaptedResponse();
                $response->setContent($content);
                return $response->sendResponse();
            }

            $encoder = $this->container->get('security.password_encoder');
            $content['userPassword'] = $encoder->encodePassword($user, $user->getPassword());
            $userRepo->createUser($content);

        } catch(QueryException $e) {
            $content = new ResponseParameters();
            //$content->addParameter("errors", array("errors" => array("Something unsuspected happend and no user has been created. Please, refresh the page and try again. If this, error repeats, contact whitepostmail@gmail.com")));
            $content->addParameter("errors",  $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        } catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array("errors" => array("Something unsuspected happend and no user has been created. Please, refresh the page and try again. If this, error repeats, contact whitepostmail@gmail.com")));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        return new Response('success', 200);
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     *
     * Route: /user-managment/user-filter
     *
     * Client:
     *     Method: User.filter()
     *     Namespace: user.userFilter
     */

    public function filterAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $builder = new Builder($content);

        $builder->build(
            $builder->expr()->hasTo(new Exist('filterType'), new BeString('filterType')),
            $builder->expr()->hasTo(new Exist('key'), new BeString('key')),
            $builder->expr()->hasTo(new OptionalExists(array(
                'username' => new BeString('username'),
                'personal' => new BeArray('personal')
            )))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $filterRepo = new FilterRepository($this->connection);

            $filterRepo->assignFilter($content['filterType']);
            $filterRepo->runFilter($content[$content['key']]);
            $users = $filterRepo->getRepositoryData();
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('generic-error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('users', $users);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     *
     * Route: /user-managment/user-list-paginated
     *     Client:
     *         Method: User.getPaginatedUsers()
     *         Namespace: user.paginatedUsers
     *
     */
    public function userPaginatedAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent());

        $builder = new Builder($content);
        $builder->build(
            $builder->expr()->hasTo(new Exist('start'), new BeInteger('start')),
            $builder->expr()->hasTo(new Exist('end'), new BeInteger('end'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        try {

            $userRepo = new UserRepository($this->connection);

            $users = $userRepo->getPaginatedUsers($content['start'], $content['end']);
        } catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('users', $e->getMessage() . $e->getTraceAsString());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(200, "OK");
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('users', $users);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     *
     * Route: /user-managment/user-info
     *
     * Client:
     *     Method: User.getUserInfoById()
     *     Namespace: user.userInfo
     */
    public function userInfoAction() {
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent());

        $builder = new Builder($content);

        $builder->build(
            $builder->expr()->hasTo(new Exist('user_id'), new BeInteger('user_id'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("error", 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $userRepo = new UserRepository($this->connection);

            $user = $userRepo->getUserInfoById($content['user_id']);
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('generic-error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }


        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('user', $user);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }
} 