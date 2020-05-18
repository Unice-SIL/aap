<?php

namespace App\Validator\Constraints;

use App\Entity\Acl;
use App\Entity\OrganizingCenter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class UserShouldBeAdminOrManagerOnAclValidator extends ConstraintValidator
{
    /**
     * @var Security
     */
    private $security;

    /**
     * UserShouldBeAdminOrManagerOnAclValidator constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof UserShouldBeAdminOrManagerOnAcl) {
            throw new UnexpectedTypeException($constraint, UserShouldBeAdminOrManagerOnAcl::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof OrganizingCenter) {
            throw new UnexpectedValueException(
                'The value should be an instance of ' . OrganizingCenter::class .
            ', "' . gettype($value) . '" given.'
            );
        }

        $userPermissions = $value->getAcls()
            ->filter(function ($acl) {
                return $acl->getUser() === $this->security->getUser();
            })
            ->map(function ($acl) {
                return $acl->getPermission();
            });

        //todo: change by voter
        if (count(array_intersect($userPermissions->toArray(), Acl::EDITOR_PERMISSIONS)) == 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
