<?php

namespace App\PublicBundle\Controller;


use App\ToolsBundle\Entity\Role;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Helpers\InstallHelper;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\GoodAjaxRequest;
use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Helpers\ConvenienceValidator;
use App\ToolsBundle\Helpers\AdaptedResponse;

use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\UserRepository;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallController extends ContainerAware
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

    public function installTestSuitAction() {
        $installHelper = new InstallHelper($this->connection);

        if( ! $installHelper->isAppInstalled()) {
            $templating = $this->container->get('templating');

            return $templating->renderResponse('AppPublicBundle:Installation:installation.html.twig');
        }

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate('login'), 302);
    }

    public function installAction() {
        $installHelpers = new InstallHelper($this->connection);

        if($installHelpers->isAppInstalled()) {
            $responseParams = new ResponseParameters();
            $responseParams->addParameter("error", "Invalid request from the client");

            $response = new AdaptedResponse();
            $response->setContent($responseParams);
            return $response->sendResponse();
        }

        $request = $this->container->get('request');
        $content = (array)json_decode($request->getContent());

        $user = new User();
        $user->setName($content['name']);
        $user->setLastname($content['lastname']);
        $user->setUsername($content['username']);
        $user->setPassword($content['userPassword']);
        $user->setPassRepeat($content['userPassRepeat']);

        $errors = ConvenienceValidator::init(array($user), $this->container->get('validator'))->getErrors();

        if($errors !== null) {
            $responseParams = new ResponseParameters();
            $responseParams->addParameter("errors", $errors);

            $response = new AdaptedResponse();
            $response->setContent($responseParams);
            return $response->sendResponse();
        }


        $encoder = $this->container->get('security.password_encoder');
        $content['userPassword'] = $encoder->encodePassword($user, $user->getPassword());

        try {
            $installHelpers->createTables();

            $content['fields'] = '';
            $content['programming_languages'] = '';
            $content['tools'] = '';
            $content['years_of_experience'] = '';
            $content['future_plans'] = '';
            $content['description'] = '';

            $content['userPermissions'] = array(
                'ROLE_TEST_SOLVER' => true,
                'ROLE_TEST_CREATOR' => true,
                'ROLE_USER_MANAGER' => true
            );

            $userRepo = new UserRepository($this->connection);
            $userRepo->createUser($content);
        }
        catch(QueryException $e) {
            $responseParams = new ResponseParameters();
            $responseParams->addParameter("error", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParams);
            return $response->sendResponse();
        }
        catch(\Exception $e) {
            $responseParams = new ResponseParameters();
            $responseParams->addParameter("error", $e->getMessage());

            $response = new AdaptedResponse();
            $response->setContent($responseParams);
            return $response->sendResponse();
        }

        $responseParams = new ResponseParameters();
        $responseParams->addParameter("success", true);

        $response = new AdaptedResponse();
        $response->setContent($responseParams);
        return $response->sendResponse(200, "OK");
    }
} 