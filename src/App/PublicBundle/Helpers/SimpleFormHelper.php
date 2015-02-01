<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 26.1.2015.
 * Time: 12:33
 */

namespace App\PublicBundle\Helpers;

use App\ToolsBundle\Entity\GenericEntity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;

class SimpleFormHelper
{
    private $modelErrors = false;

    public function buildForm($formFactory, GenericEntity $administrator, AbstractType $type, Request $request) {
        $form = $formFactory->createBuilder($type, $administrator)->getForm();
        $form->handleRequest($request);
        return $form;
    }

    public function evaluateForm(GenericEntity $entity, $validator) {
        $errorList = $validator->validate($entity);

        if(count($errorList) === 0) {
            return true;
        }

        $errors = array();
        for($i = 0; $i < count($errorList); $i++) {
            $errors[$i] = $errorList->get($i)->getMessage();
        }

        $this->modelErrors = $errors;

        return false;
    }

    public function getErrors() {
        return $this->modelErrors;
    }
} 