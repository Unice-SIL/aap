<?php

namespace App\Validator\Constraints;

use App\Entity\OrganizingCenter;
use App\Security\OrganizingCenterVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class UserShouldBeAdminOrManagerOnOrganizingCenterValidator extends ConstraintValidator
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof UserShouldBeAdminOrManagerOnOrganizingCenter) {
            throw new UnexpectedTypeException($constraint, UserShouldBeAdminOrManagerOnOrganizingCenter::class);
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

        /** @var OrganizingCenter $organizingCenter */
        $organizingCenter = $value;

        if (!$this->authorizationChecker->isGranted(OrganizingCenterVoter::EDIT, $organizingCenter)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
