<?php

namespace App\AuthorizedBundle\Controller;


use App\AuthorizedBundle\Models\CreateUserModel;
use App\AuthorizedBundle\Models\UserModel;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Helpers\AdaptedResponse;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\Exceptions\ModelException;
use App\ToolsBundle\Helpers\Factory\ObjectFactory;
use App\ToolsBundle\Helpers\Factory\Parameters;
use App\ToolsBundle\Helpers\ResponseParameters;



use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use App\ToolsBundle\Repositories\UserRepository;
use App\ToolsBundle\Repositories\FilterRepository;

use ControlFlowCompiler\Compiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use StrongType\String;
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

        $userModel = new UserModel();
        $filterRepo = new FilterRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        $compiler = new Compiler();
        $compiler
            ->runObject($userModel)
            ->withMethods(
                $compiler->method()->name('requestContentMode')->withParameters($content)->void()->save(),
                $compiler->method()->name('extractType')->void()->save(),
                $compiler->method()->name('isContentValid')->true()->save()
            )
            ->ifFailsRun(function() {
                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('error', 'Invalid request from the user');

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(400, "BAD");
            })
            ->then()
            ->runObject($filterRepo)
            ->withMethods(
                $compiler->method()->name('assignFilter')->withParameters($userModel->getType())->void()->save(),
                $compiler->method()->name('runFilter')->withParameters($userModel->getPureContent())->void()->save(),
                $compiler->method()->name('getRepositoryData')->arr()->save()
            )
            ->ifFailsRun(function() {
                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('generic-error', 'Something bad happend');

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(400, "BAD");
            })
            ->ifSuccedesRun(function($context) use ($filterRepo) {
                $users = $context->getObjectStorage()->retreiveUnit($filterRepo)->retreive('getRepositoryData')->getValue();


                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('users', $users);

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(200, "OK");
            })
            ->compile();

        return $compiler->getResponse();
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userListingAction() {
        $doctrine = $this->container->get('doctrine');

        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine,
            'security' => $this->container->get('security.password_encoder')
        )));
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

        $userModel = new UserModel();
        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));

        $compiler = new Compiler();
        $compiler
            ->runObject($userModel)
            ->withMethods(
                $compiler->method()->name('requestContentMode')->withParameters($content)->void()->save(),
                $compiler->method()->name('isValidPagination')->true()->save()
            )
            ->ifFailsRun(function() {
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
            })
            ->then()
            ->runObject($userRepo)
            ->withMethods(
                $compiler->method()->name('getPaginatedUsers')->withParameters($content['start'], $content['end'])->arr()->save()
            )
            ->ifFailsRun(function() {
                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('users', array());

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(200, "OK");
            })
            ->ifSuccedesRun(function($context) use ($userRepo) {
                $users = $context->getObjectStorage()->retreiveUnit($userRepo)->retreive('getPaginatedUsers')->getValue();

                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('users', $users);

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(200, "OK");
            })
            ->compile();

        return $compiler->getResponse();
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userInfoAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent());

        $userModel = new UserModel();
        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine
        )));
        $logger = $this->container->get('app_logger');

        $compiler = new Compiler();
        $compiler
            ->runObject($userModel)
            ->withMethods(
                $compiler->method()->name('requestContentMode')->withParameters($content)->void()->save(),
                $compiler->method()->name('isUserInfoValid')->true()->save()
            )
            ->ifFailsRun(function() use ($logger) {
                $logger->makeLog(AppLogger::WARNING)
                    ->addDate()
                    ->addMessage("Someone tried to make a request outside of the app in UserController::userInfoAction(). Probably inside the console. Possible hack.")
                    ->log();

                $content = new ResponseParameters();
                $content->addParameter("errors", array("Invalid request from the client"));

                $response = new AdaptedResponse();
                $response->setContent($content);
                return $response->sendResponse();
            })
            ->then()
            ->runObject($userRepo)
            ->withMethods(
                $compiler->method()->name('getUserInfoById')->withParameters($content['id'])->arr()->save()
            )
            ->ifFailsRun(function() {
                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('generic-error', 'No user infos were found or something bad happend');

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(400, "BAD");
            })
            ->ifSuccedesRun(function($context) use ($userRepo) {
                $user = $context->getObjectStorage()->retreiveUnit($userRepo)->retreive('getUserInfoById')->getValue();

                $responseParameters = new ResponseParameters();
                $responseParameters->addParameter('user', $user);

                $response = new AdaptedResponse();
                $response->setContent($responseParameters);
                return $response->sendResponse(200, "OK");
            })
            ->compile();

        return $compiler->getResponse();
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

        $userRepo = new UserRepository(new Parameters(array(
            'doctrine' => $doctrine,
            'security' => $this->container->get('security.password_encoder')
        )));
        $result = $userRepo->getUserByUsername($user->getUsername());

        if($result !== null) {
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
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        } catch(\Exception $e) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array($e->getMessage()));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        return new Response('success', 200);
    }
} 