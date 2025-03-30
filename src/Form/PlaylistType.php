<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire d'ajout ou de modification d'une playlist
 * 
 * @author arthurponcin
 */
class PlaylistType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void 
    {
        $builder
                ->add('name', null, [
                    'label' => 'Titre de la playlist'
                ])
                ->add('description', null, [
                    'label' => 'Description',
                    'required' => false,
                    'attr' => [
                        'rows' => 6,
                        'class' => 'form-control'
                    ]
        ]);
    }

    /**
     * Configure les options du form
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
