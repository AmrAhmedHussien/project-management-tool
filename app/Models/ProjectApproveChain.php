<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectApproveChain extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function nextUser()
    {
        $user_id = $this->select('user_id')->where('status','pending')->orderBy('order','asc')->first()?->user_id;
        if ($user_id !==null) {
            return User::find($user_id);
        }
        return null;
    }
}
