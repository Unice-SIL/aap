<?php


namespace App\Manager\Dictionary;


use App\Entity\Dictionary;

abstract class AbstractDictionaryManager implements DictionaryManagerInterface
{

    public function create(): Dictionary
    {
        return new Dictionary();
    }

    public abstract function save(Dictionary $dictionary);

    public abstract function update(Dictionary $dictionary);

    public abstract function delete(Dictionary $dictionary);


}