<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fileName', 
                FileType::class, 
                [
                    'label' => 'Choisir un fichier',
                    'attr' => [
                        
                        'placeholder'    => 'Choisir un fichier',
                    ],

                    'required'   => true,
                
                ]
            )

            ->add(
                'title', 
                TextType::class,
                [
                    'label' => 'Donner un titre au document',
                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],
                    'required'   => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
