<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class MobileBlockListener
{
    private RequestStack $requestStack;
    private RouterInterface $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $userAgent = $request->headers->get('User-Agent');
        $currentRoute = $request->attributes->get('_route');


        if ($this->isMobile($userAgent) && in_array($currentRoute,
                    ['sortie_creer',
                    'sortie_publier',
                    'sortie_modifier',
                    'sortie_supprimer',
                    'groupe_prive/creer'],
                true))
        {
            $response = new Response("Accès interdit depuis un smartphone.", Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }


        if ($this->isMobile($userAgent)) {
            $request->getSession()->set('is_mobile', true);
        } else {
            $request->getSession()->set('is_mobile', false);
        }
    }






    private function isMobile(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        return preg_match('/(android|iphone|ipad|mobile|windows phone|blackberry)/i', $userAgent); //la ou met les tip de tel
    }
}

