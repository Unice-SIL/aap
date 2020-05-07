<?php


namespace App\Entity;


use Symfony\Component\HttpFoundation\File\File;

class WidgetFile extends File
{
    /**
     * @var string|null
     */
    private $clientOriginalName;

    /**
     * @return string|null
     */
    public function getClientOriginalName(): ?string
    {
        return $this->clientOriginalName;
    }

    /**
     * @param string|null $clientOriginalName
     */
    public function setClientOriginalName(?string $clientOriginalName): void
    {
        $this->clientOriginalName = $clientOriginalName;
    }

}