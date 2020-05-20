<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserShouldBeAdminOrManagerOnOrganizingCenter extends Constraint
{
    public $message = 'app.errors.validation.user_should_be_admin_or_manager';
}