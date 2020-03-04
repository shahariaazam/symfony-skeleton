<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'help' => 'Provide your first name',
                'required' => true,
                'attr' => ['maxlength' => 80]
            ])
            ->add('last_name', TextType::class, [
                'help' => 'Provide your last name',
                'attr' => ['maxlength' => 80]
            ])
            ->add('email', EmailType::class, [
                'disabled' => true,
                'help' => 'You can\'t change email address',
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Select gender' => null,
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'help' => 'Select your gender',
                'required' => true,
            ])
            ->add('timezone', TimezoneType::class, [
                'help' => 'Select your timezone',
                'required' => true,
            ])
            ->add('profile', SubmitType::class, [
                'label' => 'Update Profile',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
