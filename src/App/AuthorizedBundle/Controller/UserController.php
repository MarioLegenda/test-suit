<?php

namespace App\AuthorizedBundle\Controller;

use App\ToolsBundle\Helpers\AdaptedResponse;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandFactory;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\Factories\DoctrineEntityFactory;
use App\ToolsBundle\Helpers\Factory\Parameters;
use App\ToolsBundle\Helpers\ResponseParameters;

use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use App\ToolsBundle\Repositories\UserRepository;
use App\ToolsBundle\Repositories\FilterRepository;

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

        $context = new CommandContext();
        $context->addParam('filtering-content', $content);

        $command = CommandFactory::construct('user-filter')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $filterRepo = new FilterRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $filterRepo->assignFilter($command->getType());
            $filterRepo->runFilter($command->getPureContent());
            $users = $filterRepo->getRepositoryData();
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('generic-error', 'Something bad happend');

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

        $context = new CommandContext();
        $context->addParam('pagination-content', $content);

        $command = CommandFactory::construct('user-pagination')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('errors', 'Invalid request from the client');

            $response = new AdaptedResponse();
            $response->setContent($responseParameters);
            return $response->sendResponse(400, "BAD");
        }

        try {
            $userRepo = new UserRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $users = $userRepo->getPaginatedUsers($content['start'], $content['end']);
        } catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('users', array());

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
     */
    public function userInfoAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $content = (array)json_decode($request->getContent());

        $context = new CommandContext();
        $context->addParam('user-info-content', $content);

        $command = CommandFactory::construct('user-info')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array("Invalid request from the client"));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
            $userRepo = new UserRepository(new Parameters(array(
                'doctrine' => $doctrine
            )));

            $user = $userRepo->getUserInfoById($content['id']);
        }
        catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('generic-error', 'No user infos were found or something bad happend');

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

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function saveUserAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $formValues = (array)json_decode($request->getContent());

        $context = new CommandContext();
        $context->addParam('valid-user-content', $formValues);

        $command = CommandFactory::construct('valid-user')->getCommand();

        if( ! $command->execute($context)->isValid()) {
            $content = new ResponseParameters();
            $content->addParameter("errors", array("Some form values are invalid. Refresh the current page and try again"));

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        $permissionArrayfied = (array)$formValues['userPermissions'];
        $formValues['userPermissions'] = $permissionArrayfied;

        $user = $user = DoctrineEntityFactory::initiate('User')->with($formValues)->create();
        $userInfo = DoctrineEntityFactory::initiate('UserInfo')->with($formValues)->create();

        $toValidate = array($user, $userInfo);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $content = new ResponseParameters();
            $content->addParameter("errors", $errors);

            $response = new AdaptedResponse();
            $response->setContent($content);
            return $response->sendResponse();
        }

        try {
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