<?php

namespace App\Manager\Dictionary;

use App\Entity\Dictionary;
use App\Event\AddProjectEvent;
use App\Event\UpdateDictionaryEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DoctrineDictionaryManager extends AbstractDictionaryManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * DoctrineDictionaryManager constructor.
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function save(Dictionary $dictionary)
    {
        $this->em->persist($dictionary);
        $this->em->flush();
    }

    public function update(Dictionary $dictionary)
    {
        $event = new UpdateDictionaryEvent($dictionary);
        $this->dispatcher->dispatch($event, UpdateDictionaryEvent::NAME);
        $this->em->flush();
    }

    public function delete(Dictionary $dictionary)
    {
        $this->em->remove($dictionary);
        $this->em->flush();
    }

}