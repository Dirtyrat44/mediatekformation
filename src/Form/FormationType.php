<?php

namespace App\Form;


use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * Formulaire d'ajout ou de modification d'une formation
 * 
 * @author arthurponcin
 */
class FormationType extends AbstractType
{
    /**
     * Construit le formulaire
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre de la formation'
                ])
            ->add('publishedAt', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'attr' => [
                    'type' => 'date',
                    'onkeydown' => 'return false;', // bloque la saisie clavier
                    'max' => (new \DateTime())->format('Y-m-d')
                ]
            ])
            ->add('description', null, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 8,
                    'class' => 'form-control'
                ]
            ])
            ->add('videoId', null, [
                'label' => 'ID de la vidéo YouTube'
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'label' => 'Playlist associée',
                'choice_label' => 'name'
                
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégories',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ]);

    }

    /**
     * Configure les options du form (lien entité)
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
