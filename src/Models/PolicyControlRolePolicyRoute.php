<?php

namespace iProtek\PolicyControl\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyControlRolePolicyRoute extends Model
{
    //
    public $fillable = [
        "branch_id",
        "xrole_id",
        "route_name"
    ]; 

}
