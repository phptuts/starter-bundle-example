<?php

namespace AppBundle\Controller;

use StarterKit\StartBundle\Service\AuthResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function logoutAction(Request $request)
    {
        $request->cookies->remove(AuthResponseService::AUTH_COOKIE);

        return $this->redirectToRoute('homepage');
    }
}