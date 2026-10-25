<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_edit_form_with_dates_and_times_ready_for_inputs(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Tasks/Edit', false)
            ->where('task.id', $task->id)
            ->where('task.due_date', '2026-10-30')
            ->where('task.due_time', '14:30'));
    }

    public function test_owner_can_save_task_changes(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Updated assignment',
            'description' => 'Updated details',
            'subject_id' => null,
            'due_date' => '2026-11-02',
            'due_time' => '09:15',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'title' => 'Updated assignment',
            'description' => 'Updated details',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);
        $savedTask = $task->fresh()->toArray();
        $this->assertSame('2026-11-02', $savedTask['due_date']);
        $this->assertSame('09:15', $savedTask['due_time']);
    }

    public function test_owner_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertModelMissing($task);
    }

    #[DataProvider('taskActions')]
    public function test_other_users_cannot_edit_update_or_delete_task(string $method, string $route): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = $this->createTask($owner);

        $response = $this->actingAs($otherUser)->{$method}(route($route, $task), [
            'title' => 'Unauthorized change',
            'priority' => 'high',
            'status' => 'completed',
        ]);

        $response->assertForbidden();
        $this->assertModelExists($task);
        $this->assertSame('Original assignment', $task->fresh()->title);
    }

    #[DataProvider('taskActions')]
    public function test_guests_are_redirected_to_login(string $method, string $route): void
    {
        $task = $this->createTask(User::factory()->create());

        $response = $this->{$method}(route($route, $task));

        $response->assertRedirect(route('login'));
        $this->assertModelExists($task);
    }

    public function test_invalid_changes_do_not_modify_task(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        $response = $this->actingAs($user)->from(route('tasks.edit', $task))
            ->put(route('tasks.update', $task), [
                'title' => '',
                'priority' => 'medium',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('title')->assertRedirect(route('tasks.edit', $task));
        $this->assertSame('Original assignment', $task->fresh()->title);
    }

    public static function taskActions(): array
    {
        return [
            'edit form' => ['get', 'tasks.edit'],
            'save changes' => ['put', 'tasks.update'],
            'delete' => ['delete', 'tasks.destroy'],
        ];
    }

    private function createTask(User $user): Task
    {
        return $user->tasks()->create([
            'title' => 'Original assignment',
            'due_date' => '2026-10-30',
            'due_time' => '14:30',
            'priority' => 'medium',
            'status' => 'pending',
        ]);
    }
}
