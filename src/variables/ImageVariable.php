<?php

namespace mission10\images\variables;
use Craft;

Class ImageVariable
{
    
    public static function getTransform( $handle )
    {
        return is_string( $handle )
            ? Craft::$app->assetTransforms->getTransformByHandle( $handle )
            : $handle;
    }
    
    public static function getDesktopTransform( $options = null )
    {
        if( !is_null($options) )
        {
            if( array_key_exists('transform', $options) )
            {
                if( is_string($options['transform']) )
                {
                    return static::getTransform($options['transform']);
                }
                return $options['transform'];
            }
        }
        return null;
    }
    
    public static function getMobileTransform( $options = null )
    {
        $transform = null;
        if( !is_null($options) )
        {
            if( array_key_exists('mobileTransform', $options) )
            {
                if( is_string($options['mobileTransform']) )
                {
                    $transform = static::getTransform($options['mobileTransform']);
                }
                else
                {
                    $transform = $options['mobileTransform'];
                }
            }
            elseif( array_key_exists('transform', $options) )
            {
                if( is_string($options['transform']) )
                {
                    $transform = static::getTransform($options['transform'] . 'Mobile');
                }
            }
        }
        return $transform;
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
            'transform' => static::getDesktopTransform($options),
            'mobileTransform' => static::getMobileTransform($options),
            'fallback' => $options['fallback'] ?? null,
            'class' => $options['class'] ?? null,
            'alt' => $options['alt'] ?? null,
            'style' => $options['style'] ?? null,
            'attributes' => $options['attributes'] ?? [],
            'pictureClass' => $options['picture-class'] ?? null,
            'lazyLoading' => $options['lazyLoading'] ?? true,
            'dimensions' => array_key_exists('dimensions', $options) ? $options['dimensions'] : true,
        ]);
    }
    
    public function url($field = null, $options = null)
    {
        if( is_array($field) )
        {
            $options = $field;
            $field = null;
        }
        if( !is_null($options) && array_key_exists('field', $options) )
        {
            $field = $options['field'];
        }
        if(!is_null($field) && !is_null($options) && array_key_exists('transform', $options))
        {
            if( !($field instanceof \craft\elements\Asset) )
            {
                $field = $field->one();
            }
            if( Craft::$app->images->supportsWebP && !(array_key_exists('format', $options['transform'])))
            {
                $options['transform']['format'] = 'webp';
            }
            return $field->setTransform($options['transform'])->url;
        }
        elseif(!is_null($field))
        {
            return $field->url;
        }
        elseif( array_key_exists('fallback', $options))
        {
            return $options['fallback'];
        }
        return '';
    }
    
}
