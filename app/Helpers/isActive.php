<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('is Active')) {

    function isActive($href, $class = 'active')
    {
        return $class = (strpos(Route::currentRouteName(), $href) === 0 ? $class : '');
    }
}


// fim
