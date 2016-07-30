<?php
namespace SymfonyExtraBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use SymfonyExtraBundle\Locale\LocaleManager;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var LocaleManager
     */
    protected $localeManager;
    
    /**
     * @param LocaleManager $localeManager
     * @param boolean       $forceUsingLocaleManager  Use the locale manager even in CLI env
     *                                                (e.g. for Unit Tests)
     * @return void
     */
    public function __construct(LocaleManager $localeManager, $forceUsingLocaleManager = false)
    {
        if (PHP_SAPI == 'cli' && !$forceUsingLocaleManager) {
            // do not manage command line locales
            return;
        }

        $this->localeManager = $localeManager;
    }
    
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $locale = $this->localeManager->getLocale();
        $this->localeManager->setLocale($locale);
    }

    /**
     * @param FilterResponseEvent $event
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }
        $event->getResponse()->headers->setCookie(new Cookie('lang', $this->localeManager->getLocale(), 'now + 1 year'));
    }
    
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 100)),
            KernelEvents::RESPONSE => array(array('onKernelResponse')),
        );
    }
}