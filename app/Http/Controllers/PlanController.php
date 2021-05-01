<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tasks;
use App\Models\Plans;


class PlanController extends Controller
{
    public function AddPlan(Request $request){
        $data = Plans::create($request->except(["_token"]));
        if($data){
            echo "Plan Added!";
        }else{
            echo "Error on Adding Plan";
        }
    }

    public function RemovePlan(Request $request){
        $data = Plans::join("tasks", "tasks.plan_id", "=", $request->id)
                    ->where("plans.id", "=", $request->id)
                    ->delete();
        if($data){
            echo "Plan Deleted!";
        }else{
            echo "Error on Deleting plan";
        }


    }

    public function UpdatePlan(Request $request){
        $data = Plans::where("id", "=", $request->id)
                ->update([
                    "plan_name" => $request->plan_name,
                    "plan_priority" => $request->plan_priority,
                    "plan_dependency" => $request->plan_dependency,
                    "plan_date_start" => $request->plan_date_start,
                    "plan_date_end" => $request->plan_date_end
                ]);
        if($data){
            echo "Plan Updated!";
        }else{
            echo "Error on Updating plan";
        }

        
    }
}
