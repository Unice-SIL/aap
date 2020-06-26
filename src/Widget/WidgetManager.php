<?php


namespace App\Widget;


use App\Entity\Dictionary;
use App\Entity\Project;
use App\Entity\ProjectContent;
use App\Entity\ProjectFormWidget;
use App\Entity\WidgetFile;
use App\Form\Widget\DynamicWidgetsType;
use App\Utils\File\FileUploaderInterface;
use App\Widget\FormWidget\AbstractChoiceWidget;
use App\Widget\FormWidget\FormWidgetInterface;
use App\Widget\HtmlWidget\HtmlWidgetInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @var FileUploaderInterface
     */
    private $fileUploader;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * WidgetManager constructor.
     * @param FormFactoryInterface $formFactory
     * @param Environment $twig
     * @param FileUploaderInterface $fileUploader
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twig,
        FileUploaderInterface $fileUploader,
        EntityManagerInterface $entityManager
    )
    {
        $this->widgets = [];
        $this->formWidgets = [];
        $this->htmlWidgets = [];
        $this->formFactory = $formFactory;
        $this->twig = $twig;

        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
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

        $showOnlyActive = true;
        if (
            isset($dynamicForm->getConfig()->getOptions()['allWidgets'])
            and $dynamicForm->getConfig()->getOptions()['allWidgets']
        ) {
            $projectFormWidgets = $project->getCallOfProject()->getProjectFormLayout()->getAllProjectFormWidgets();
            $showOnlyActive = false;
        }

        return $this->twig->render($template,[
            'form' => $dynamicForm->createView(),
            'projectFormWidgets' => $projectFormWidgets,
            'showOnlyActive' => $showOnlyActive
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
            $content = $form->get($position)->getData();

            //Special case if File
            if ($projectFormWidget->getWidget()->isFileWidget()) {

            //If the checkbox "delete" is set to true
                $delete = $content['delete'];
                if ($delete) {
                    $projectContent->setContent(null);
                    continue;
                }

                $file = $content['file'];
                //If the project is in edition with an existing file, not upload a file should not remove the existing one.
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $content = $file;

            }

            $projectContent->setContent($content);

        }

    }

    public function getFileWidgets() {

        $fileFormWidgets = array_filter($this->getFormWidgets(), function ($formWidget) {
            return $formWidget->isFileWidget();
        });

        return array_keys($fileFormWidgets);
    }

    public function extractWidget(ProjectFormWidget $projectFormWidget)
    {
        $widget = $projectFormWidget->getWidget();

        if ($widget instanceof AbstractChoiceWidget and $widget->getDictionary()) {
            $dictionary = $this->entityManager->find(Dictionary::class, $widget->getDictionary()->getId());
            $widget->setDictionary($dictionary);
        }

        return $widget;
    }
}