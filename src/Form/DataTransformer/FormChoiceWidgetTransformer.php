<?php


namespace App\Form\DataTransformer;

use App\Widget\FormWidget\AbstractChoiceWidget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class FormChoiceWidgetTransformer implements DataTransformerInterface
{
    private $em;

    /**
     * BaseAclTransformer constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        /** @var AbstractChoiceWidget $widget */
        $widget = $value;
        if ($dictionary = $widget->getDictionary()) {

            $widget->setChoices($dictionary->getDictionaryContents()->map(function ($dc) {
                return $dc->getValue();
            })->toArray());
        }


        return $value;
    }
}