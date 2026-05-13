<?php

namespace iProtek\PolicyControl\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\PolicyControl\Models\PolicyControl;

class PolicyControlController extends _CommonController
{
    //
    public function list(Request $request)
    {
        $data = PolicyControl::whereRaw('name like ? AND is_visible = 1',["api.%"])->orderBy('name')->get();

        return $data;
    }
    
}
