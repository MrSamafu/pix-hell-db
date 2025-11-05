<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Mode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Nom du jeu']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5]
            ])
            ->add('platform', TextType::class, [
                'label' => 'Plateforme'
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('developer', TextType::class, [
                'label' => 'Développeur',
                'required' => false,
            ])
            ->add('publisher', TextType::class, [
                'label' => 'Éditeur',
                'required' => false,
            ])
            ->add('series', TextType::class, [
                'label' => 'Série',
                'required' => false,
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Date d\'enregistrement',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (fichier)',
                'mapped' => false,
                'required' => false,
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => 'Genres',
            ])
            ->add('modes', EntityType::class, [
                'class' => Mode::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => 'Modes de jeu',
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
