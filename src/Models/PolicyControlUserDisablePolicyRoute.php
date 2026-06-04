<?php

namespace iProtek\PolicyControl\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyControlUserDisablePolicyRoute extends Model
{
    //
    public $fillable = [
        "branch_id",
        "app_account_id",
        "route_name"
    ];  
}
