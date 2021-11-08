<?php

namespace jordanbeattie\images;
use \craft\web\View;
use \craft\events\RegisterTemplateRootsEvent;
use jordanbeattie\images\variables\ImageVariable;
use \craft\web\twig\variables\CraftVariable;

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
                $event->roots['images'] = __DIR__ . '/available-templates';
            }
        );
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('images', ImageVariable::class);
            }
        );
    }
}
