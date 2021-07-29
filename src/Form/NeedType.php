<?php

namespace App\Form;

use App\Entity\Need;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\IsFalse;

class NeedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type', 
                ChoiceType::class,
                [
                    'label' => 'Type du besoin',
                    'choices'  => [
                        'Une compétence (ex : un développeur; un électricien)' => 'skill',
                        'Un service ponctuel (ex : préparer un repas; créer deux dessins, )' => 'task',
                        'Un objet, un outil, du matériel (ex : une perçeuse)' => 'material',
                        'Un local, un terrain, une surface' => 'area',
                        "Un conseil" => 'advice',
                        "Une somme d'argent" => 'money',
                        'Autres' => 'other',
                    ],
                    'required'   => False,
                    'attr' => [

                        'placeholder'    => 'Choisir une option',
                    ],
                ]
            )
            ->add(
                'title', 
                TextType::class,
                [

                    'label' => 'Titre du besoin',
                    'required'   => true,
                    'attr' => [

                        'placeholder'    => "Exemple : Un local à Paris",
                    ],
                ]
            )
            ->add(
                'isPaid', 
                ChoiceType::class,
                [

                    'label' => 'Est-ce payé ?',
                    'placeholder'    => "Choisir une option",
                    'choices'  => [
                        'Peut-être, à voir' => 'maybe',
                        'Oui' => 'yes',
                        'Non' => 'no',
                    ],
                    'required'   => false,
                ]
            )
            ->add(
                'description', 
                TextareaType::class,
                [

                    'label' => 'Description',
                    'required'   => false,
                    'attr' => [

                        'placeholder'    => "Exemple : Nous recherchons un local pour pouvoir développer notre projet. L'idéal serait 30 m² au minimum, si possible à une distance raisonnable du centre ville.",

                        'rows' => '7',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Need::class,
        ]);
    }
}
