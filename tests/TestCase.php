<?php

namespace Tests;

use App\Models\ProjectApproveChain;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public function createApproveChain($user_ids)
    {
        foreach($user_ids as $i => $user_id)
        {
            ProjectApproveChain::query()->create([
                'user_id' => $user_id,
                'project_id' => 1,
                'order' => $i + 1,
                'status' => 'pending'
            ]);
        }
    }
}
