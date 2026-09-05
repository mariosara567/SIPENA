<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_class_and_subject(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($admin)
            ->post(route('admin.classes.store'), ['name' => 'X IPA 2'])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.subjects.store'), ['name' => 'Biologi'])
            ->assertRedirect();

        $this->assertDatabaseHas('classes', ['name' => 'X IPA 2']);
        $this->assertDatabaseHas('subjects', ['name' => 'Biologi']);
    }

    public function test_admin_can_generate_students_in_bulk(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $class = SchoolClass::query()->create(['name' => 'XI IPS 2']);

        $this->actingAs($admin)
            ->post(route('admin.students.generate'), [
                'class_id' => $class->id,
                'prefix' => 'siswa',
                'nis_prefix' => 'NIS',
                'start_number' => 1,
                'count' => 5,
                'default_password' => 'password',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('students', 5);
        $this->assertDatabaseHas('users', [
            'username' => 'siswa0001',
            'role' => 'student',
        ]);
    }
}
