<?php

namespace App\Manager\Dictionary;

use App\Entity\Dictionary;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineDictionaryManager extends AbstractDictionaryManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineDictionaryManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Dictionary $dictionary)
    {
        $this->em->persist($dictionary);
        $this->em->flush();
    }

    public function update(Dictionary $dictionary)
    {
        $this->em->flush();
    }

    public function delete(Dictionary $dictionary)
    {
        $this->em->remove($dictionary);
        $this->em->flush();
    }

}