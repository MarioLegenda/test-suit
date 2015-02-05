<?php

namespace App\ToolsBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)  {
        $builder
            ->add('name', 'text')
            ->add('lastname', 'text')
            ->add('username', 'email')
            ->add('password', 'password')
            ->add('repeat_password', 'password', array('mapped' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\ToolsBundle\Entity\User',
        ));
    }

    public function getName() {
        return 'insForm';
    }
} 