<?php


namespace App\Widget\FormWidget;


use App\Entity\WidgetFile;
use App\Widget\AbstractWidget;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractFormWidget extends AbstractWidget implements FormWidgetInterface
{

    /** @var bool */
    protected $visibilityLabel = true;

    /** @var bool */
    protected $required = false;

    /** @var string|null */
    protected $placeholder;

    /** @var string|null */
    protected $style;

    /**
     * @var array
     * @Assert\Valid
     */
    protected $dynamicConstraints = [];

    /**
     * @return bool
     */
    public function getVisibilityLabel(): bool
    {
        return $this->visibilityLabel;
    }

    /**
     * @param bool $visibilityLabel
     */
    public function setVisibilityLabel(bool $visibilityLabel): void
    {
        $this->visibilityLabel = $visibilityLabel;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return string|null
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @param string|null $placeholder
     */
    public function setPlaceholder(?string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string|null
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * @param string|null $style
     */
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return             [
            'label' => $this->getLabel(),
            'required' => $this->isRequired(),
            'attr' => [
                'placeholder' => $this->getPlaceholder(),
                'style' => $this->getStyle()
            ],
            'constraints' => $this->getConstraints()
        ];
    }

    public function transformData($value, array $options = [])
    {
        return $value;
    }

    public function reverseTransformData($value, array $options = [])
    {
        return $value;
    }

    abstract public function getSymfonyType(): string;

    abstract public function getFormType(): string;

    public function getConstraints(): array
    {
        $constraints = [];
        if ($this->isRequired()) {
            $constraints['not_blank'] = new NotBlank();
        }

        $dynamicConstraints = array_filter($this->getDynamicConstraints(), function ($dynamicConstraint){
            return $dynamicConstraint->isActive();
        });

        $dynamicConstraints = array_map(function ($dynamicConstraint) {
            return $dynamicConstraint->getSymfonyConstraint();
        }, $dynamicConstraints);

        return array_merge($constraints, $dynamicConstraints);
    }


    /**
     * @return array
     */
    public function getDynamicConstraints(): array
    {
        return $this->dynamicConstraints;
    }

    /**
     * @param array $dynamicConstraints
     */
    public function setDynamicConstraints(array $dynamicConstraints): void
    {
        $this->dynamicConstraints = $dynamicConstraints;
    }

    public function getDynamicConstraintsType(): ?string
    {
        return null;
    }

    public function renderView($value): ?string
    {
        if ($value === null) {
            return 'app.project_content.form.no_communicate';
        }

        if ($value instanceof \DateTime) {

            if (isset($value->onlyDate)) {
                return $value->format('d-m-Y');
            }

            return $value->format('d-m-Y h:i');
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        if ($value instanceof WidgetFile) {
            return $value->getClientOriginalName();
        }
        return $value;
    }

    public function isFileWidget(): bool
    {
        return false;
    }
}