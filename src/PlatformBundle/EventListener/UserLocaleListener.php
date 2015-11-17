<?php

namespace PlatformBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class UserLocaleListener implements EventSubscriberInterface
{
    private $session;
    private $defaultLocale;
    private $supportedLocales;

    public function __construct(Session $session, $defaultLocale = 'en', array $supportedLocales = array('en'))
    {
        $this->session = $session;
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
    }

    public function isLocaleSupported($locale) {
        return in_array($locale, $this->supportedLocales);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->query->get('locale');
        $sessionLocale = $session->get('_locale', $this->defaultLocale);

        if ($locale === null || !$this->isLocaleSupported($locale)) {
            $locale = $sessionLocale;
        }

        if ($sessionLocale !== $locale){
            $session->set('_locale', $locale);
        }

        $request->setLocale($locale);
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
