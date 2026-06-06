<?php

namespace iProtek\PolicyControl\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\PolicyControl\Models\PolicyControl;
use iProtek\PolicyControl\Models\PolicyControlUserDisablePolicyRoute;
use iProtek\PolicyControl\Models\PolicyControlRolePolicyRoute;


class PolicyControlController extends _CommonController
{
    //
    public function list(Request $request)
    {
        $data = PolicyControl::whereRaw('name like ? AND is_visible = 1',["api.%"])->orderBy('name')->get();

        return $data;
    }

    public function get_role_routes(Request $request){
        $requestData = $this->validate($request, [
            "xrole_id" => "required",
            "branch_id" => "nullable",
        ])->validated();

        if(empty($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] == "null"){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] <= 0 ){
            $requestData["branch_id"] = 1;
        }
        if(!is_numeric($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }

        $routes = PolicyControlRolePolicyRoute::where(["xrole_id" => $requestData["xrole_id"], "branch_id" => $requestData["branch_id"] ?? null]);
        
        
        $data = $routes->pluck('route_name')->toArray();

        return $data;
    }


    public function update_role(Request $request){
        //role_id and policy-control routes

        $requestData = $this->validate($request, [
            "xrole_id" => "required",
            "branch_id" => "nullable",
            "policy_control_routes" => "required|array",
        ])->validated();
        
        if(empty($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] == "null"){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] <= 0 ){
            $requestData["branch_id"] = 1;
        }
        if(!is_numeric($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }

        //CLEANUP ROUTES NAME FROM ROLE_ID BRANCH_ID WHICH NOT EXIST IN THE REQUEST
        PolicyControlRolePolicyRoute::where(["xrole_id" => $requestData["xrole_id"], "branch_id" => $requestData["branch_id"] ?? null])->whereNotIn('route_name', $requestData["policy_control_routes"])->delete();

        //GET EXISTING ROUTES AND COMPARE FROM THE REQUEST THEN ADD NEW ONES
        $existingRoutes = PolicyControlRolePolicyRoute::where(["xrole_id" => $requestData["xrole_id"], "branch_id" => $requestData["branch_id"] ?? null])->pluck('route_name')->toArray();

        $newRoutes = array_diff($requestData["policy_control_routes"], $existingRoutes);
        foreach($newRoutes as $route){
            PolicyControlRolePolicyRoute::create([
                "xrole_id" => $requestData["xrole_id"],
                "branch_id" => $requestData["branch_id"] ?? null,
                "route_name" => $route
            ]);
        }

        return ["status"=>1, "message" => "Role policy control updated"];

    }

    public function get_user_disable_routes(Request $request){
        $requestData = $this->validate($request, [
            "app_account_id" => "required",
            "branch_id" => "nullable",
        ])->validated();

        if(empty($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] == "null"){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] <= 0 ){
            $requestData["branch_id"] = 1;
        }
        if(!is_numeric($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }

        $routes = PolicyControlUserDisablePolicyRoute::where(["app_account_id" => $requestData["app_account_id"], "branch_id" => $requestData["branch_id"] ?? null]);
        
        
        $data = $routes->pluck('route_name')->toArray();

        return $data;
    }

    public function update_user_disable_routes(Request $request){
        //user_id and policy-control routes

        $requestData = $this->validate($request, [
            "app_account_id" => "required",
            "branch_id" => "nullable",
            "policy_control_routes" => "nullable|array",
        ])->validated();
        
        if(empty($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] == "null"){
            $requestData["branch_id"] = 1;
        }
        if($requestData["branch_id"] <= 0 ){
            $requestData["branch_id"] = 1;
        }
        if(!is_numeric($requestData["branch_id"])){
            $requestData["branch_id"] = 1;
        }

        //CLEANUP ROUTES NAME FROM USER_ID BRANCH_ID WHICH NOT EXIST IN THE REQUEST
        $userRoutes = PolicyControlUserDisablePolicyRoute::where(["app_account_id" => $requestData["app_account_id"], "branch_id" => $requestData["branch_id"]])->whereNotIn('route_name', $requestData["policy_control_routes"])->delete();

        //GET EXISTING ROUTES AND COMPARE FROM THE REQUEST THEN ADD NEW ONES
        $existingRoutes = PolicyControlUserDisablePolicyRoute::where(["app_account_id" => $requestData["app_account_id"], "branch_id" => $requestData["branch_id"]])->pluck('route_name')->toArray();

        $newRoutes = array_diff($requestData["policy_control_routes"], $existingRoutes);
        foreach($newRoutes as $route){
            PolicyControlUserDisablePolicyRoute::create([
                "app_account_id" => $requestData["app_account_id"],
                "branch_id" => $requestData["branch_id"] ?? null,
                "route_name" => $route
            ]);
        }

        return ["status"=>1, "message" => "User policy control updated"];

    }

    public function check_ability(Request $request){
        
        $this->validate($request, [
            "ability" => "required|string",
            "branch_id" => "nullable",
        ]);
        //return auth('admin')->user();
        return auth()->user()->can($request->ability, $request->branch_id ?? null) ? 1 : 0;

    }
}
