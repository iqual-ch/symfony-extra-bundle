<?php

namespace SymfonyExtraBundle\SwiftmailerExtension\Plugin;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\StringAsset;
use Assetic\AssetManager;
use Assetic\FilterManager;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;
use Symfony\Component\HttpKernel\Kernel;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Plugin merges CSS into inline "style" blocks in html.
 * Gmail does not display styles from <style /> tag.
 * 
 * How to enable:
 * add the following lines to your service config:
 * 
 * swiftmailer.mailer.plugin.csstoinline:
 *     class: SymfonyExtraBundle\SwiftmailerExtension\Plugin\CssToInlinePlugin
 *     arguments: [@assetic.asset_manager, @assetic.filter_manager, @kernel]
 *     tags:
 *         - { name: swiftmailer.default.plugin }
 * 
 * NOTE: swiftmailer.default.plugin <-- default here is a mailer name!
 * 
 * @author Alex Oleshkevich <alex.oleshkevich@muehlemann-popp.ch>
 * @requires assetic
 * @requires tijsverkoyen/css-to-inline-styles
 */
class CssToInlinePlugin implements Swift_Events_SendListener
{

    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $cssFile;
    
    /**
     * @param AssetManager $assetManager
     * @param FilterManager $filterManager
     * @param Kernel $kernel
     */
    public function __construct(AssetManager $assetManager, FilterManager $filterManager, Kernel $kernel, $cssFile = null)
    {
        $this->assetManager = $assetManager;
        $this->filterManager = $filterManager;
        $this->kernel = $kernel;
        $this->cssFile = $cssFile;
    }

    /**
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        if (!$this->cssFile) {
            return;
        }
        
        $lessFile = $this->kernel->locateResource($this->cssFile);
        $css = $this->compileLess($lessFile);

        $message = $evt->getMessage();
        $converter = new CssToInlineStyles($message->getBody(), $css);
        $converter->setCleanup();
        $converted = $converter->convert();
        $message->setBody($converted);
    }

    /**
     * Not used.
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        
    }

    /**
     * Compiles less into css.
     * 
     * @param string $lessFile Absolute path to LESS file
     * @return string 
     */
    protected function compileLess($lessFile)
    {
        $content = file_get_contents($lessFile);
        $this->assetManager->set('styles', new StringAsset($content, array(), dirname($lessFile), basename($lessFile)));

        $resource = new AssetCollection(array(
            new AssetReference($this->assetManager, 'styles'),
        ));

        /* @var $filterManager FilterManager */
        $resource->ensureFilter($this->filterManager->get('less'));
        $resource->load();
        return $resource->dump();
    }

}
