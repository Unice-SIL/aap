<?php


namespace App\Utils\Breadcrumb;


class Breadcrumb
{
    /** @var string */
    private $id;

    /** @var array */
    private $items = [];

    /** @var string|null */
    private $template;

    /**
     * Breadcrumb constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BreadcrumbItem $breadcrumbItem
     * @return $this
     */
    public function addItem(BreadcrumbItem $breadcrumbItem): self
    {
        $this->items[] = $breadcrumbItem;

        return $this;
    }

    /**
     * @param BreadcrumbItem $breadcrumbItem
     * @return $this
     */
    public function removeItem(BreadcrumbItem $breadcrumbItem): self
    {
        if (in_array($breadcrumbItem, $this->items)) {
            unset($this->items[array_search($breadcrumbItem, $this->items)]);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     * @return Breadcrumb
     */
    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

}