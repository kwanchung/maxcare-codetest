<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Log;


class TaskTest extends TestCase
{
    public function test_create_task(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
    
        $response = $this->postJson('/api/tasks', [
            'title' => 'test',
            'description' => 'test',
            'status' => 'pending',
            'due_date' => now(),
        ]);
    
        $response->assertStatus(201);

    }

    public function test_create_task_failed(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson('/api/tasks', [
            'title' => 'test',
            'description' => 'test',
            'due_date' => now(),
        ]);
    
        $response->assertStatus(422);
    }

    public function test_task_list_can_be_retrieved(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $task = Task::factory()->for($userA)->create([
            'title' => 'User A Task',
            'description' => 'User A Task',
            'status' => 'pending',
            'due_date' => '2025-11-29 18:00:00'
        ]);

        $this->actingAs($userB, 'sanctum');
    
        $response = $this->getJson("/api/tasks/{$task->id}");
    
        $response->assertStatus(403);
    }

    public function test_task_filter(): void
    {
        $user = User::factory()->create();

        Task::factory()->for($user)->count(3)->pending()->create();
        Task::factory()->for($user)->count(2)->completed()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/tasks?status=completed');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonCount(2, 'data');
    }
}
