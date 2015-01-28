<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 25.1.2015.
 * Time: 16:38
 */

namespace App\PublicBundle\Controller\Ajax;



use App\PublicBundle\Entity\InstallEntity;
use App\PublicBundle\Helpers\GenericAjaxResponseWrapper;
use App\PublicBundle\Helpers\InstallHelper;
use App\PublicBundle\Helpers\ResponseParameters;
use App\PublicBundle\Helpers\SimpleFormHelper;
use App\PublicBundle\Helpers\UserSecurityManager;
use App\PublicBundle\Models\InstallModel;
use Symfony\Component\DependencyInjection\ContainerAware;
use App\PublicBundle\Helpers\Exceptions\ModelException;

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


        $installModel->injectDependencies($installEntity);
        $encoder = $this->container->get('security.password_encoder');

        try {
            $modelObjectWrapper = $installModel->runModel();
            $administrator = $modelObjectWrapper->getObject('administrator');
            $administrator->setPassword(UserSecurityManager::initEncoder($encoder)->encodePassword($administrator));

            $em->persist($modelObjectWrapper->getObject('administrator'));
            $em->persist($modelObjectWrapper->getObject('role_user'));
            $em->persist($modelObjectWrapper->getObject('role_admin'));
            $em->flush();
        } catch(ModelException $e) {
            $responseParameters = new ResponseParameters();
            $responseParameters->addParameter('model-exception', array(
                'unexpected' => true,
                'unintentional' => true,
                'not_catchable' => true,
                'message' => 'Something unexpected happend. Please, try again are contact whitepostmail@gmail.com'
            ));

            $ajaxResponse = new GenericAjaxResponseWrapper(400, 'BAD', $responseParameters);

            return $ajaxResponse->getResponse();
        } catch(\Exception $e) {
            // make response to client with friendly error message
            echo $e->getMessage();
            die();
        }

        $responseParameters = new ResponseParameters();
        $responseParameters->addParameter('success', true);
        $ajaxResponse = new GenericAjaxResponseWrapper(200, 'OK', $responseParameters);

        return $ajaxResponse->getResponse();
    }
} 