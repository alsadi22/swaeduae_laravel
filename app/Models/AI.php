<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBehavior extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $fillable = [
        'user_id', 'action_type', 'entity_type', 'entity_id', 'metadata',
        'engagement_score', 'device_type', 'duration_seconds', 'referrer',
    ];

    protected $casts = ['metadata' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeByActionType($q, $type) { return $q->where('action_type', $type); }
    public function scopeRecent($q) { return $q->orderBy('created_at', 'desc'); }
}

class UserPreferenceProfile extends Model
{
    protected $fillable = [
        'user_id', 'preferred_event_types', 'preferred_skills', 'preferred_locations',
        'preferred_organizations', 'disliked_categories', 'average_engagement_score',
        'total_interactions', 'last_updated_at',
    ];

    protected $casts = [
        'preferred_event_types' => 'array',
        'preferred_skills' => 'array',
        'preferred_locations' => 'array',
        'preferred_organizations' => 'array',
        'disliked_categories' => 'array',
        'last_updated_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class MlModel extends Model
{
    protected $table = 'ml_models';
    protected $fillable = [
        'name', 'type', 'version', 'status', 'accuracy', 'precision', 'recall',
        'training_samples', 'hyperparameters', 'description', 'trained_at', 'last_used_at',
    ];

    protected $casts = ['hyperparameters' => 'array', 'trained_at' => 'datetime', 'last_used_at' => 'datetime'];

    public function scopeActive($q) { return $q->where('status', 'active'); }
}

class PersonalizedRecommendation extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $fillable = [
        'user_id', 'recommendation_type', 'item_id', 'item_type', 'reason',
        'ml_model_id', 'score', 'rank', 'explanation', 'clicked', 'clicked_at',
        'converted', 'converted_at',
    ];

    protected $casts = [
        'explanation' => 'array', 'clicked' => 'boolean', 'clicked_at' => 'datetime',
        'converted' => 'boolean', 'converted_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function model(): BelongsTo { return $this->belongsTo(MlModel::class, 'ml_model_id'); }
    public function scopeUnclicked($q) { return $q->where('clicked', false); }
}

class ContentPersonalization extends Model
{
    protected $fillable = [
        'user_id', 'content_type', 'content_id', 'variant', 'personalized_title',
        'personalized_description', 'personalized_metadata', 'is_shown', 'impressions',
        'clicks', 'conversions',
    ];

    protected $casts = ['personalized_metadata' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class AbTest extends Model
{
    protected $table = 'ab_tests';
    protected $fillable = [
        'name', 'description', 'entity_type', 'entity_id', 'status',
        'variants', 'total_users', 'started_at', 'ended_at',
    ];

    protected $casts = ['variants' => 'array', 'started_at' => 'datetime', 'ended_at' => 'datetime'];

    public function scopeActive($q) { return $q->where('status', 'active'); }
}

class AbTestResult extends Model
{
    protected $table = 'ab_test_results';
    protected $fillable = [
        'ab_test_id', 'user_id', 'variant', 'impressions', 'conversions',
        'conversion_rate', 'metrics',
    ];

    protected $casts = ['metrics' => 'array'];

    public function test() { return $this->belongsTo(AbTest::class, 'ab_test_id'); }
    public function user() { return $this->belongsTo(User::class); }
}

class UserInsight extends Model
{
    protected $fillable = [
        'user_id', 'engagement_level', 'activity_frequency', 'volunteer_type',
        'estimated_lifetime_value', 'behavior_patterns', 'risk_indicators',
        'opportunities', 'last_analyzed_at',
    ];

    protected $casts = [
        'behavior_patterns' => 'array', 'risk_indicators' => 'array',
        'opportunities' => 'array', 'last_analyzed_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class Prediction extends Model
{
    protected $fillable = [
        'user_id', 'prediction_type', 'predicted_value', 'confidence', 'factors',
        'actual_result', 'prediction_date', 'result_date',
    ];

    protected $casts = ['factors' => 'array', 'prediction_date' => 'datetime', 'result_date' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeByType($q, $type) { return $q->where('prediction_type', $type); }
}

class ContentSimilarity extends Model
{
    protected $fillable = ['content_type', 'content_id_1', 'content_id_2', 'similarity_score'];

    public function scopeForContent($q, $type, $id)
    {
        return $q->where('content_type', $type)
            ->where(function ($query) use ($id) {
                $query->where('content_id_1', $id)->orWhere('content_id_2', $id);
            });
    }
}

class FeatureFlag extends Model
{
    protected $table = 'feature_flags';
    protected $fillable = [
        'name', 'description', 'is_enabled', 'target_group', 'conditions', 'rollout_percentage',
    ];

    protected $casts = ['conditions' => 'array'];

    public function scopeEnabled($q) { return $q->where('is_enabled', true); }
}

class UserCohort extends Model
{
    protected $table = 'user_cohorts';
    protected $fillable = ['name', 'description', 'criteria', 'user_count', 'status'];

    protected $casts = ['criteria' => 'array'];

    public function scopeActive($q) { return $q->where('status', 'active'); }
}

class CohortAssignment extends Model
{
    protected $fillable = ['user_id', 'cohort_id', 'assigned_at'];

    protected $casts = ['assigned_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function cohort() { return $this->belongsTo(UserCohort::class); }
}

class EngagementMetric extends Model
{
    protected $fillable = [
        'user_id', 'date', 'events_viewed', 'events_applied', 'events_completed',
        'badges_earned', 'messages_sent', 'hours_volunteered', 'login_count',
        'daily_engagement_score',
    ];

    protected $casts = ['date' => 'date'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeByDate($q, $date) { return $q->where('date', $date); }
}

class ChurnPrediction extends Model
{
    protected $fillable = [
        'user_id', 'churn_probability', 'risk_factors', 'risk_level',
        'recommended_action', 'intervened', 'intervention_type', 'intervention_date',
        'churned', 'churned_date',
    ];

    protected $casts = [
        'risk_factors' => 'array', 'intervention_date' => 'datetime', 'churned_date' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeHighRisk($q) { return $q->where('risk_level', 'high'); }
}
