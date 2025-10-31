<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\RecommendationService;
use App\Models\User;
use App\Models\PersonalizedRecommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $recommendationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recommendationService = app(RecommendationService::class);
    }

    /** @test */
    public function it_can_save_a_recommendation()
    {
        $user = User::factory()->create();
        
        $recommendation = $this->recommendationService->saveRecommendation(
            $user->id,
            1,
            'Event',
            8.5,
            'collaborative_filtering'
        );

        $this->assertInstanceOf(PersonalizedRecommendation::class, $recommendation);
        $this->assertEquals($user->id, $recommendation->user_id);
        $this->assertEquals(1, $recommendation->item_id);
        $this->assertEquals(8.5, $recommendation->score);
    }

    /** @test */
    public function it_can_track_recommendation_click()
    {
        $user = User::factory()->create();
        $recommendation = PersonalizedRecommendation::factory()->create([
            'user_id' => $user->id,
            'clicked' => false,
        ]);

        $updated = $this->recommendationService->trackRecommendationClick($recommendation->id);

        $this->assertTrue($updated->clicked);
        $this->assertNotNull($updated->clicked_at);
    }

    /** @test */
    public function it_can_track_recommendation_conversion()
    {
        $user = User::factory()->create();
        $recommendation = PersonalizedRecommendation::factory()->create([
            'user_id' => $user->id,
            'converted' => false,
        ]);

        $updated = $this->recommendationService->trackRecommendationConversion($recommendation->id);

        $this->assertTrue($updated->converted);
        $this->assertNotNull($updated->converted_at);
    }

    /** @test */
    public function it_can_get_recommendations_for_user()
    {
        $user = User::factory()->create();
        PersonalizedRecommendation::factory(5)->create(['user_id' => $user->id, 'recommendation_type' => 'event']);

        $recommendations = $this->recommendationService->getRecommendations($user->id, 10, 'event');

        $this->assertCount(5, $recommendations);
    }
}
