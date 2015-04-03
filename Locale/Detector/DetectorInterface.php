<?php
namespace SymfonyExtraBundle\Locale\Detector;

use Symfony\Component\HttpFoundation\Request;

interface DetectorInterface
{
    /**
     * @param Request $request
     * @return string|null
     */
    public function detect(Request $request);
    
    /**
     * @return string
     */
    public function getName();
}