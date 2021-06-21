<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class QuestionAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('question', TextType::class, 
                [
                    'label' => 'Question que des personnes vous posent',
                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici la question',
                    ],
                    'required'   => true,
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\NotBlank( 
                            [
                                'message' => 'Ce champ ne peut être vide'
                            ]
                        ),
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "min" => 2,
                                "max" => 2500,
                                "minMessage" => "La question doit contenir au minimum {{ limit }} caractères",
                                "maxMessage" => "La question doit contenir au maximum {{ limit }} caractères",
                            ]
                        ),
                    )
                ]
            )

            ->add('answer', TextareaType::class, 
                [
                    'label' => 'Votre réponse',
                    'attr' => [
                        
                        'placeholder'    => 'Écrire ici la réponse',
                    ],
                    'required'   => true,
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\NotBlank( 
                            [
                                'message' => 'Ce champ ne peut être vide'
                            ]
                        ),
                        new \Symfony\Component\Validator\Constraints\Length(
                            [
                                "min" => 2,
                                "max" => 2500,
                                "minMessage" => "La réponse doit contenir au minimum {{ limit }} caractères",
                                "maxMessage" => "La réponse doit contenir au maximum {{ limit }} caractères",
                            ]
                        ),

                    )
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
