<?php


namespace App\Widget\Constraint;


use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * @AppAssert\ValueRequiredIfActive()
 */
class LessThanOrEqualConstraint extends AbstractConstraint
{
    /**
     * @var integer|null
     * @Assert\Type(type={"integer", "null"})
     */
    private $value;

    /**
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int|null $value
     */
    public function setValue(?int $value): void
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'value' => $this->getValue(),
        ];
    }

    public function getSymfonyConstraint()
    {
        return new LessThanOrEqual($this->getOptions());
    }

}