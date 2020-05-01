<?php


namespace App\Widget;


use App\Entity\Project;
use App\Entity\ProjectContent;
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

    /**
     * @return array
     */
    public function getWidgets(): array
    {
        return array_merge($this->getFormWidgets(), $this->getHtmlWidgets());
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
        if (!isset($this->formWidgetsSorted)) {
            $this->formWidgetsSorted = true;
            uasort($this->formWidgets, function ($a, $b){
                return $a->getPosition() <=> $b->getPosition();
            });
        }

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
        if (!isset($this->htmlWidgetsSorted)) {
            $this->htmlWidgetsSorted = true;
            uasort($this->htmlWidgets, function ($a, $b){
                return $a->getPosition() <=> $b->getPosition();
            });
        }

        return $this->htmlWidgets;
    }


    public function getDynamicForm(Project $project, array $options = []): FormInterface
    {
        return  $this->formFactory->create(DynamicWidgetsType::class, $project, $options);
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

        $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getProjectFormWidgets();

        if (
            isset($dynamicForm->getConfig()->getOptions()['allWidgets'])
            and $dynamicForm->getConfig()->getOptions()['allWidgets']
        ) {
            $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getAllProjectFormWidgets();
        }

        return $this->twig->render($template, [
            'form' => $dynamicForm->createView(),
            'projectFormWidgets' => $projectFormWidgets
        ]);
    }

    public function hydrateProjectContentsByForm(Collection $projectContents, FormInterface $form)
    {
        foreach ($projectContents as $projectContent) {

            if (!$projectContent instanceof ProjectContent) {
                continue;
            }


            $position = $projectContent->getProjectFormWidget()->getPosition();

            $content = $form->get($position)->getData();


            $projectContent->setContent($content);

        }

    }
}