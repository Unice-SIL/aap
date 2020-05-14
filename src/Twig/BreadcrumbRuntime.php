<?php


namespace App\Twig;


use App\Utils\Breadcrumb\BreadcrumbManager;
use Twig\Extension\RuntimeExtensionInterface;

class BreadcrumbRuntime implements RuntimeExtensionInterface
{
    /**
     * @var BreadcrumbManager
     */
    private $breadcrumb;


    /**
     * BreadcrumbRuntime constructor.
     * @param BreadcrumbManager $breadcrumb
     */
    public function __construct(BreadcrumbManager $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function renderBreadcrumb(string $id)
    {
        return $this->breadcrumb->renderBreadcrumb($id);
    }
}