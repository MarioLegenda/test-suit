<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.1.2015.
 * Time: 14:50
 */

namespace App\PublicBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdministratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)  {
        $builder
            ->add('name', 'text')
            ->add('lastname', 'text')
            ->add('username', 'email')
            ->add('password', 'password')
            ->add('repeat_password', 'password', array('mapped' => false))
            ->add('sign_up', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\PublicBundle\Entity\Administrator',
        ));
    }

    public function getName() {
        return 'insForm';
    }
} 