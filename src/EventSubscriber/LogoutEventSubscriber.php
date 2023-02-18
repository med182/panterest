<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogoutEventSubscriber implements EventSubscriberInterface
{


    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {

        $this->urlGenerator = $urlGenerator;
    }
    public function onLogoutEvent(LogoutEvent $event): void
    {
        $event->getRequest()->getSession()->getFlashBag()->add(
            'success',
            'Bye Bye' . ' ' . $event->getToken()->getUser()->getFullName()
        );
        $event->getResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
