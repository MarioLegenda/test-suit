<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 26.1.2015.
 * Time: 12:33
 */

namespace App\PublicBundle\Models\Helpers;

use App\PublicBundle\Entity\Administrator;

use App\PublicBundle\Entity\GenericEntity;
use App\PublicBundle\Models\Contracts\ClientDependencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

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