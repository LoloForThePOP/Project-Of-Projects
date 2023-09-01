<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotContainsUrlOrEmailValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\NotContainsUrlOrEmail $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if(preg_match('/http|www/i', $value)) {

            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

        if(preg_match('/(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))/', $value)) {

            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}
