<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveChainRequest;
use App\Http\Resources\ApproveChainResource;
use App\Models\ProjectApproveChain;
use App\Notifications\ApproveProjectRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveChainController extends Controller
{
    public function store(ApproveChainRequest $request)
    {
        try{
            DB::beginTransaction();
            $approveChain = ProjectApproveChain::query()->create($request->validated());
            $user = $approveChain->nextUser();
            $pravious_chains = ProjectApproveChain::query()->where('project_id',$request->project_id)->where('user_id','<>',$request->user_id)->where('status','pending')->first();
            if(!$pravious_chains)
                $user->notify(new ApproveProjectRequest($approveChain->project_id));
            DB::commit();
            return apiResponse($approveChain,'User Added To Approve Chain Successfully',200);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            Log::error($e->getMessage());
            return apiResponse(null,'Someting Went Wrong, Please Contact Support',400);
        }
    }
    public function show($project_id)
    {
        try{
           $approveChain = ProjectApproveChain::query()->with('user')->where('project_id',$project_id)->get();
           if($approveChain)
                return apiResponse(ApproveChainResource::collection($approveChain),'Approve Chain Returned Successfully',200);
            else
                return apiResponse(null,'No Approve Chain Added For This Project',200);
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return apiResponse(null,'Someting Went Wrong, Please Contact Support',400);
        }
    }
}
