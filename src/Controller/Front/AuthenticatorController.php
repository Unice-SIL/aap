<?php


namespace App\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticatorController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/shibboleth-login", name="shibboleth_login")
     */
    public function shibbolethAuthentication(Request $request)
    {
        dd($this->getUser());
        return $this->redirect();
    }
}