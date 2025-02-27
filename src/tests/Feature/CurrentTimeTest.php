<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CurrentTimeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAttendanceCurrentTimeDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $CurrentTime = Carbon::now()->format('H:i');

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertSee($CurrentTime);
    }
}
