<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 2.2.2015.
 * Time: 17:11
 */

namespace App\ToolsBundle\Forms;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)  {
        $builder
            ->add('fields', 'text')
            ->add('programming_languages', 'text')
            ->add('tools', 'text')
            ->add('years_of_experience', 'text')
            ->add('future_plans', 'text')
            ->add('personal_description', 'textarea');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\ToolsBundle\Entity\UserInfo',
        ));
    }

    public function getName() {
        return 'AddUserForm';
    }
} 