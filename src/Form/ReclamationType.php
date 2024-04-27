<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Reponse;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('avis', null, [ // Utilisez 'null' pour laisser Symfony déduire automatiquement le type du champ
                'constraints' => [
                    new Assert\Length([
                        'max' => 10,
                        'maxMessage' => 'L\'avis ne doit pas dépasser 10 caractères.'
                    ]),
                ],
            ])
            ->add('daterec')
            ->add('idRep', EntityType::class, [
                'class' => Reponse::class,
                'label' => 'ID des réponses associées',
                'multiple' => true, // Permet la sélection de plusieurs réponses
                'expanded' => false, // Utilise une liste déroulante au lieu de cases à cocher
                'choice_label' => 'contenu',
            ])
           ;
          
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
