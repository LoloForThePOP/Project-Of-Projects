<?php

namespace App\Form;

use App\Entity\Persorg;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PersorgType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add(
                'name', 
                TextType::class, 
                [
                    'label' => 'Nom de la personne ou organisation',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => true,
                ]
            )

            
            ->add(
                'imageFile',
                VichImageType::class, 

                array(

                    'label' => 'Une photo, une image, ou un logo ?',

                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'asset_helper' => true,

                )

            )

            ->add(
                'description',
                TextareaType::class, 
                [
                    'label' => 'Ajouter des informations ou des remarques ?',

                    'attr' => [
                        
                        'placeholder'    => "Exemple (cas d'une personne) : Aime la lecture, la musique, et la marche. Autre exemple (cas d'une organisation) : Leader dans le domaine de la construction écologique.",

                        'rows' => '4',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                'email',
                EmailType::class, 
                [
                    'label' => 'Une adresse e-mail ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                'website1',
                UrlType::class, 
                [
                    'label' => 'Réseau social ou site web',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                'website2',
                UrlType::class, 
                [
                    'label' => 'Réseau social ou site web 2',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                'website3',
                UrlType::class, 
                [
                    'label' => 'Réseau social ou site web 3',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                'website4',
                UrlType::class, 
                [
                    'label' => 'Réseau social ou site web 4',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,
                ]
            )

            ->add(
                'postalMail', 
                TextareaType::class,
                [
                    'label' => 'Une adresse postale ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                ]
            )

            ->add(
                'tel1', 
                TelType::class,
                [
                    'label' => 'Un téléphone ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                ]

            )

            ->add(
                'tel2', 
                TelType::class,
                [
                    'label' => 'Un autre téléphone ?',

                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici',
                    ],

                    'required'   => false,

                ]

            )

            // Storing Potential Parent Structure
                        
            ->add('parentStuctureId', HiddenType::class, 
                [

                    'required'   => false,

                    "mapped" => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Persorg::class,
        ]);
    }
}
