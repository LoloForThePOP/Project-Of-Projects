<?php

namespace App\Form;

use App\Entity\ContributorStructure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContributorStructureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add(
            'title',
            TextareaType::class, 
            [
                'label' => 'Résultat',
                'attr' => [
                    
                    'placeholder'    => '',
                ],

                'required'   => true,
            ]
        )

        ->add(
            'richTextContent',
            TextareaType::class, 
            [
                'label' => '',

                'attr' => [
                    'placeholder'    => '',
                    'class' => "tinymce",
                ],

                'required'   => false,
                'sanitize_html' => true,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContributorStructure::class,
        ]);
    }
}
