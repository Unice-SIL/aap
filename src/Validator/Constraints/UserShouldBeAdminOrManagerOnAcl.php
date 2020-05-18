<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserShouldBeAdminOrManagerOnAcl extends Constraint
{
    public $message = 'app.widget.errors.validation.user_should_be_admin_or_manager';
}