<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app.homepage")
     * @return RedirectResponse
     */
    public function home()
    {
        return $this->redirectToRoute('app.call_of_project.index');
    }
}