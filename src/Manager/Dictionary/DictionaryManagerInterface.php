<?php

namespace App\Manager\Dictionary;


use App\Entity\Dictionary;

interface DictionaryManagerInterface
{
    public function create(): Dictionary;

    public function save(Dictionary $dictionary);

    public function update(Dictionary $dictionary);

    public function delete(Dictionary $dictionary);
}