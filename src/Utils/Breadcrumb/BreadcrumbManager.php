<?php


namespace App\Utils\Breadcrumb;


use Twig\Environment;
use Twig\Markup;

class BreadcrumbManager
{
    const TEMPATE_DEFAULT = 'partial/breadcrumb/_adminLTE3.html.twig';

    const BREADCRUMB_MAIN = 'main';

    /**
     * @var Environment
     */
    private $twigEngine;

    /** @var array */
    private $breadcrumbs = [];

    /**
     * WrapperRuntime constructor.
     * @param Environment $twigEngine
     */
    public function __construct(Environment $twigEngine)
    {
        $this->twigEngine = $twigEngine;
    }

    public function renderBreadcrumb(string $id)
    {
        /** @var Breadcrumb $breadcrumb */
        $breadcrumb = $this->getBreadcrumb($id);

        if (!$breadcrumb->getTemplate()) {
            $breadcrumb->setTemplate(self::TEMPATE_DEFAULT);
        }

        //todo: check label injections

        return new Markup($this->twigEngine->render($breadcrumb->getTemplate(), [
            'items' => $breadcrumb->getItems()
        ]), 'UTF-8');
    }

    /**
     * @param string $id
     * @return Breadcrumb
     * @throws \Exception
     */
    public function getBreadcrumb(string $id): Breadcrumb
    {
        if (!isset($this->breadcrumbs[$id])) {
            throw new \Exception('This breadcrumb doesn\'t exist');
        }

        return $this->breadcrumbs[$id];
    }

    /**
     * @param string $id
     * @param Breadcrumb $breadcrumb
     * @return $this
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb): self
    {
        $this->breadcrumbs[$breadcrumb->getId()] = $breadcrumb;

        return $this;
    }

    /**
     * @param Breadcrumb $breadcrumb
     * @return $this
     */
    public function removeBreadcrumb(Breadcrumb $breadcrumb): self
    {
        if (in_array($breadcrumb, $this->breadcrumbs)) {
            unset($this->breadcrumbs[array_search($breadcrumb, $this->breadcrumbs)]);
        }

        return $this;
    }

}