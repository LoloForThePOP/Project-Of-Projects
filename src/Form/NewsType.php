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

            //News text content textarea

            ->add('textContent', TextareaType::class, 
                [
                    'label' => 'Texte de la News',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false, //otherwise form won't be submitted

                ]
            )

            //News potential image 1

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

            //News potential caption for image 1

            ->add('captionImage1', TextType::class, 
                [
                    'label' => 'Légende Image 1',

                    'attr' => [
                        
                        'placeholder'    => 'Légende facultative',
                    ],

                    'required'   => false,
                ]
            )

            //News potential image 2

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

            //News potential caption for image 2

            ->add('captionImage2', TextType::class, 
                [
                    'label' => 'Légende Image 2',

                    'attr' => [
                        
                        'placeholder'    => 'Légende facultative',
                    ],

                    'required'   => false,
                ]
            )

            //News potential image 3

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

            //News potential caption for image 3

            ->add('captionImage3', TextType::class, 
                [
                    'label' => 'Légende Image 3',

                    'attr' => [
                        
                        'placeholder'    => 'Légende facultative',
                    ],

                    'required'   => false,
                ]
            )

            //Which presentation is concerned by this news?
            //We store this presentation id in an hidden field

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
