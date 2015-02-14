<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\CreateUserModel;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Helpers\BadAjaxResponse;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
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
    public function userTemplateAction() {
        $templating = $this->container->get('templating');
        $security = $this->container->get('security.context');


        $responseParameters = new ResponseParameters();

        $createUserModel = new CreateUserModel($security);
        $createUserModel->runModel();


        $responseParameters->addParameter('model', $createUserModel);
        return $templating->renderResponse('AppAuthorizedBundle:User:createUser.html.twig', $responseParameters->getParameters());
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userManagmentTemplateAction() {
        $templating = $this->container->get('templating');
        $security = $this->container->get('security.context');


        $responseParameters = new ResponseParameters();

        $createUserModel = new CreateUserModel($security);
        $createUserModel->runModel();


        $responseParameters->addParameter('model', $createUserModel);
        return $templating->renderResponse('AppAuthorizedBundle:User:userManagment.html.twig', $responseParameters->getParameters());
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userFilterAction() {

    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userListingAction() {
        $doctrine = $this->container->get('doctrine');
        $em = $this->container->get('doctrine')->getManager();

        $userRepo = new UserRepository($doctrine, $this->container->get('security.password_encoder'));
        $users = $userRepo->getAllUsers();

        $responseParameters = new ResponseParameters();
        if($users !== null) {
            $responseParameters->addParameter('users', $users);

            return GoodAjaxRequest::init($responseParameters)->getResponse();
        }

        $responseParameters->addParameter('users', array());
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userPaginatedAction() {
        $doctrine = $this->container->get('doctrine');
        $em = $this->container->get('doctrine')->getManager();

        $userRepo = new UserRepository($doctrine, $this->container->get('security.password_encoder'));
        $users = $userRepo->getPaginatedUsers(0, 10);

        $responseParameters = new ResponseParameters();
        if($users !== null) {
            $responseParameters->addParameter('users', $users);

            return GoodAjaxRequest::init($responseParameters)->getResponse();
        }

        $responseParameters->addParameter('users', array());
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function userInfoAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');
        $id = (array)json_decode($request->getContent());

        if(empty($id) OR ! array_key_exists('id', $id)) {
            return BadAjaxResponse::init('Invalid request from client')->getResponse();
        }

        $userRepo = new UserRepository($doctrine);
        $user = $userRepo->getUserById($id['id']);

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('user', $user);
        return GoodAjaxRequest::init($responseParameters)->getResponse();
    }

    /**
     * @Security("has_role('ROLE_USER_MANAGER')")
     */
    public function saveUserAction() {
        $request = $this->container->get('request');
        $doctrine = $this->container->get('doctrine');

        $formValues = (array)json_decode($request->getContent());
        $permissionArrayfied = (array)$formValues['userPermissions'];
        $formValues['userPermissions'] = $permissionArrayfied;

        $user = new User();
        $user->setName($formValues['name']);
        $user->setLastname($formValues['lastname']);
        $user->setUsername($formValues['username']);
        $user->setPassword($formValues['userPassword']);
        $user->setPassRepeat($formValues['userPassRepeat']);

        $userInfo = new UserInfo();
        $userInfo->setYearsOfExperience($formValues['years_of_experience']);
        $toValidate = array($user, $userInfo);
        $errors = ConvenienceValidator::init($toValidate, $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            return BadAjaxResponse::init(null, $errors)->getResponse();
        }

        $userRepo = new UserRepository($doctrine, $this->container->get('security.password_encoder'));
        $result = $userRepo->getUserByUsername($user->getUsername());

        if($result !== null) {
            return BadAjaxResponse::init("User with these credentials already exists.")->getResponse();
        }

        try {
            $userRepo->createUserFromArray($formValues, $user);
            $userRepo->saveUser();
        } catch(RepositoryException $e) {
            return BadAjaxResponse::init("Something unexpected happend. Please, refresh the page and try again")->getResponse();
            //return BadAjaxResponse::init($e->getMessage())->getResponse();
        } catch(\Exception $e) {
            return BadAjaxResponse::init("Something unexpected happend. Please, refresh the page and try again")->getResponse();
            //return BadAjaxResponse::init($e->getMessage())->getResponse();
        }

        return new Response('success', 200);
    }
} 