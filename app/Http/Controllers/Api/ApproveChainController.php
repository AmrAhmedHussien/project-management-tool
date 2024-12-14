<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveChainRequest;
use App\Http\Resources\ApproveChainResource;
use App\Models\Project;
use App\Models\ProjectApproveChain;
use App\Models\ProjectStatus;
use App\Notifications\ApproveProjectRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveChainController extends Controller
{
    public function store(ApproveChainRequest $request)
    {
        try {
            DB::beginTransaction();
            $approveChain = ProjectApproveChain::query()->create($request->validated());
            $user = $approveChain->nextUser();
            $pravious_chains = ProjectApproveChain::query()->where('project_id', $request->project_id)->where('user_id', '<>', $request->user_id)->where('status', 'pending')->first();
            $status_id = ProjectStatus::query()->select('id')->where('name', 'pending-approval')->first()->id;
            $project = Project::query()->where('id', $request->project_id);
            $project->update(['status_id' => $status_id]);
            $project->first()->users()->syncWithoutDetaching([$request->user_id => ['role' => 'Approver']]);
            if (!$pravious_chains)
                $user->notify(new ApproveProjectRequest($approveChain->project_id));
            DB::commit();
            return apiResponse(new ApproveChainResource($approveChain), 'User Added To Approve Chain Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return apiResponse(null, 'Someting Went Wrong, Please Contact Support', 400);
        }
    }
    public function show($project_id)
    {
        try {
            $approveChain = ProjectApproveChain::query()->with('user')->where('project_id', $project_id)->orderBy('order', 'asc')->get();
            if ($approveChain)
                return apiResponse(ApproveChainResource::collection($approveChain), 'Approve Chain Returned Successfully', 200);
            else
                return apiResponse(null, 'No Approve Chain Added For This Project', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return apiResponse(null, 'Someting Went Wrong, Please Contact Support', 400);
        }
    }
    public function approve($project_id)
    {
        try {
            $projectApproveChain = ProjectApproveChain::query()->where('project_id', $project_id)->where('user_id', Auth::id())->first();
            if ($projectApproveChain && $projectApproveChain->nextUser()?->id == Auth::id()) {
                $projectApproveChain->update(['status' => 'approved','approved_at'=>now()]);
                $next_user = $projectApproveChain->nextUser();
                if (isset($next_user)) {
                    $next_user->notify(new ApproveProjectRequest($projectApproveChain->project_id));
                } else {
                    $status_id = ProjectStatus::query()->select('id')->where('name', 'completed')->first()->id;
                    $projectApproveChain->project->update(['status_id' => $status_id]);
                }
                return apiResponse(null, 'Project Approved Successfully', 200);
            } else {
                return apiResponse(null, 'You Are Not Authorized To Perform This Action', 403);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return apiResponse(null, 'Someting Went Wrong, Please Contact Support', 400);
        }
    }
}
