<?php
namespace SymfonyExtraBundle\Locale\Detector;

use Symfony\Component\HttpFoundation\Request;

class QueryDetector extends AbstractDetector
{
    const DEFAULT_PARAM_NAME = '_locale';
    
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
        if ($request->query->has($this->param)) {
            return $request->query->get($this->param);
        }
    }
    
    public function getName()
    {
        return 'query';
    }

}