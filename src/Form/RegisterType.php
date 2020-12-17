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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom",
                'attr'=>[
                    'placeholder' => "Saisir votre prénom"
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'  => "Votre nom",
                'attr'=>[
                    'placeholder' => "Saisir votre nom"
                ]
            ])
            ->add('email', EmailType::class, [
                'label'=> 'Votre email',
                'attr'=>[
                    'placeholder' => "Saisir votre adresse email"
                ]

            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "le mot de passe et la confirmation doivent être identiques",
                'required' => true,
                'first_options' => [
                    'label' => "Votre mot de passe",
                    'attr' => [
                        'placeholder' => "Saisir votre mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmez votre mot de passe",
                    'attr' => [
                        'placeholder' => "Confirmez votre mot de passe"
                    ]
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 10
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
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
