<?php

namespace App\Form;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    private array $roles = [];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, array(
                'attr' => array('class' => 'form-control',
                    'style' => 'margin:5px 0;'),
                'choices' =>
                    array(
                        'First Agent' => 'ROLE_FIRSTAGENT',
                        'Second agent' => 'ROLE_SECONDAGENT',
                    )
            ,
                'multiple' => true,
                'required' => true,
            ))
            ->add('firstName')
            ->add('lastName');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
