<?php


namespace App\Widget;


use App\Entity\ProjectFormLayout;
use App\Form\Widget\DynamicWidgetsType;
use App\Widget\FormWidget\FormWidgetInterface;
use App\Widget\HtmlWidget\HtmlWidgetInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

class WidgetManager
{
    private $widgets;
    private $formWidgets;
    private $htmlWidgets;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $twig;

    /**
     * WidgetManager constructor.
     * @param FormFactoryInterface $formFactory
     * @param Environment $twig
     */
    public function __construct(FormFactoryInterface $formFactory, Environment $twig)
    {
        $this->widgets = [];
        $this->formWidgets = [];
        $this->htmlWidgets = [];
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function addWidget(WidgetInterface $widget)
    {
        $this->widgets[$widget->getName()] = $widget;
    }

    /**
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function getWidget(?string $name): ?WidgetInterface
    {
        if (isset($this->getWidgets()[$name])) {
            return $this->getWidgets()[$name];
        }

        return null;
    }

    public function addFormWidget(FormWidgetInterface $formWidget)
    {
        $this->formWidgets[$formWidget->getName()] = $formWidget;
    }

    /**
     * @return array
     */
    public function getFormWidgets(): array
    {
        return $this->formWidgets;
    }

    public function addHtmlWidget(HtmlWidgetInterface $htmlWidget)
    {
        $this->htmlWidgets[$htmlWidget->getName()] = $htmlWidget;
    }

    /**
     * @return array
     */
    public function getHtmlWidgets(): array
    {
        return $this->htmlWidgets;
    }

    public function getDynamicForm(ProjectFormLayout $projectFormLayout): FormInterface
    {
        return  $this->formFactory->create(DynamicWidgetsType::class, null, [
            'project_form_layout' => $projectFormLayout
        ]);
    }

    public function renderDynamicFormHtml(FormInterface $dynamicForm)
    {
        if (
            !isset($dynamicForm->getConfig()->getOptions()['project_form_layout'])
            or !$dynamicForm->getConfig()->getOptions()['project_form_layout'] instanceof ProjectFormLayout
        ) {
            throw new \Exception('You should set a ' . ProjectFormLayout::class . ' instance as 
            "project_form_layout option of the given form');
        }

        /** @var ProjectFormLayout $projectFormLayout */
        $projectFormLayout = $dynamicForm->getConfig()->getOptions()['project_form_layout'];

        $projectFormWidgets = $projectFormLayout->getProjectFormWidgets()->getIterator();

        $projectFormWidgets->uasort(function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });


        return $this->twig->render('partial/widget/_dynamic_form.html.twig', [
            'form' => $dynamicForm->createView(),
            'projectFormWidgets' => $projectFormWidgets
        ]);
    }
}