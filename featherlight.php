<?php
namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Page\Page;

class FeatherlightPlugin extends Plugin
{
    protected $active = false;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPageInitialized' => ['onPageInitialized', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPageInitialized()
    {
        $defaults = (array) $this->config->get('plugins.featherlight');

        /** @var Page $page */
        $page = $this->grav['page'];
        if (isset($page->header()->featherlight)) {
            $this->config->set('plugins.featherlight', array_merge($defaults, $page->header()->featherlight));
        }

        // take the old legacy `lightbox: true` setting into account
        if (isset($page->header()->lightbox) && $page->header()->lightbox == true) {
            $legacy = true;
        } else {
            $legacy = false;
        }

        $this->active = $this->config->get('plugins.featherlight.active') || $legacy;
    }

    /**
     * if enabled on this page, load the JS + CSS theme.
     */
    public function onTwigSiteVariables()
    {
        if ($this->active) {

            $config = $this->config->get('plugins.featherlight');

            $init = "$(document).ready(function() {
                        $('a[rel=\"lightbox\"]').featherlight({
                            openSpeed: {$config['openSpeed']},
                            closeSpeed: {$config['closeSpeed']},
                            closeOnClick: '{$config['closeOnClick']}'
                        });
                     });";

            $this->grav['assets']->addCss('plugin://featherlight/css/featherlight.min.css');
            $this->grav['assets']->addJs('plugin://featherlight/js/featherlight.min.js');
            $this->grav['assets']->addInlineJs($init);
        }
    }
}
