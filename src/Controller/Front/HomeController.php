<?php


namespace App\Controller\Front;

use App\Entity\CallOfProject;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function home(EntityManagerInterface $em)
    {

        return $this->render('page/dashboard.html.twig', [
            'call_of_projects' => $em->getRepository(CallOfProject::class)->getIfUserHasOnePermissionAtLeast(
                $this->getUser(),
                ['limit' => 5]
            ),
            'projects' => $em->getRepository(Project::class)->findBy(
                ['createdBy' => $this->getUser()],
                ['createdAt' => 'DESC'],
                5
            ),
        ]);
    }
}