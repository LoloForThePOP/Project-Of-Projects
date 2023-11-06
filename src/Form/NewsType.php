<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('textContent', TextareaType::class, 
                [
                    'label' => 'Contenu',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'sanitize_html' => true,

                    'required'   => false, //otherwise form won't be submitted
                ]
            )

            ->add('image1File', VichImageType::class, 

                array(

                    'label' => 'Image 1',

                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,

                )

            )

            ->add('captionImage1', TextType::class, 
                [
                    'label' => 'Légende',

                    'attr' => [
                        
                        'placeholder'    => 'Légende facultative',
                    ],

                    'required'   => false,
                ]
            )

            ->add('image2File', VichImageType::class, 

                array(

                    'label' => 'Image 2',

                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,

                )

            )

            ->add('captionImage2', TextType::class, 
                [
                    'label' => 'Légende',

                    'attr' => [
                        
                        'placeholder'    => 'Légende facultative',
                    ],

                    'required'   => false,
                ]
            )

            ->add('image3File', VichImageType::class, 

                array(

                    'label' => 'Image 3',

                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,

                )

            )

            ->add('captionImage3', TextType::class, 
                [
                    'label' => 'Légende',

                    'attr' => [
                        
                        'placeholder'    => 'Légende facultative',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                
                'presentationId', 

                HiddenType::class,
                
                [

                    'empty_data' => 'yes',
                    'required'   => false,
                    "mapped" => false,
                ]
            )


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
