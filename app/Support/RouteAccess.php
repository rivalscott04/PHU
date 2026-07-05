<?php

namespace App\Support;

use App\Models\Followup;
use App\Models\Inspection;
use App\Models\RiskScore;
use App\Models\SupervisionWorkQueue;
use App\Models\TravelCompany;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\ChecklistPolicy;
use App\Policies\CompliancePolicy;
use App\Policies\ExportPolicy;
use App\Policies\FollowupPolicy;
use App\Policies\InspectionPolicy;
use App\Policies\MonitoringPolicy;
use App\Policies\RiskPolicy;
use App\Policies\WorkQueuePolicy;
use App\Services\TravelCapabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Satu sumber kebenaran untuk menampilkan link/navigasi sesuai policy & matriks role.
 */
final class RouteAccess
{
    public static function canAccessUrl(string $url): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $parts = parse_url($url);
        $path = $parts['path'] ?? $url;
        $query = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        try {
            $route = Route::getRoutes()->match(Request::create($path, 'GET', $query));
        } catch (NotFoundHttpException|MethodNotAllowedHttpException) {
            return false;
        }

        $name = $route->getName();

        if ($name === null) {
            return false;
        }

        return self::canAccessRoute($user, $name, $route->parameters());
    }

    /** @param  array<string, mixed>  $parameters */
    public static function canAccessRoute(User $user, string $routeName, array $parameters = []): bool
    {
        if (self::matches($routeName, 'v2.monitoring.travel.pengaduan')) {
            return self::canAccessTravelPengaduan($user, $parameters);
        }

        if (self::matches($routeName, 'v2.monitoring.kabupaten.pengaduan')) {
            $kabupaten = $parameters['kabupaten'] ?? null;

            return is_string($kabupaten)
                && $kabupaten !== ''
                && (new MonitoringPolicy())->viewKabupatenPengaduan($user, $kabupaten);
        }

        if (self::matches($routeName, 'v2.dashboard') || self::matches($routeName, 'v2.monitoring')) {
            return (new MonitoringPolicy())->view($user);
        }

        if (self::matches($routeName, 'v2.pengawasan.create')) {
            return (new InspectionPolicy())->create($user);
        }

        if (self::matches($routeName, 'v2.pengawasan')) {
            return self::canAccessInspection($user, $parameters);
        }

        if (self::matches($routeName, 'v2.risk.recalculate')) {
            return (new RiskPolicy())->recalculate($user);
        }

        if (self::matches($routeName, 'v2.risk')) {
            return self::canAccessRisk($user, $parameters);
        }

        if (self::matches($routeName, 'v2.compliance')) {
            return self::canAccessCompliance($user, $parameters);
        }

        if (self::matches($routeName, 'v2.followup')) {
            return self::canAccessFollowup($user, $parameters);
        }

        if (self::matches($routeName, 'v2.antrian')) {
            return self::canAccessWorkQueue($user, $parameters);
        }

        if (self::matches($routeName, 'v2.checklist')) {
            return (new ChecklistPolicy())->viewAny($user);
        }

        if (self::matches($routeName, 'v2.audit-log')) {
            return (new AuditLogPolicy())->viewAny($user);
        }

        if ($routeName === 'v2.export.dashboard') {
            return (new ExportPolicy())->exportDashboard($user);
        }

        if ($routeName === 'v2.export.pengawasan') {
            return (new ExportPolicy())->exportPengawasan($user);
        }

        if (self::matches($routeName, 'v2.export')) {
            return (new ExportPolicy())->export($user);
        }

        if (self::matches($routeName, 'v2.notifications')) {
            return true;
        }

        $matrixResult = self::matrixAccess($user, $routeName);

        if ($matrixResult !== null) {
            return $matrixResult;
        }

        return self::legacyFeatureAccess($user, $routeName);
    }

    /** @return list<array{name: string, route: string, icon: string, style: string, params?: array<string, mixed>}> */
    public static function monitoringQuickAccessLinks(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $candidates = [
            ['name' => 'Dashboard Eksekutif', 'route' => 'v2.dashboard', 'icon' => 'bx-line-chart', 'style' => 'outline-primary'],
            ['name' => 'Daftar Pengawasan', 'route' => 'v2.pengawasan.index', 'icon' => 'bx-search-alt', 'style' => 'outline-primary'],
            ['name' => 'Skor Risiko', 'route' => 'v2.risk.index', 'icon' => 'bx-shield-quarter', 'style' => 'outline-warning'],
            ['name' => 'Profil Kepatuhan', 'route' => 'v2.compliance.index', 'icon' => 'bx-check-shield', 'style' => 'outline-success'],
            ['name' => 'Export Data Travel', 'route' => 'v2.export.travel', 'icon' => 'bx-export', 'style' => 'outline-secondary', 'params' => ['format' => 'xlsx']],
        ];

        return array_values(array_filter(
            $candidates,
            fn (array $link): bool => self::canAccessRoute($user, $link['route'], $link['params'] ?? [])
        ));
    }

    private static function matches(string $routeName, string $prefix): bool
    {
        return $routeName === $prefix || str_starts_with($routeName, $prefix.'.');
    }

    /** @param  array<string, mixed>  $parameters */
    private static function canAccessTravelPengaduan(User $user, array $parameters): bool
    {
        $travel = $parameters['travel'] ?? null;

        if ($travel instanceof TravelCompany) {
            return (new MonitoringPolicy())->viewTravelPengaduan($user, $travel);
        }

        if (is_numeric($travel)) {
            $travel = TravelCompany::find((int) $travel);

            return $travel instanceof TravelCompany
                && (new MonitoringPolicy())->viewTravelPengaduan($user, $travel);
        }

        return false;
    }

    /** @param  array<string, mixed>  $parameters */
    private static function canAccessInspection(User $user, array $parameters): bool
    {
        $policy = new InspectionPolicy();
        $inspection = $parameters['pengawasan'] ?? null;

        if ($inspection instanceof Inspection) {
            return $policy->view($user, $inspection);
        }

        return $policy->viewAny($user);
    }

    /** @param  array<string, mixed>  $parameters */
    private static function canAccessRisk(User $user, array $parameters): bool
    {
        $policy = new RiskPolicy();
        $travel = $parameters['travel'] ?? null;

        if ($travel instanceof TravelCompany) {
            $risk = $travel->relationLoaded('riskScore')
                ? $travel->riskScore
                : $travel->riskScore()->first();

            if ($risk instanceof RiskScore) {
                return $policy->view($user, $risk);
            }
        }

        return $policy->viewAny($user);
    }

    /** @param  array<string, mixed>  $parameters */
    private static function canAccessCompliance(User $user, array $parameters): bool
    {
        $policy = new CompliancePolicy();
        $travel = $parameters['travel'] ?? null;

        if ($travel instanceof TravelCompany) {
            return $policy->view($user, $travel);
        }

        return $policy->viewAny($user);
    }

    /** @param  array<string, mixed>  $parameters */
    private static function canAccessFollowup(User $user, array $parameters): bool
    {
        $policy = new FollowupPolicy();
        $followup = $parameters['followup'] ?? null;

        if ($followup instanceof Followup) {
            return $policy->view($user, $followup);
        }

        return $policy->viewAny($user);
    }

    /** @param  array<string, mixed>  $parameters */
    private static function canAccessWorkQueue(User $user, array $parameters): bool
    {
        $policy = new WorkQueuePolicy();
        $item = $parameters['antrian'] ?? null;

        if ($item instanceof SupervisionWorkQueue) {
            return $policy->view($user, $item);
        }

        return $policy->viewAny($user);
    }

    private static function matrixAccess(User $user, string $routeName): ?bool
    {
        $expectations = RoleRouteMatrix::routeExpectations()[$user->role] ?? null;

        if ($expectations === null || ! array_key_exists($routeName, $expectations)) {
            return null;
        }

        return $expectations[$routeName] === 200;
    }

    private static function legacyFeatureAccess(User $user, string $routeName): bool
    {
        $featureMap = [
            'bap' => 'bap',
            'form.bap' => 'bap',
            'form.bap.edit' => 'bap',
            'detail.bap' => 'bap',
            'post.bap' => 'bap',
            'put.bap' => 'bap',
            'bap.jamaah.options' => 'bap',
            'cetak.bap' => 'bap',
            'bap.upload' => 'bap',
            'bap.ajukan' => 'bap',
            'bap.wizard.upload' => 'bap',
            'bap.wizard.review' => 'bap',
            'bap.template.surat-pernyataan' => 'bap',
            'bap.updateStatus' => 'bap',
            'keberangkatan' => 'keberangkatan',
            'calendar.events' => 'keberangkatan',
            'pengunduran' => 'pengunduran',
            'pengunduran.create' => 'pengunduran',
            'pengunduran.store' => 'pengunduran',
            'cabang.travel' => 'cabang_travel',
            'form.cabang_travel' => 'cabang_travel',
            'post.cabang_travel' => 'cabang_travel',
            'cabang.travel.edit' => 'cabang_travel',
            'cabang.travel.update' => 'cabang_travel',
            'cabang.travel.destroy' => 'cabang_travel',
            'import.cabang_travel' => 'cabang_travel',
            'download.template.cabang_travel' => 'cabang_travel',
            'cabang.travel.export' => 'cabang_travel',
            'sertifikat.index' => 'sertifikat',
            'sertifikat.create' => 'sertifikat',
            'sertifikat.store' => 'sertifikat',
            'sertifikat.generate' => 'sertifikat',
            'sertifikat.download' => 'sertifikat',
            'sertifikat.view' => 'sertifikat',
            'sertifikat.settings' => 'sertifikat',
            'sertifikat.settings.update' => 'sertifikat',
            'travel.certificates' => 'sertifikat',
            'form.travel' => 'travel_management',
            'post.travel' => 'travel_management',
            'travel.edit' => 'travel_management',
            'travel.update' => 'travel_management',
            'travel.export' => 'travel_management',
            'travel.update-status' => 'travel_management',
            'jamaah.haji' => 'jamaah_haji_khusus',
            'jamaah.haji.create' => 'jamaah_haji_khusus',
            'jamaah.haji.store' => 'jamaah_haji_khusus',
            'jamaah.haji-khusus.export' => 'jamaah_haji_khusus',
            'jamaah.haji-khusus.export-pdf' => 'jamaah_haji_khusus',
            'jamaah.haji-khusus.update-status' => 'jamaah_haji_khusus',
            'jamaah.haji-khusus.verify-bukti-setor' => 'jamaah_haji_khusus',
            'jamaah.haji-khusus.assign-porsi' => 'jamaah_haji_khusus',
            'jamaah.umrah.create' => 'jamaah_umrah',
            'jamaah.umrah.store' => 'jamaah_umrah',
            'jamaah.umrah.export' => 'jamaah_umrah',
        ];

        if (isset($featureMap[$routeName])) {
            return TravelCapabilityService::canAccess($featureMap[$routeName]);
        }

        if (self::matches($routeName, 'jamaah.haji-khusus') || self::matches($routeName, 'jamaah.haji_khusus')) {
            return TravelCapabilityService::canAccess('jamaah_haji_khusus');
        }

        if (self::matches($routeName, 'jamaah')) {
            return TravelCapabilityService::canAccess('jamaah_umrah')
                || TravelCapabilityService::canAccess('jamaah_haji_khusus');
        }

        if (self::matches($routeName, 'sertifikat')) {
            return TravelCapabilityService::canAccess('sertifikat');
        }

        if (self::matches($routeName, 'users')) {
            return $user->role === 'admin';
        }

        if (self::matches($routeName, 'impersonate')) {
            return $user->role === 'admin';
        }

        return false;
    }
}
