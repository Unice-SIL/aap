<?php


namespace App\Controller\Admin;


use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GroupController
 * @package App\Controller\Admin
 * @Route("/group", name="group.")
 */
class GroupController extends AbstractController
{
    /**
     * @Route(name= "index")
     * @param GroupRepository $groupRepository
     */
    public function index(GroupRepository $groupRepository){

        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll()
        ]);
    }
}