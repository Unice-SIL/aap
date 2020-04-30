<?php


namespace App\Widget\Constraint;


use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @AppAssert\PatternRequiredIfActive()
 */
class RegexConstraint extends AbstractConstraint
{
    /**
     * @var string|null
     * @Assert\Type(type={"string", "null"})
     */
    private $pattern;



    /**
     * @return string|null
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * @param string|null $pattern
     */
    public function setPattern(?string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'pattern' => $this->getPattern(),
        ];
    }

    public function getSymfonyConstraint()
    {
        return new Regex($this->getOptions());
    }

}