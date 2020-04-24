<?php


namespace App\Widget;


use App\Entity\Project;
use App\Entity\ProjectContent;
use App\Entity\ProjectFormLayout;
use App\Form\Widget\DynamicWidgetsBisType;
use App\Form\Widget\DynamicWidgetsType;
use App\Widget\FormWidget\FormWidgetInterface;
use App\Widget\HtmlWidget\HtmlWidgetInterface;
use Doctrine\Common\Collections\Collection;
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


    public function getDynamicForm(Project $project): FormInterface
    {
        return  $this->formFactory->create(DynamicWidgetsType::class, $project);
    }

    public function renderDynamicFormHtml(FormInterface $dynamicForm, string $template)
    {
        $data = $dynamicForm->getData();
        if (!$data instanceof Project) {
            throw new \Exception('You should set a ' . Project::class . ' instance as 
            data of the given form');
        }

        /** @var Project $project */
        $project = $data;

        return $this->twig->render($template, [
            'form' => $dynamicForm->createView(),
            'projectFormWidgets' => $project->getCallOfProject()->getProjectFormLayout()->getProjectFormWidgets()
        ]);
    }

    public function hydrateProjectContentsByForm(Collection $projectContents, FormInterface $form)
    {
        foreach ($projectContents as $projectContent) {

            if (!$projectContent instanceof ProjectContent) {
                continue;
            }

            $projectFormWidget = $projectContent->getProjectFormWidget();
            $position = $projectFormWidget->getPosition();

            $data = $form->get($position)->getData();
            $content = $projectFormWidget->getWidget()->reverseTransformData($data);

            $projectContent->setContent($content);

        }

    }
}