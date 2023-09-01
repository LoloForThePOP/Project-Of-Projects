<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotContainsUrlOrEmail extends Constraint
{

    public $message = "Le texte ne doit pas contenir d'adresse web ou e-mail";
    
}
