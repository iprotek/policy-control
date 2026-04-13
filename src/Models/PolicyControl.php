<?php

namespace iProtek\PolicyControl\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyControl extends Model
{
    //
    public $fillable = [
        "name",
        "description",
        "is_active",
        "is_visible",
        "default_is_allow"
    ];
}
