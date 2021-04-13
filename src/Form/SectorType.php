<?php

namespace App\Form;

use App\Entity\Sector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'attr'=>[
                'required',
                'placeholder'=>'Enter Sector Name']])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->add('cancel', SubmitType::class, [
                'label' => 'Cancel',
                'attr'=>[
                    'class'=>'btn btn-warning']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sector::class,
        ]);
    }
}
