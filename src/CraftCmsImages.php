<?php

namespace Jordanbeattie\Images;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use yii\base\Event;

class CraftCmsImages extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['jbimage'] = __DIR__ . '/available-templates';
            }
        );
    }
}
