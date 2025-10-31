<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\BehaviorAnalysisService;
use App\Models\User;
use App\Models\UserBehavior;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BehaviorAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $behaviorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->behaviorService = app(BehaviorAnalysisService::class);
    }

    /** @test */
    public function it_can_track_user_behavior()
    {
        $user = User::factory()->create();

        $behavior = $this->behaviorService->trackBehavior(
            $user->id,
            'view',
            'Event',
            1,
            ['duration' => 30]
        );

        $this->assertInstanceOf(UserBehavior::class, $behavior);
        $this->assertEquals('view', $behavior->action_type);
        $this->assertEquals(1, $behavior->engagement_score);
    }

    /** @test */
    public function it_calculates_correct_engagement_scores()
    {
        $user = User::factory()->create();

        $view = $this->behaviorService->trackBehavior($user->id, 'view', 'Event', 1);
        $click = $this->behaviorService->trackBehavior($user->id, 'click', 'Event', 1);
        $apply = $this->behaviorService->trackBehavior($user->id, 'apply', 'Event', 1);
        $complete = $this->behaviorService->trackBehavior($user->id, 'complete', 'Event', 1);

        $this->assertEquals(1, $view->engagement_score);
        $this->assertEquals(2, $click->engagement_score);
        $this->assertEquals(5, $apply->engagement_score);
        $this->assertEquals(10, $complete->engagement_score);
    }

    /** @test */
    public function it_can_get_user_insights()
    {
        $user = User::factory()->create();

        // Create behavior history
        for ($i = 0; $i < 10; $i++) {
            $this->behaviorService->trackBehavior($user->id, 'view', 'Event', $i);
        }

        $insights = $this->behaviorService->getUserInsights($user->id);

        $this->assertNotNull($insights->engagement_level);
        $this->assertNotNull($insights->volunteer_type);
        $this->assertGreaterThan(0, $insights->estimated_lifetime_value);
    }

    /** @test */
    public function it_updates_preference_profile()
    {
        $user = User::factory()->create();

        $this->behaviorService->trackBehavior($user->id, 'view', 'Event', 1);
        $this->behaviorService->trackBehavior($user->id, 'view', 'Event', 2);
        $this->behaviorService->trackBehavior($user->id, 'click', 'Event', 3);

        $profile = $this->behaviorService->updatePreferenceProfile($user->id);

        $this->assertNotNull($profile);
    }
}
