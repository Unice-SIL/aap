<?php


namespace App\EventSubscriber;


use App\Entity\ProjectFormWidget;
use App\Event\UpdateDictionaryEvent;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DictionarySubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var WidgetManager
     */
    private $widgetManager;


    /**
     * DictionarySubscriber constructor.
     * @param EntityManagerInterface $em
     * @param WidgetManager $widgetManager
     */
    public function __construct(EntityManagerInterface $em, WidgetManager $widgetManager)
    {
        $this->em = $em;
        $this->widgetManager = $widgetManager;
    }

    public static function getSubscribedEvents()
    {
        return [
              UpdateDictionaryEvent::NAME => 'onUpdateDictionary'
        ];
    }

    public function onUpdateDictionary(UpdateDictionaryEvent $event)
    {
        $dictionary = $event->getDictionary();

        $choiceTypeProjectFormWidgets = $this->em->getRepository(ProjectFormWidget::class)->findByWidgetClass(
            $this->widgetManager->getChoiceTypeWidget()
        );

        $i = 1;
        foreach ($choiceTypeProjectFormWidgets as $choiceTypeProjectFormWidget) {
            $widget = $choiceTypeProjectFormWidget->getWidget();
            if ($dictionaryWidgeet = $widget->getDictionary()
            and $dictionary->getId() === $dictionaryWidgeet->getId()) {

                $widget->setChoices(
                    $dictionary->getDictionaryContents()->map(function ($dc) {
                        return $dc->getValue();
                    })
                    ->toArray()
                );
                $choiceTypeProjectFormWidget->setWidget($widget);
            }

        }
    }
}