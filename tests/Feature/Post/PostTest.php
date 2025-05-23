<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;
    protected $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->post = Post::factory()->create([
            'created_by' => $this->user->id,
            'title' => 'Old Title',
            'content' => 'Old Content',
            'publish_date' => now()->format('Y-m-d'),
            'is_draft' => false,
        ]);
    }

    public function test_user_owner_can_update_post()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('posts.update', $this->post->id), [
            'title' => 'New Title',
            'content' => 'New Content',
            'publish_date' => now()->format('Y-m-d'),
            'is_draft' => false,
        ]);

        $response->assertRedirect(route('posts.internal', $this->post->id));
        $response->assertSessionHas('status', 'success');

        $this->assertDatabaseHas('posts', [
            'id' => $this->post->id,
            'title' => 'New Title',
            'content' => 'New Content',
        ]);
    }

    public function test_user_not_owner_cannot_update_post()
    {
        $this->actingAs($this->otherUser);

        $response = $this->put(route('posts.update', $this->post->id), [
            'title' => 'Hack Title',
            'content' => 'Hack Content',
            'publish_date' => now()->format('Y-m-d'),
            'is_draft' => false,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', [
            'id' => $this->post->id,
            'title' => 'Old Title',
            'content' => 'Old Content',
        ]);
    }

    public function test_user_owner_can_delete_post()
    {
        $this->actingAs($this->user);

        $response = $this->delete(route('posts.destroy', $this->post->id));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'success');

        $this->assertSoftDeleted('posts', ['id' => $this->post->id]);
    }

    public function test_user_not_owner_cannot_delete_post()
    {
        $this->actingAs($this->otherUser);

        $response = $this->delete(route('posts.destroy', $this->post->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('posts', ['id' => $this->post->id]);
    }
}
