<?php

namespace Tests\Unit\V2;

use App\Policies\ExportPolicy;
use App\Policies\MonitoringPolicy;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class ExtendedPolicyTest extends TestCase
{
    use RunsV2Migrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
    }

    public function test_monitoring_policy_allows_all_v2_roles(): void
    {
        $policy = new MonitoringPolicy();
        $admin = $this->seedAdminUser();
        $kabupaten = $this->seedKabupatenUser();
        $travelUser = $this->seedTravelUser();

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->view($kabupaten));
        $this->assertTrue($policy->view($travelUser));
    }

    public function test_export_policy_allows_export_for_authenticated_roles(): void
    {
        $policy = new ExportPolicy();
        $admin = $this->seedAdminUser();
        $kabupaten = $this->seedKabupatenUser();
        $travelUser = $this->seedTravelUser();

        $this->assertTrue($policy->export($admin));
        $this->assertTrue($policy->exportDashboard($kabupaten));
        $this->assertTrue($policy->exportPengawasan($travelUser));
    }
}
