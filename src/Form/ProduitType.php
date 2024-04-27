<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProduit', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom Produit',
            ])
            ->add('descriptionProduit', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Description Produit',
            ])
            ->add('imageProduit', FileType::class, [
                'data_class' => null,
                'required' => false,
                'mapped' => false,
                'label' => 'Image (JPEG, PNG)',
                'attr' => ['class' => 'form-control-file'],
            ])
            ->add('prixProduit', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prix Produit',
            ])
            ->add('likeProduit', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Like Produit',
            ])
            ->add('dislikeProduit', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Dislike Produit',
            ])
            ->add('fkMenu', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Menu',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
