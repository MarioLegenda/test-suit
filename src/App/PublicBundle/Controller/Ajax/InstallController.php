<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 25.1.2015.
 * Time: 16:38
 */

namespace App\PublicBundle\Controller\Ajax;


use App\PublicBundle\Entity\Administrator;
use App\PublicBundle\Entity\InstallEntity;
use App\PublicBundle\Entity\Role;
use App\PublicBundle\Models\Helpers\GenericAjaxResponseWrapper;
use App\PublicBundle\Models\Helpers\InstallHelper;
use App\PublicBundle\Models\Helpers\ResponseParameters;
use App\PublicBundle\Models\Helpers\SimpleFormHelper;
use App\PublicBundle\Models\InstallModel;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class InstallController extends ContainerAware
{
    public function installAction($testingValues = null) {
        $request = $this->container->get('request');

        $doctrine = $this->container->get('doctrine');

        $em = $doctrine->getManager();
        $installHelpers = new InstallHelper($em);

        /*if( $installHelpers->isAppInstalled() AND $installHelpers->doesAppHasAdmin() ) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_public'), 302);
        }*/

        $installModel = new InstallModel($em);
        $simpleForm = new SimpleFormHelper();
        $formValues = (array)json_decode($request->getContent());
        $installEntity = new InstallEntity($formValues);
        if($simpleForm->evaluateForm($installEntity, $this->container->get('validator')) !== true) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('form_errors', $simpleForm->getErrors());
            $ajaxResponse = new GenericAjaxResponseWrapper(200, 'OK', $responseParameters);

            return $ajaxResponse->getResponse();
        }


        $installModel->runModel();

        try {
            $administrator = new Administrator();
            $administrator->setName($installEntity->getName());
            $administrator->setLastname($installEntity->getLastname());
            $administrator->setUsername($installEntity->getUsername());
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($administrator, $installEntity->getPassword());
            $administrator->setPassword($encoded);
        } catch(\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $role_admin = new Role();
        $role_admin->setRole('ROLE_ADMIN');
        $role_admin->setAdministrator($administrator);

        $role_user = new Role();
        $role_user->setRole('ROLE_USER');
        $role_user->setAdministrator($administrator);

        $administrator->setRoles($role_admin);
        $administrator->setRoles($role_user);

        try {
            $em->persist($administrator);
            $em->persist($role_user);
            $em->persist($role_admin);
            $em->flush();
        }
        catch(\Exception $e) {
            echo $e->getMessage();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $ajaxResponse = new GenericAjaxResponseWrapper(200, 'OK', $responseParameters);

        return $ajaxResponse->getResponse();
    }
} 