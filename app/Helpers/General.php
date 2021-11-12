<?php

use Illuminate\Support\Facades\Config;


function get_languages()
{
    return \App\models\Language::active()->Selection()->get(); //#13
}

function get_default_lang()
{
    return  Config::get('app.locale');
}


function uploadImage($folder , $image)
{
    $image->store('/' , $folder);
    $filename = $image->hashName();
    $path = 'images/' . $folder . '/' . $filename;
    return $path;
}

