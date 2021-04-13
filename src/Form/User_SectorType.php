<?php

namespace App\Form;

use App\Entity\Sector;
use App\Entity\User;
use App\Entity\User_Sector;
use App\Form\UserType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class User_SectorType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['attr'=>[
                'required',
                'placeholder'=>'Enter your email']])
            ->add('password', PasswordType::class)
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
                ->add('signup', SubmitType::class, ['label' => 'Sign Up'])
            ->add('cancel', SubmitType::class, [
                'label' => 'Cancel',
                'attr'=>[
                    'class'=>'btn btn-warning']])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User_Sector::class,
        ]);
    }
}
