<?php

function negotiateFormat($ep)
{

    // rex_media_manager object
    $subject = $ep->getSubject();

    // check if the requested image has the 'negotiator' effect 
    $set_effects = $subject->effectsFromType($subject->getMediaType());
    $set_effects = array_column($set_effects, 'effect');

    // if not, skip
    if (!in_array('negotiator', $set_effects)) {
        return $subject;
    } else {
        // if yes, set cache path
        $possible_types = rex_server('HTTP_ACCEPT', 'string', '');
        $types = explode(',', $possible_types);


        if (function_exists('imageavif') && in_array('image/avif', $types)) {
            $subject->setCachePath($subject->getCachePath() . 'avif-');
        } elseif (function_exists('imagewebp') && in_array('image/webp', $types)) {
            $subject->setCachePath($subject->getCachePath() . 'webp-');
        } else {
            $subject->setCachePath($subject->getCachePath() . 'default-');
        }
    }

    return $subject;
}

if (rex_addon::get('media_manager')->isAvailable()) {
    rex_media_manager::addEffect(rex_effect_negotiator::class);
}
rex_extension::register('MEDIA_MANAGER_BEFORE_SEND', "negotiateFormat",rex_extension::LATE);
