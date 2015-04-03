<?php

namespace SymfonyExtraBundle\Twig;

use Locale;
use NumberFormatter;
use Twig_Extension;
use Twig_SimpleFilter;

class LocaleExtension extends Twig_Extension
{

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('money', array($this, 'formatMoney')),
            new Twig_SimpleFilter('locale_strip_region', array($this, 'stripRegion')),
        );
    }

    public function formatMoney($value)
    {
        $formatter = new NumberFormatter(Locale::getDefault(), NumberFormatter::CURRENCY);
        return $formatter->format($value);
    }
    
    public function stripRegion($value)
    {
        $parts = explode('_', $value);
        return $parts[0];
    }
    
    public function getName()
    {
        return 'se_locale';
    }

}