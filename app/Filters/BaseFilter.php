<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class BaseFilter
{
    abstract public static function createFromRequest(Request $request);
}
