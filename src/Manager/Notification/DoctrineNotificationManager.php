<?php

namespace App\Manager\Notification;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineNotificationManager extends AbstractNotificationManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineNotificationManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Notification $notification)
    {
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function update(Notification $notification)
    {
        $this->em->flush();
    }

    public function delete(Notification $notification)
    {
        $this->em->remove($notification);
        $this->em->flush();
    }

}