<?php

namespace App\AuthorizedBundle\Controller;


use App\AuthorizedBundle\Models\CreateUserModel;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Helpers\AdaptedResponse;
use App\ToolsBundle\Helpers\AppLogger;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\Exceptions\ModelException;
use App\ToolsBundle\Helpers\Factory\ObjectFactory;
use App\ToolsBundle\Helpers\ResponseParameters;


use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use App\ToolsBundle\Repositories\UserRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ContainerAware
{
    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function filterAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent(), true);

        $factory = new ObjectFactory();
        $userModel = $factory
            ->defineType('App\\AuthorizedBundle\\Models\\UserModel')
            ->createObject();

        if($userModel->requestContentMode($content)->extractType()->isContentValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('error', 'Invalid request from the user');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }


        $filterRepo = $factory
            ->defineType('App\\ToolsBundle\\Repositories\\FilterRepository')
            ->withObjectDependencies(array(
                'doctrine' => $doctrine
            ))
            ->createObject();

        //$filterRepo = new FilterRepository($doctrine);
        try {
            $filterRepo->assignFilter($userModel->getType());
            $filterRepo->runFilter($userModel->getPureContent());
            $users = $filterRepo->getRepositoryData();
        }
        catch(ModelException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('model-error', $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
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
     */
    public function userListingAction() {
        $doctrine = $this->container->get('doctrine');

        $userRepo = new UserRepository($doctrine, $this->container->get('security.password_encoder'));
        $users = $userRepo->getAllUsers();

        $responseParameters = new ResponseParameters();
        if($users !== null) {
            $responseParameters->addParameter('users', $users);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(200, "OK");
        }

        $responseParameters->addParameter('users', array());

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userPaginatedAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        $content = (array)json_decode($request->getContent());

        if( ! array_key_exists('start', $content) OR ! array_key_exists('end', $content)) {
            $logger = $this->container->get('app_logger');
            $logger->makeLog(AppLogger::WARNING)
                ->addDate()
                ->addMessage("Someone tried to make a custom request in UserController::userPaginatedAction(). No 'end' or 'start' parameters. Possible hack")
                ->log();

            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        $factory = new ObjectFactory();

        $userRepo = $factory
            ->defineType('App\\ToolsBundle\\Repositories\\UserRepository')
            ->withObjectDependencies(array(
                'doctrine' => $doctrine
            ))
            ->createObject();

        $users = $userRepo->getPaginatedUsers($content['start'], $content['end']);

        $responseParameters = new ResponseParameters();
        if($users !== null) {
            $responseParameters->addParameter('users', $users);

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(200, "OK");
        }

        $responseParameters->addParameter('users', array());

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userInfoAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent());

        if( ! array_key_exists('id', $content) OR empty($content['id'])) {
            $logger = $this->container->get('app_logger');
            $logger->makeLog(AppLogger::WARNING)
                ->addDate()
                ->addMessage("Someone tried to make a request outside of the app in UserController::userInfoAction(). Probably inside the console. Possible hack.")
                ->log();

            $content = new ResponseParameters();
            $content->addParameter("errors", array("Invalid request from the client"));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $id = $content['id'];

        $factory = new ObjectFactory();
        $userRepo = $factory
            ->defineType('App\\ToolsBundle\\Repositories\\UserRepository')
            ->withObjectDependencies(array(
                'doctrine' => $doctrine
            ))
            ->createObject();

        $user = $userRepo->getUserInfoById($id);

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('user', $user);

        $response = new AdaptedResponse();
        $response->setContent($responseParameters);
        return $response->sendResponse(200, "OK");
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function saveUserAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $formValues = (array)json_decode($request->getContent());

        $userModel = new CreateUserModel($formValues);
        if( ! $userModel->areValidKeys() ) {
            $logger = $this->container->get('app_logger');
            $logger->makeLog(AppLogger::WARNING)
                ->addDate()
                ->addMessage("Someone tried to make a request outside of the app in UserController::saveUserAction(). Probably inside the console. Possible hack.")
                ->log();


            $content = new ResponseParameters();
            $content->addParameter("errors", array("Some form values are invalid. Refresh the current page and try again"));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $permissionArrayfied = (array)$formValues['userPermissions'];
        $formValues['userPermissions'] = $permissionArrayfied;

        $user = new User();
        $user->setName($formValues['name']);
        $user->setLastname($formValues['lastname']);
        $user->setUsername($formValues['username']);
        $user->setPassword($formValues['userPassword']);
        $user->setPassRepeat($formValues['userPassRepeat']);

        $userInfo = new UserInfo();
        $userInfo->setFields($formValues['fields']);
        $userInfo->setProgrammingLanguages($formValues['programming_languages']);
        $userInfo->setTools($formValues['tools']);
        $userInfo->setYearsOfExperience($formValues['years_of_experience']);
        $userInfo->setFuturePlans($formValues['future_plans']);
        $userInfo->setDescription($formValues['description']);
        $toValidate = array($user, $userInfo);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $errors);

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $userRepo = new UserRepository($doctrine, $this->container->get('security.password_encoder'));
        $result = $userRepo->getUserByUsername($user->getUsername());

        if($result !== null) {
            $logger = $this->container->get('app_logger');
            $logger->makeLog(AppLogger::NOTIFICATION)
                ->addDate()
                ->addMessage("Someone wrote an already using email/username which is weird in UserController::saveUserAction()")
                ->log();

            $content = new ResponseParameters();
            $content->addParameter("errors", array("errors" => array("User with these credentials already exists")));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $userRepo->createUserFromArray($formValues, $user);
            $userRepo->saveUser();
        } catch(RepositoryException $e) {
            $logger = $this->container->get('app_logger');
            $logger->makeLog(AppLogger::EXCEPTION)
                ->addDate()
                ->addMessage("User could not be created due to a exception with message: " . $e->getMessage() .
                    " . Possible bug in UserController::saveUserAction or UserRepository::createUserFromArray()
                    or UserRepository::saveUser(). Check those methods.")
                ->log();

            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        } catch(\Exception $e) {
            $logger = $this->container->get('logger');
            $logger->makeLogger(AppLogger::EXCEPTION)
                ->addDate()
                ->addMessage("An unsuspected exception occurred with message: " . $e->getMessage() . ' in UserController::saveUserAction()')
                ->log();
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        return new Response('success', 200);
    }
} 