<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => ['class' => 'form-control chic-input']
            ])
            ->add('releaseYear', null, [
                'attr' => ['class' => 'form-control chic-input']
            ])
            ->add('director', null, [
                'attr' => ['class' => 'form-control chic-input']
            ])
            ->add('cast', null, [ // This is now a simple text field
                'attr' => ['class' => 'form-control chic-input'],
                'label' => 'Cast (comma-separated)' // Optional label modification
            ])
            ->add('image', FileType::class, [
                'attr' => ['class' => 'form-control chic-input']
            ])
            ->add('runningTime', null, [
                'attr' => ['class' => 'form-control chic-input']
            ])
            ->add('newReview', ReviewFormType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control chic-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
