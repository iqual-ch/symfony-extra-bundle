<?php
namespace SymfonyExtraBundle\Locale;

use Symfony\Component\HttpFoundation\Request;
use SymfonyExtraBundle\Locale\Detector\AbstractDetector;
use SymfonyExtraBundle\Locale\Detector\DetectorInterface;

class LocaleManager
{
    /**
     * @var string
     */
    protected $fallbackLocale;
    
    /**
     * @var array
     */
    protected $managedLocales;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var AbstractDetector[]
     */
    protected $detectors;
    
    /**
     * Cached locale.
     *  
     * @var string
     */
    protected $locale;
    
    /**
     * @param string $fallbackLocale
     * @param array $managedLocales
     * @param Request $request
     */
    public function __construct($fallbackLocale, array $managedLocales, Request $request)
    {
        $this->fallbackLocale = $fallbackLocale;
        $this->managedLocales = $managedLocales;
        $this->request = $request;
        $this->addDefaultDetectors();
    }
    
    /**
     * @param DetectorInterface $detector
     */
    public function addDetector(DetectorInterface $detector)
    {
        $this->detectors[$detector->getName()] = $detector;
    }
    
    /**
     * @param string $name
     */
    public function removeDetector($name)
    {
        if ($this->hasDetector($name)) {
            $this->detectors[$name] = null;
            unset($this->detectors[$name]);
        }
    }
    
    /**
     * @param string $name
     * @return bool
     */
    public function hasDetector($name)
    {
        return isset($this->detectors[$name]);
    }
    
    /**
     * @param bool $force Force locale redetection
     * @return string
     */
    public function getLocale($force = false)
    {
        if (!$force) {
            if (null !== $this->locale) {
                return $this->locale;
            }
        }
        
        $locale = $this->fallbackLocale;
        foreach ($this->detectors as $detector) {
            $detectedLocale = $detector->detect($this->request);
            if ($this->isManaged($detectedLocale)) {
                $locale = $detectedLocale;
                break;
            }
        }
        $this->locale = $locale;
        return $this->locale;
    }
    
    /**
     * @param string $locale
     * @return bool
     */
    public function isManaged($locale)
    {
        return in_array($locale, $this->managedLocales);
    }
    
    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->request->setLocale($locale);
        $this->request->getSession()->set('_locale', $locale);
    }
    
    public function addDefaultDetectors()
    {
        $this->addDetector(new Detector\QueryDetector);
        $this->addDetector(new Detector\CookieDetector);
        $this->addDetector(new Detector\AcceptHeaderDetector);
    }
}