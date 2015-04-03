<?php
namespace SymfonyExtraBundle\Locale\Detector;

use Symfony\Component\HttpFoundation\Request;

class AcceptHeaderDetector extends AbstractDetector
{

    /**
     * @param Request $request
     * @return string
     */
    public function detect(Request $request)
    {
        if ($request->server->has('HTTP_ACCEPT_LANGUAGE')) {
            return current($this->getLocalesFromAcceptHeader($request->server->get('HTTP_ACCEPT_LANGUAGE')));
        }
    }

    public function getLocalesFromAcceptHeader($header)
    {
        $locales = array();
        $sources = explode(';', $header);
        
        $filter = function ($value) use (&$locales) {
            /**
             * $parts could be in the following formats:
             * ['ru', 'en-US'] or ['q=0.8', 'en']
             */
            $parts = explode(',', $value);
            
            // when $parts[0] == 'en' and not 'q=0.8'
            if (strlen($parts[0]) == 2) {
                $locales[] = $parts[0];
            }
            
            // when $parts[1] == 'en-US' or 'en'
            if (isset($parts[1])) {
                if (strlen($parts[1]) == 2) {// just "en"
                    $locales[] = $parts[1];
                } else if (strstr($parts[1], '-') !== false) { // when "en-US"
                    $locales[] = substr($parts[1], 0, strpos($parts[1], '-'));
                }
            }
        };
        
        array_map($filter, $sources);
        return array_unique($locales);
    }
    
    
    public function getName()
    {
        return 'accept-header';
    }
    
}