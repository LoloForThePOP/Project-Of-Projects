<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
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
                'file', 
                VichFileType::class, 
                [
                    'allow_delete' => false,
                    'download_label' => false,
                    
                    'label' => 'Choisir un fichier',
                    'attr' => [
                        
                        'placeholder'    => 'Choisir un fichier',
                    ],

                    'required'   => false,
                
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
