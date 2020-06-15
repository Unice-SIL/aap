<?php


namespace App\Controller\Front;


use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InvitationController
 * @package App\Controller\Front
 * @Route("/invitation", name="invitation.")
 */
class InvitationController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route(name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager)
    {
        return $this->render('invitation/index.html.twig', [
            'invitations' => $entityManager->getRepository(Invitation::class)->findByCreatedBy($this->getUser()),
            'layout' => 'top_navbar_layout.html.twig'
        ]);
    }
}