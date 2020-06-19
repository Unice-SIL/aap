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
        return $this->redirect($request->get('_target_path'));
    }
}