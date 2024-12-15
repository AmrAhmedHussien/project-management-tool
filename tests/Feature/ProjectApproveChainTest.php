<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectApproveChainTest extends TestCase
{
    private $headers = [
        'Accept'       => 'application/json',
    ];
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
        User::factory()->count(5)->create();
        Project::query()->create([
            'name' => 'test project',
            'description' => 'test project description',
            'status_id' => 1,
            'ticket_prefix' => 'p1',
            'status_type' => 'default',
            'type' => 'kanban',
            'owner_id' => 1
        ]);
    }
    public function test_create_chain_un_authenticated()
    {
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'user_id' => 1,
            'order' => 1
        ],$this->headers);
        $response->assertStatus(401);
    }
    public function test_create_chain_without_project_id()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'user_id' => 1,
            'order' => 1,
        ],$this->headers);
        $this->assertTrue($response->json()['message'] == "The project id field is required.");
        $response->assertStatus(422);

    }

    public function test_create_chain_with_invalid_project_id()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 2,
            'user_id' => 1,
            'order' => 1,
        ],$this->headers);
        $this->assertTrue($response->json()['message'] == "The selected project id is invalid.");
        $response->assertStatus(422);

    }
    public function test_create_chain_without_user_id()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'order' => 1,
        ],$this->headers);
        $this->assertTrue($response->json()['message'] == "The user id field is required.");
        $response->assertStatus(422);

    }

    public function test_create_chain_with_invalid_user_id()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'user_id' => 0,
            'order' => 1,
        ],$this->headers);
        $this->assertTrue($response->json()['message'] == "The selected user id is invalid.");
        $response->assertStatus(422);

    }
    public function test_create_chain_with_non_unique_user_id_and_non_unique_order()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'user_id' => 1,
            'order' => 1,
        ],$this->headers);
        $response1 = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'user_id' => 1,
            'order' => 1,
        ],$this->headers);
        $this->assertTrue($response1->json()['errors']['user_id'][0] == "This User Already Exist In The Chain");
        $this->assertTrue($response1->json()['errors']['order'][0] == "There Is Another User With This Order");
        $response1->assertStatus(422);
        DB::table('project_approve_chains')->truncate();
    }
    public function test_create_chain_without_order()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'user_id' => 1,
        ],$this->headers);
        $this->assertTrue($response->json()['errors']['order'][0] == "The order field is required.");
        $response->assertStatus(422);
        DB::table('project_approve_chains')->truncate();
    }
    public function test_create_chain_happy_scenario()
    {
        $this->actingAs(User::first());
        $response = $this->post('/api/project-approve-chain',[
            'project_id' => 1,
            'user_id' => 1,
            'order' => 1
        ],$this->headers);
        $this->assertTrue($response->json()['message'] == "User Added To Approve Chain Successfully");
        $response->assertStatus(200);
        $this->assertDatabaseCount('project_approve_chains',1);
        DB::table('project_approve_chains')->truncate();
    }
    public function test_approve_a_project_unauthenticated()
    {
        $this->createApproveChain([1]);
        $response = $this->patch('/api/project-approve-chain/1/approve',[],$this->headers);
        $response->assertStatus(401);
        DB::table('project_approve_chains')->truncate();
    }
    public function test_approve_a_project_unAuthorized()
    {
        $this->createApproveChain([1]);
        $this->actingAs(User::find(2));
        $response = $this->patch('/api/project-approve-chain/1/approve',[],$this->headers);
        $this->assertTrue($response->json()['message'] == 'You Are Not Authorized To Perform This Action');
        $response->assertStatus(403);
        DB::table('project_approve_chains')->truncate();
    }
    public function test_approve_a_project_not_in_order()
    {
        $this->createApproveChain([1,2]);
        $this->actingAs(User::find(2));
        $response = $this->patch('/api/project-approve-chain/1/approve',[],$this->headers);
        $this->assertTrue($response->json()['message'] == 'You Are Not Authorized To Perform This Action');
        $response->assertStatus(403);
        DB::table('project_approve_chains')->truncate();
    }
    public function test_approve_a_project_happy_scenario()
    {
        $this->createApproveChain([1,2]);
        $this->actingAs(User::find(1));
        $response = $this->patch('/api/project-approve-chain/1/approve',[],$this->headers);
        $this->assertTrue(Project::with('status')->find(1)->status->name != 'completed');
        $this->assertTrue($response->json()['message'] == 'Project Approved Successfully');
        $response->assertStatus(200);
        $this->actingAs(User::find(2));
        $response1 = $this->patch('/api/project-approve-chain/1/approve',[],$this->headers);
        $this->assertTrue(Project::with('status')->find(1)->status->name == 'completed');
        $this->assertTrue($response1->json()['message'] == 'Project Approved Successfully');
        $response1->assertStatus(200);
        DB::table('project_approve_chains')->truncate();
    }

}
