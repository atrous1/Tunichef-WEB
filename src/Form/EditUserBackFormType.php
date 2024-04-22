<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File as AssertFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class EditUserBackFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                
            ])
            ->add('numtel', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => '+216 -- ---- ---'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse mail',
                
            ])
            ->add('image', FileType::class, [
                'label' => 'Choisir une image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new AssertFile([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPG, JPEG, PNG or GIF)',
                    ])
                ],
            ])
            
            ->add('Soumettre', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
