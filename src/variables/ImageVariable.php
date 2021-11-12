<?php

namespace jordanbeattie\images\variables;
use Craft;

Class ImageVariable
{
    
    public function transformExists( $handle )
    {
        return Craft::$app->assetTransforms->getTransformByHandle( $handle );
    }
    
    public function mobileTransformExists( $handle )
    {
        return Craft::$app->assetTransforms->getTransformByHandle( $handle . "Mobile" );
    }
    
    public function render($field = null, $options = null)
    {
        if( is_array($field) )
        {
            $options = $field;
            $field = null;
        }
        echo Craft::$app->view->renderTemplate('images/img', [
            'field' => $field ?? ($options['field'] ?? null),
            'transform' => $options['transform'] ?? null,
            'fallback' => $options['fallback'] ?? null,
            'class' => $options['class'] ?? null,
            'alt' => $options['alt'] ?? null,
            'style' => $options['style'] ?? null,
            'attributes' => $options['attributes'] ?? null,
        ]);
    }
    
}
