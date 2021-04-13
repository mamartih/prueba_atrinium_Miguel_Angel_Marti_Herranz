<?php

namespace App\Form;

use App\Entity\Empresa;
use App\Entity\Sector;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditEmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', TextType::class, ['attr'=>[
            'required',
            'placeholder'=>'Enter your name',]])
        ->add('telefono', TextType::class, [
            'attr'=>[
            'required',
            'placeholder'=>'empresa.telefono']])
        ->add('email', EmailType::class, 
            ['attr'=>[
            'required',
            'placeholder'=>'Enter your email']])
        ->add('sector', EntityType::class, [
            'class'=>Sector::class,
            'choice_label' => 'nombre',
        ])
        ->add('update', SubmitType::class, ['label' => 'Update'])
        ->add('cancel', SubmitType::class, [
            'label' => 'Cancel',
            'attr'=>[
                'class'=>'btn btn-warning']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Empresa::class,
        ]);
    }
}
