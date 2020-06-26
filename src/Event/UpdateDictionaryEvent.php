<?php


namespace App\Event;


use App\Entity\Dictionary;
use Symfony\Contracts\EventDispatcher\Event;

class UpdateDictionaryEvent extends Event
{
    const NAME = 'update_dictionary';
    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * UpdateDictionaryEvent constructor.
     * @param Dictionary $dictionary
     */
    public function __construct(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary(): Dictionary
    {
        return $this->dictionary;
    }

}