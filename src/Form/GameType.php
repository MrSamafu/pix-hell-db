<?php

namespace App\Form;

use App\Entity\Console;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Mode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Ex: Super Mario Bros']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Décrivez le jeu...'
                ]
            ])
            ->add('platform', EntityType::class, [
                'class' => Console::class,
                'choice_label' => 'name',
                'label' => 'Console / Plateforme',
                'placeholder' => 'Sélectionnez une console',
                'attr' => ['class' => 'form__select']
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('developer', TextType::class, [
                'label' => 'Développeur',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Nintendo']
            ])
            ->add('publisher', TextType::class, [
                'label' => 'Éditeur',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Nintendo']
            ])
            ->add('series', TextType::class, [
                'label' => 'Série',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Mario']
            ])
            ->add('image', UrlType::class, [
                'label' => 'Image (URL)',
                'required' => false,
                'attr' => ['placeholder' => 'https://example.com/image.jpg']
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => 'Genres',
                'attr' => ['class' => 'form__select']
            ])
            ->add('modes', EntityType::class, [
                'class' => Mode::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => 'Modes de jeu',
                'attr' => ['class' => 'form__select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
