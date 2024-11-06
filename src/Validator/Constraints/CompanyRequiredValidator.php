<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CompanyRequiredValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $user = $this->context->getObject();

        if (in_array($user->getRoles()[0], [User::ROLE_USER, User::ROLE_COMPANY_ADMIN]) && empty($user->getCompany())) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}