<?php

namespace App\AuthorizedBundle\Controller;

use App\AuthorizedBundle\Models\CreateUserModel;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Entity\ValidateUser;
use App\ToolsBundle\Forms\ValidateUserType;
use App\ToolsBundle\Helpers\BadAjaxRequest;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\GenericAjaxResponseWrapper;

use App\ToolsBundle\Helpers\SimpleFormHelper;
use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use App\ToolsBundle\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ContainerAware
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function userTemplateAction() {
        $templating = $this->container->get('templating');
        $security = $this->container->get('security.context');


        $responseParameters = new ResponseParameters();

        $createUserModel = new CreateUserModel($security);
        $createUserModel->runModel();

        if( ! $createUserModel->isInRole('ROLE_ADMIN') AND ! $createUserModel->isInRole('ROLE_SUPER_ADMIN')) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_authorized_home'), 302);
        }


        $responseParameters->addParameter('model', $createUserModel);
        return $templating->renderResponse('AppAuthorizedBundle:CreateUser:createUser.html.twig', $responseParameters->getParameters());
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function saveUserAction() {
        $request = $this->container->get('request');

        $formValues = (array)json_decode($request->getContent());
        $permissionArrayfied = (array)$formValues['userPermissions'];
        $formValues['userPermissions'] = $permissionArrayfied;

        $user = new User();
        $user->setName($formValues['name']);
        $user->setLastname($formValues['lastname']);
        $user->setUsername($formValues['username']);
        $user->setPassword($formValues['userPassword']);
        $user->setPassRepeat($formValues['userPassRepeat']);

        $validator = $this->container->get('validator');
        $constraintVioliationList = $validator->validate($user);

        if(count($constraintVioliationList) > 0) {
            $errors = array();
            for($i = 0; $i < count($constraintVioliationList); $i++) {
                $errors["errors"][] = $constraintVioliationList->get($i)->getMessage();
            }

            $response = new Response(json_encode($errors));
            $response->setStatusCode(400, "BAD");
            return $response;
        }

        $em = $this->container->get('doctrine')->getManager();
        $userRepo = new UserRepository($em, $encoder = $this->container->get('security.password_encoder'));
        $result = $userRepo->getUserByUsername($user->getUsername());

        if($result !== null) {
            return BadAjaxRequest::init("User with these credentials already exists.")->getResponse();
        }

        try {
            $userRepo->createUserFromArray($formValues, $user);
            $userRepo->saveUser();
        } catch(RepositoryException $e) {
            return BadAjaxRequest::init("Something unexpected happend. Please, refresh the page and try again")->getResponse();
        } catch(\Exception $e) {
            return BadAjaxRequest::init("Something unexpected happend. Please, refresh the page and try again")->getResponse();
        }

        return new Response('success', 200);
    }
} 