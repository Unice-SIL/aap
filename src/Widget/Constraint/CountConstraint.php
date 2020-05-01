<?php


namespace App\Widget\Constraint;


use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @AppAssert\MinOrMaxIfActive()
 */
class CountConstraint extends AbstractConstraint
{
    /**
     * @var integer|null
     * @Assert\Type(type={"integer", "null"})
     */
    private $min;

    /**
     * @var integer|null
     * @Assert\Type(type={"integer", "null"})
     */
    private $max;



    /**
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @param int|null $min
     */
    public function setMin(?int $min): void
    {
        $this->min = $min;
    }

    /**
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @param int|null $max
     */
    public function setMax(?int $max): void
    {
        $this->max = $max;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'min' => $this->getMin(),
            'max' => $this->getMax(),
        ];
    }

    public function getSymfonyConstraint()
    {
        return new Count($this->getOptions());
    }

}