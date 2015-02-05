<?php

namespace App\ToolsBundle\Forms;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ValidateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)  {
        $builder
            ->add('user', new UserType())
            ->add('userInfo', new UserInfoType());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\ToolsBundle\Entity\ValidateUser'
        ));
    }

    public function getName() {
        return 'name';
    }
} 