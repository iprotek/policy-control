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


    public function update_role(Request $request){
        //role_id and policy-control routes

        $requestData = $this->validate($request, [
            "xrole_id" => "required",
            "branch_id" => "nullable",
            "policy_control_routes" => "required|array",
        ])->validated();

        //CLEANUP ROUTES NAME FROM ROLE_ID BRANCH_ID WHICH NOT EXIST IN THE REQUEST
        PolicyControlRolePolicyRoute::where(["xrole_id" => $requestData["xrole_id"], "branch_id" => $requestData["branch_id"]])->whereNotIn('route_name', $requestData["policy_control_routes"])->delete();

        //GET EXISTING ROUTES AND COMPARE FROM THE REQUEST THEN ADD NEW ONES
        $existingRoutes = PolicyControlRolePolicyRoute::where(["xrole_id" => $requestData["xrole_id"], "branch_id" => $requestData["branch_id"]])->pluck('route_name')->toArray();

        $newRoutes = array_diff($requestData["policy_control_routes"], $existingRoutes);
        foreach($newRoutes as $route){
            PolicyControlRolePolicyRoute::create([
                "xrole_id" => $requestData["xrole_id"],
                "branch_id" => $requestData["branch_id"],
                "route_name" => $route
            ]);
        }

        return ["status"=>1, "message" => "Role policy control updated"];

    }

    public function update_user_disable_routes(Request $request){
        //user_id and policy-control routes

        $requestData = $this->validate($request, [
            "app_account_id" => "required",
            "branch_id" => "nullable",
            "policy_control_routes" => "required|array",
        ])->validated();

        //CLEANUP ROUTES NAME FROM USER_ID BRANCH_ID WHICH NOT EXIST IN THE REQUEST
        PolicyControlUserDisablePolicyRoute::where(["app_account_id" => $requestData["app_account_id"], "branch_id" => $requestData["branch_id"]])->whereNotIn('route_name', $requestData["policy_control_routes"])->delete();

        //GET EXISTING ROUTES AND COMPARE FROM THE REQUEST THEN ADD NEW ONES
        $existingRoutes = PolicyControlUserDisablePolicyRoute::where(["app_account_id" => $requestData["app_account_id"], "branch_id" => $requestData["branch_id"]])->pluck('route_name')->toArray();

        $newRoutes = array_diff($requestData["policy_control_routes"], $existingRoutes);
        foreach($newRoutes as $route){
            PolicyControlUserDisablePolicyRoute::create([
                "app_account_id" => $requestData["app_account_id"],
                "branch_id" => $requestData["branch_id"],
                "route_name" => $route
            ]);
        }

        return ["status"=>1, "message" => "User policy control updated"];

    }
    
}
