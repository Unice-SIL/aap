<?php


namespace App\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class AuthenticatorController extends AbstractController
{
    /**
     * @param Request $request
     * @param RouterInterface $router
     * @return RedirectResponse
     * @Route("/shibboleth-login", name="shibboleth_login")
     */
    public function shibbolethAuthentication(Request $request, RouterInterface $router)
    {
        $target = $request->get('_target_path', $router->generate('app.homepage'));
        return $this->redirect($target);
    }
}