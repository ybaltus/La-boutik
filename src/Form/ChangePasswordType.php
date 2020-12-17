<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => "Mon prénom"
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => "Mon nom"
            ])
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => "Mon adresse email"
            ])
            ->add('old_password', PasswordType::class, [
                'label' => "Mon mot de passe",
                'attr' => [
                    'placeholder' => "Saisir votre mot de passe"
                ],
                'mapped' => false
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "le mot de passe et la confirmation doivent être identiques",
                'required' => true,
                'first_options' => [
                    'label' => "Mon nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Saisir votre nouveau mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmez votre nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Confirmez votre nouveau mot de passe"
                    ]
                ],
                'mapped' => false
               ])
            ->add('submit', SubmitType::class, [
                'label' => "Modifier mon mot de passe"

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
