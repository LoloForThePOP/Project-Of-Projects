<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class WebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'url',
                UrlType::class,
                [

                    'label' => 'Adresse du site',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => 'www.exemple.com',
                    ],

                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\NotBlank(['message' => 'Ce champ ne peut être vide']),
                        new \Symfony\Component\Validator\Constraints\Url(['message' => 'Vous devez utiliser une addresse web valide']),
                    )
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'Titre (facultatif)',
                    'attr' => [

                        'placeholder'    => 'Exemple : Notre site web',
                    ],
                    'required'   => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
