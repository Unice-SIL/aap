<?php


namespace App\Widget\FormWidget;

use App\Form\Widget\FormWidget\FormRangeWidgetType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints as Assert;

class RangeWidget extends AbstractFormWidget implements FormWidgetInterface
{

    /**
     * @var integer
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     */
    private $min;

    /**
     * @var integer
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     */
    private $max;

    /**
     * @return int
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax(int $max): void
    {
        $this->max = $max;
    }


    public function getFormType(): string
    {
        return FormRangeWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return RangeType::class;
    }

    public function getOptions(): array
    {
        return array_merge_recursive(parent::getOptions(), [
            'attr' => [
                'min' => $this->getMin(),
                'max' => $this->getMax(),
            ]
        ]);
    }

    public function getConstraints(): array
    {
        if ($this->getMin() or $this->getMax()) {
            return array_merge(parent::getConstraints(), [
                new Range([
                    'min' => $this->getMin(),
                    'max' => $this->getMax(),
                ])
            ]);
        }

        return parent::getConstraints();
    }


}