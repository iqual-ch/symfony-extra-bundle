<?php
namespace SymfonyExtraBundle\Locale\Detector;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractDetector implements DetectorInterface
{
    abstract public function detect(Request $request);
}