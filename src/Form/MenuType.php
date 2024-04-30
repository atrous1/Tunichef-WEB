<?php

namespace App\Form;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbrPage', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Number of Pages',
            ])
            ->add('categorie', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Category',
                'choices' => [
                    'Kids' => 'Kids',
                    'Tunisian' => 'Tunisian',
                    'European' => 'European',
                    'Oriental' => 'Oriental',
                ],
            ])
            ->add('origine', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Origin',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
