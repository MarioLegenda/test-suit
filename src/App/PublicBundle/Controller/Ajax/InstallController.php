<?php

namespace App\PublicBundle\Controller\Ajax;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\ToolsBundle\Entity\InstallEntity;
use App\ToolsBundle\Helpers\GenericAjaxResponseWrapper;
use App\ToolsBundle\Helpers\InstallHelper;
use App\ToolsBundle\Helpers\ResponseParameters;
use App\ToolsBundle\Helpers\SimpleFormHelper;
use App\ToolsBundle\Helpers\UserSecurityManager;
use App\PublicBundle\Models\InstallModel;
use App\ToolsBundle\Helpers\Exceptions\ModelException;

class InstallController extends ContainerAware
{
    public function installAction() {
        $request = $this->container->get('request');

        $doctrine = $this->container->get('doctrine');

        $em = $doctrine->getManager();
        $installHelpers = new InstallHelper($em);

        if( $installHelpers->isAppInstalled() AND $installHelpers->doesAppHasAdmin() ) {
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate('app_authorized_home'), 302);
        }

        $installModel = new InstallModel($em);
        $simpleForm = new SimpleFormHelper();
        $formValues = (array)json_decode($request->getContent());
        $installEntity = new InstallEntity($formValues);

        if($simpleForm->evaluateForm($installEntity, $this->container->get('validator')) !== true) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('form_errors', $simpleForm->getErrors());
            $ajaxResponse = new GenericAjaxResponseWrapper(400, 'OK', $responseParameters);

            return $ajaxResponse->getResponse();
        }


        $installModel->injectDependencies($installEntity);
        $encoder = $this->container->get('security.password_encoder');

        try {
            $modelObjectWrapper = $installModel->runModel();
            $user = $modelObjectWrapper->getObject('user');
            $user->setPassword(UserSecurityManager::initEncoder($encoder)->encodePassword($user));

            $em->persist($modelObjectWrapper->getObject('user'));
            $em->flush();
        } catch(ModelException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter(0, array(
                'message' => 'Something unexpected happend. Please, try again are contact whitepostmail@gmail.com or check the browser console for more information',
                'exception' => $e->getMessage()
            ));

            $ajaxResponse = new GenericAjaxResponseWrapper(400, 'BAD', $responseParameters);

            return $ajaxResponse->getResponse();

        } catch(\Exception $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter(0, array(
                'message' => 'Something unexpected happend. Please, try again are contact whitepostmail@gmail.com or check the browser console for more information',
                'exception' => $e->getMessage()
            ));

            $ajaxResponse = new GenericAjaxResponseWrapper(400, 'BAD', $responseParameters);

            return $ajaxResponse->getResponse();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $ajaxResponse = new GenericAjaxResponseWrapper(200, 'OK', $responseParameters);

        return $ajaxResponse->getResponse();
    }
} 