<?php

namespace Tests\Feature\Home;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeListPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_home_but_no_posts()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('posts', null);
    }

    public function test_authenticated_user_sees_own_posts()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('posts', function ($posts) use ($post) {
            return $posts->contains($post);
        });
    }

    public function test_authenticated_user_with_no_posts_sees_empty_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('posts', function ($posts) {
            return $posts->count() === 0;
        });
    }

    public function test_user_does_not_see_posts_of_other_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Post::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHas('posts', function ($posts) {
            return $posts->count() === 0;
        });
    }
}
