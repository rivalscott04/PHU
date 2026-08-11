<?php

namespace App\Support;

use App\Enums\BapStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\UserRole;
use App\Models\BAP;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\User;
use App\Repositories\WorkQueueRepository;
use Illuminate\Support\Facades\Schema;

final class SidebarBadges
{
    /** @return array<string, int> */
    public static function forUser(User $user): array
    {
        $scope = self::scopeFilters($user);
        $queues = self::resolveQueues($user);

        return [
            'antrian' => self::antrianCount($scope),
            'home_queues' => self::sumQueueCounts($queues),
            'bap_pending' => self::countFromQueues($queues, 'bap_pending')
                ?: HomeCommandCenter::countBapPendingForScope(self::kabupatenScope($user)),
            'pengaduan_open' => self::countFromQueues($queues, 'pengaduan_open'),
            'registration_pending' => self::countFromQueues($queues, 'registration_pending'),
            'followup_verify' => self::followupVerifyCount($user, $scope),
            'followup_action' => self::followupActionCount($user),
            'bap_draft' => self::bapDraftCount($user),
            'inspeksi_active' => self::inspeksiActiveCount($user),
        ];
    }

    public static function countForKey(User $user, ?string $key): int
    {
        if ($key === null || $key === '') {
            return 0;
        }

        return self::forUser($user)[$key] ?? 0;
    }

    /** @return array<string, mixed> */
    private static function scopeFilters(User $user): array
    {
        if ($user->role === UserRole::Kabupaten->value) {
            $kabupaten = NtbKabupatenMap::normalize($user->kabupaten);

            return $kabupaten ? ['kabupaten' => $kabupaten] : [];
        }

        if ($user->role === UserRole::Pengawas->value) {
            $scoped = $user->getScopedKabupatens();

            if ($scoped === null) {
                return [];
            }

            if (count($scoped) === 1) {
                return ['kabupaten' => $scoped[0]];
            }

            return ['kabupatens' => $scoped];
        }

        return [];
    }

    /** @param  array<string, mixed>  $scope */
    private static function antrianCount(array $scope): int
    {
        if (! Schema::hasTable('pengawasan_antrian')) {
            return 0;
        }

        $byType = app(WorkQueueRepository::class)->countOpenByType($scope);

        return (int) array_sum($byType);
    }

    /** @return list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}> */
    private static function resolveQueues(User $user): array
    {
        if ($user->role === UserRole::Admin->value) {
            return HomeCommandCenter::adminQueues();
        }

        if ($user->role === UserRole::Kabupaten->value && $user->kabupaten) {
            return HomeCommandCenter::kabupatenQueues(
                NtbKabupatenMap::normalize($user->kabupaten) ?? $user->kabupaten
            );
        }

        return [];
    }

    /** @param  list<array{key: string, count: int}>  $queues */
    private static function sumQueueCounts(array $queues): int
    {
        $total = 0;

        foreach ($queues as $queue) {
            $total += (int) ($queue['count'] ?? 0);
        }

        return $total;
    }

    private static function kabupatenScope(User $user): ?string
    {
        if ($user->role !== UserRole::Kabupaten->value || ! $user->kabupaten) {
            return null;
        }

        return NtbKabupatenMap::normalize($user->kabupaten) ?? $user->kabupaten;
    }

    /** @param  array<string, mixed>  $scope */
    private static function followupVerifyCount(User $user, array $scope): int
    {
        if (! in_array($user->role, [UserRole::Admin->value, UserRole::Pengawas->value], true)) {
            return 0;
        }

        if (! Schema::hasTable('pengawasan_followups')) {
            return 0;
        }

        $query = Followup::query()
            ->whereIn('status', [FollowupStatus::Submitted->value, FollowupStatus::Pending->value])
            ->whereHas('finding.inspection.travel', function ($travelQuery) use ($user, $scope) {
                if (! empty($scope['kabupaten'])) {
                    $travelQuery->where('kab_kota', $scope['kabupaten']);
                } elseif (! empty($scope['kabupatens'])) {
                    $travelQuery->whereIn('kab_kota', $scope['kabupatens']);
                } elseif ($user->role === UserRole::Pengawas->value) {
                    $scoped = $user->getScopedKabupatens();
                    if (is_array($scoped)) {
                        $travelQuery->whereIn('kab_kota', $scoped);
                    }
                }
            });

        return $query->count();
    }

    private static function followupActionCount(User $user): int
    {
        if ($user->role !== UserRole::User->value || ! $user->travel_id) {
            return 0;
        }

        if (! Schema::hasTable('pengawasan_followups')) {
            return 0;
        }

        return Followup::query()
            ->where('status', FollowupStatus::RevisionRequired->value)
            ->whereHas('finding.inspection', fn ($q) => $q->where('travel_id', $user->travel_id))
            ->count();
    }

    /** BA Pemberangkatan yang masih draf: travel harus melengkapi lalu mengajukan. */
    private static function bapDraftCount(User $user): int
    {
        if ($user->role !== UserRole::User->value || ! Schema::hasTable('bap')) {
            return 0;
        }

        return BAP::query()
            ->where('user_id', $user->id)
            ->where('status', BapStatus::Pending->value)
            ->count();
    }

    /** Pemeriksaan yang dibuat user ini dan belum ditutup. */
    private static function inspeksiActiveCount(User $user): int
    {
        if (! in_array($user->role, [UserRole::Admin->value, UserRole::Pengawas->value], true)) {
            return 0;
        }

        if (! Schema::hasTable('pengawasan')) {
            return 0;
        }

        return Inspection::query()
            ->where('created_by', $user->id)
            ->whereIn('status', [
                InspectionStatus::Draft->value,
                InspectionStatus::Scheduled->value,
                InspectionStatus::OnProgress->value,
            ])
            ->count();
    }

    /** @param  list<array{key: string, count: int}>  $queues */
    private static function countFromQueues(array $queues, string $key): int
    {
        foreach ($queues as $queue) {
            if (($queue['key'] ?? '') === $key) {
                return (int) ($queue['count'] ?? 0);
            }
        }

        return 0;
    }
}
