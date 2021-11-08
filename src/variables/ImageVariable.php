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
    
}
