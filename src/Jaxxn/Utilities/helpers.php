<?php
use Jaxxn\Support\GenericCollection;

function dd($dump)
{
    var_dump($dump);
    die();
}

function is_collection($item)
{
    return ($item instanceof GenericCollection);
}