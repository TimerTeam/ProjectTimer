<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['attr' => ['class' => "form-control"]])
            ->add('lastName', TextType::class, ['label' => "Nom", 'attr' => ['class' => "form-control"]])
            ->add('firstName', TextType::class, ['label' => "Prénom", 'attr' => ['class' => "form-control"]])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe', 'attr' => ['class' => "form-control"]],
                'second_options' => ['label' => "Répétez le mot de passe", 'attr' => ['class' => "form-control"]],
            ])
            ->add('Modifier', SubmitType::class, ['attr' => ['class' => "btn btn-lg btn-primary btn-sbm-mod"]])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
