<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanyRequired extends Constraint
{
    public $message = 'Company is required for ROLE_USER and ROLE_COMPANY_ADMIN.';
}