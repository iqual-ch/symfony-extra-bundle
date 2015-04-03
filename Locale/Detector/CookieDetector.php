<?php
namespace SymfonyExtraBundle\Locale\Detector;

use Symfony\Component\HttpFoundation\Request;

class CookieDetector extends AbstractDetector
{
    const DEFAULT_PARAM_NAME = 'lang';
    
    /**
     * @var string
     */
    protected $param = self::DEFAULT_PARAM_NAME;

    /**
     * @param string $param
     */
    public function setParam($param) 
    {
        $this->param = $param;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function detect(Request $request)
    {
        if ($request->cookies->has($this->param)) {
            return $request->cookies->get($this->param);
        }
    }
    
    public function getName()
    {
        return 'cookie';
    }

}