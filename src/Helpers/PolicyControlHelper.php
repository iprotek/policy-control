<?php


namespace iProtek\PolicyControl\Helpers;


class PolicyControlHelper
{

    private static function validateName($name) {
        // Define the regex pattern for validation
        $pattern = '/^[a-zA-Z0-9_-]+$/';
    
        // Check if the name matches the pattern
        if (preg_match($pattern, $name)) {
            return true; // Valid name
        }
    
        return false; // Invalid name
    }

    public static function updateIfSet($model, $array, $keys ){ 
        foreach($keys as $key){
            if(isset($array[$key]))
                $model->{$key} = $array[$key]; 
        } 
    }


    //SET:
    /**Control
     * [
     * [
     *   "name":"", //primary
     *   "title":"",
     *   "accesses":[
     *     [
     *      "name":"",
     *      "title":"",
     *      "":""
     *      ]   
     *   ]
     * ]
     * ]
     * 
     */
    public static function setControlAccess(array $array){

        //TODO::
        if (!Schema::hasTable('xcontrols')) {
            return ["status"=>0, "message"=>"No Table found"];
        }
        
        //Segragating Controls
        foreach($array as $control){

            //GET CONTROL BY NAME
            if(!$control['name'] && strlen( trim($control['name'])) <= 3 && static::validateName($control['name'])) continue;

            $xControlName = strtolower( trim($control['name']));

            $xcount = Xcontrol::get()->count();
            $xControl = Xcontrol::where('name', $xControlName)->first();
            //IF CONTROL NOT EXIST
            if(!$xControl){
                //CREATE NEW CONTROL
                $xControl = Xcontrol::create([
                    "name"=>$xControlName,
                    "title"=>$control['title'] ?? "",
                    "description"=>$control['description'] ?? "",
                    "order_id"=>$control['order_id'] ?? ($xcount+1),
                    "data"=>$control['data'] ?? "",
                    "is_active"=>$control['is_active'] ?? true,
                    "cstyle"=>$control['is_active'] ?? "",
                    "group_id"=>0
                ]);
                //TODO:: Reorder other controls
                if(isset($control['order_id'])){
                    

                }


            }
            else{

                //UPDATE TITLE OR DESC IF NOT NULL
                static::updateIfSet($xControl, $control, ["title", "description", "order_id", "data", "is_active", "cstyle"]);
                //STORING/UPDATING CONTROL
                $xControl->save();
            }
            //PASS CONTROL ID
            $control_id = $xControl->id;

            //Access
            foreach($control['accesses'] as $access){
                
                //GET ACCESS BY NAME & CONTROL ID
                if(!$access['name'] && strlen( trim($access['name'])) <= 3 && static::validateName($access['name'])) continue;

                $accessName = strtolower( trim($access['name']) );

                $controlCount = XcontrolAccess::count();
                $accessControl = XcontrolAccess::where(['name'=> $accessName, "xcontrol_id"=>$control_id])->first();
                //IF ACCESS NOT EXIST
                if(!$accessControl){
                    //CREATE NEW ACESS
                    $accessControl = XcontrolAccess::create([
                        "name"=>$accessName,
                        "xcontrol_id"=>$control_id,
                        "title"=> $access['title'] ?? "",
                        "description"=> $access['description'] ?? "",
                        "order_id"=> $access['order_id'] ?? ($controlCount+1),
                        "app_id"=>$access['app_id'] ?? 0,
                        "data"=>$access['data'] ?? "",
                        "is_active"=>$access['is_active'] ?? true,
                        "cstyle"=>$access['cstyle'] ?? "",
                        "group_id"=>0
                    ]);
                }
                //UPDATE TITLE OR DESC IF NOT NULL
                static::updateIfSet($accessControl, $access, ["title", "description", "app_id", "order_id", "data", "is_active", "cstyle"]);
                //STORING/UPDATING CONTROL
                $accessControl->save();

            }

        }
        return ["status"=>1, "message"=>"Control Successfully Set"];
    }

    public static function getControlAccess($name=null){
        if (!Schema::hasTable('xcontrols')) {
            return [];
        }
        if(!$name)
            return Xcontrol::with(['accesses'])->orderBy('order_id', 'ASC')->get();
        return Xcontrol::where('name', $name)->with(['accesses'])->orderBy('order_id', 'ASC')->get();
    }

    public static function setRoleControlAccess($role, array $array ){


        //TODO::

        //CHECK ROLE IF EXISTS


        //Segragating Controls
        foreach($array as $control){

            //GET CONTROL BY NAME & $role

            //IF CONTROL NOT EXIST
                //CREATE NEW CONTROL

            //UPDATE TITLE OR DESC IF NOT NULL

            //STORING/UPDATING CONTROL

            //PASS CONTROL ID

            //Access
            foreach($control['accesses'] as $access){
                
                    //GET ACCESS BY NAME & CONTROL ID

                    //IF ACCESS NOT EXIST
                        //CREATE NEW ACESS

                    //UPDATE TITLE OR NAME IF NOT NULL

                    //STORING/UPDATING ACCESS

            }

        }

    }

    public static function getRoleControlAccess($role){

    }

    public static function setGates(){
        //MENU
        Gate::define('menu-xrole', function ($user) {
            if($user->can('super-admin')){
                return true;
            }
            // Check if user has menu-xrole at systemmenu and check its role and its user and in a specific branch
            return UserMenuHelper::userHasMenu($user, 'menu-xrole');
        });


        //XRAC SETTINGS
        $controlAccessList = static::getControlAccess();
        foreach($controlAccessList as $control){

            foreach($control->accesses as $access){
                //PUT DEFINE
                Gate::define($control->name.":".$access->name, function($user) use($control, $access){
                    
                    //CHECK IF USER IS ADMIN
                    if($user->can('superadmin')){
                        return true;
                    }

                    //
                    //GET CURRENT BRANCH
                    $branch_id = BranchSelectionHelper::get();

                    //GET CURRENT PAY_ACCOUNT_ID
                    $app_account_id = PayHttp::pay_account_id();
                
                    //GET USER ROLE
                    $userRole = XuserRole::where([
                        "app_account_id"=>$app_account_id,
                        "branch_id"=>$branch_id,
                        "is_allowed"=>true
                    ])->first();
                    if($userRole){

                        //CHECK USER ROLE IF DEFAULT?
                        if($userRole->is_default){
                            //GET BY ACCESS
                            $defaultAccess = XcontrolAccessDefault::where([
                                "xrole_id"=>$userRole->xrole_id,
                                "xcontrol_access_id"=>$access->id,
                                "is_allow"=>true
                            ])->first();
                            if($defaultAccess){
                                return true;
                            }

                        }
                        else{
                            //TODO:: SHOULD HAVING MERGING FOR DEFAULT TO THOSE NON EXISTENT BUT ITS OKAY
                            $customAccess = XuserCustomRoleAccess::where([
                                "app_account_id"=>$app_account_id,
                                "xrole_id"=>$userRole->xrole_id,
                                "xcontrol_access_id"=>$access->id,
                                "is_allow"=>true,
                                "branch_id"=>$branch_id
                            ])->first();
                            if($customAccess){
                                return true;
                            }

                        }

                        return false;
                    }
                    return false;
                });
            }


        }
        

    }




}