<?php


namespace App\Utils\Breadcrumb;


class BreadcrumbItem
{
    /** @var string|null */
    private $label;

    /** @var string|null */
    private $link;

    /**
     * BreadcrumbItem constructor.
     * @param string|null $label
     * @param string|null $link
     */
    public function __construct(?string $label, ?string $link)
    {
        $this->label = $label;
        $this->link = $link;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }


}