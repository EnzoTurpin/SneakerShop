<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session; // Ajouté

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
         $this->urlGenerator = $urlGenerator;
    }
    
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $session = $request->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add('error', "Vous n'avez pas l'accès suffisant pour accéder à cette page.");
        }
        
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}