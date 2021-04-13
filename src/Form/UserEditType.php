<?php

namespace App\Form;

use App\Entity\Sector;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['attr'=>[
                'required',
                'placeholder'=>'Enter your email']])
            ->add('roles', ChoiceType::class,[
            'choices'  => [
                'CLIENT' => 'ROLE_CLIENT',
                'ADMIN' => 'ROLE_ADMIN',
                
            ],
            'attr' => [
                'selected'=>''
            ]
            ])
            ->add('sector', EntityType::class, [
                'class'=>Sector::class, 
                // 'checkbox',
                 'multiple'=>'true',
                 'expanded'=>'true',
                'choice_label'=> function(Sector $sector) {
                    return sprintf('%s', $sector->getNombre());},
                    'placeholder'=>'Select your sector',               
                'attr'=>[
                '',
                ]])
            ->add('update', SubmitType::class,
                ['label' => 'Update User',
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
