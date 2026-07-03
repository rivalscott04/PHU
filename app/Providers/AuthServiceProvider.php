<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Checklist::class => \App\Policies\ChecklistPolicy::class,
        \App\Models\Inspection::class => \App\Policies\InspectionPolicy::class,
        \App\Models\Followup::class => \App\Policies\FollowupPolicy::class,
        \App\Models\RiskScore::class => \App\Policies\RiskPolicy::class,
        \App\Models\AuditLog::class => \App\Policies\AuditLogPolicy::class,
        \App\Models\SupervisionWorkQueue::class => \App\Policies\WorkQueuePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
