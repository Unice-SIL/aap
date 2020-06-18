<?php


namespace App\Controller\Front;


use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller\Front
 * @Route("/notification" , name="notification.")
 */
class NotificationController extends AbstractController
{
    /**
     * @param Notification $notification
     * @param EntityManagerInterface $entityManager
     * @Route("/{id}/process", name="process", methods={"GET"})
     * @IsGranted(App\Security\NotificationVoter::PROCESS, subject="notification")
     */
    public function process(Notification $notification, EntityManagerInterface $entityManager)
    {

        $routeName = $notification->getRouteName();
        $routeParams = $notification->getRouteParams();

        $entityManager->remove($notification);
        $entityManager->flush();

        return $this->redirectToRoute($routeName, $routeParams);

    }
}