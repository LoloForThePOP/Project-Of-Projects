<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


/**
 * Same Form as RefistrationFormType except we do no ask for a username.
 */
class WithoutUsernameRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('email', EmailType::class, [
                'label' => "Écrire ici votre adresse e-mail",
                'attr' => ['placeholder' => 'Votre adresse e-mail'],

                'constraints' => array(
                    new Email(['message' => 'Veuillez écrire une adresse e-mail valide']),
                )
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => "Créer votre mot de passe",
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Créer ici votre mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Écrire un mot de passe',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);

        /*  ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "Accepter les conditions d'utilisation",
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]) */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}
