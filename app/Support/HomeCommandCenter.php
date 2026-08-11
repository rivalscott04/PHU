<?php

namespace App\Support;

use App\Enums\BapStatus;
use App\Enums\FollowupStatus;
use App\Enums\RiskLevel;
use App\Enums\TravelRegistrationStatus;
use App\Models\BAP;
use App\Models\Followup;
use App\Models\Jamaah;
use App\Models\Pengaduan;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

final class HomeCommandCenter
{
    /** @var array<string, int> */
    private static array $countCache = [];

    /** @var array<string, list<array<string, mixed>>> */
    private static array $queueCache = [];
    /**
     * @return list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>
     */
    public static function adminQueues(): array
    {
        return self::queuesForScope(null);
    }

    /**
     * @return list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>
     */
    public static function queuesForScope(?string $kabupaten = null): array
    {
        return self::queueCards($kabupaten, includeRegistration: true);
    }

    /**
     * @return list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>
     */
    public static function kabupatenQueues(string $kabupaten): array
    {
        return self::queueCards($kabupaten, includeRegistration: false);
    }

    /**
     * @param  list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>  $queues
     * @return array{level: string, label: string, badge_class: string, message: string}
     */
    public static function overallStatus(array $queues): array
    {
        $registration = self::findQueueCount($queues, 'registration_pending');
        $bap = self::findQueueCount($queues, 'bap_pending');
        $pengaduan = self::findQueueCount($queues, 'pengaduan_open');
        $risk = self::findQueueCount($queues, 'risk_high');

        if ($risk > 0 || $registration > 5) {
            return [
                'level' => 'critical',
                'label' => 'Perlu Perhatian Segera',
                'badge_class' => 'bg-danger',
                'message' => 'Ada isu prioritas yang memerlukan tindakan segera.',
            ];
        }

        if ($bap > 0 || $pengaduan > 0 || $registration > 0) {
            return [
                'level' => 'warning',
                'label' => 'Ada Antrian',
                'badge_class' => 'bg-warning text-dark',
                'message' => 'Masih ada pekerjaan operasional yang perlu ditindaklanjuti.',
            ];
        }

        return [
            'level' => 'ok',
            'label' => 'Kondisi Normal',
            'badge_class' => 'bg-success',
            'message' => 'Tidak ada antrian kritis saat ini.',
        ];
    }

    /**
     * @return array{
     *     travel_name: ?string,
     *     registration_status: ?TravelRegistrationStatus,
     *     registration_notes: ?string,
     *     steps: list<array{label: string, done: bool, hint: string, url?: string, tone: string}>,
     *     stats: array{bap_diajukan: int, bap_diproses: int, bap_diterima: int, jamaah_total: int}
     * }
     */
    public static function travelChecklist(User $user): array
    {
        $user->loadMissing('travel');
        $travel = $user->travel;

        $registrationStatus = $travel?->registration_status ?? TravelRegistrationStatus::Approved;
        $isApproved = $travel === null || $registrationStatus === TravelRegistrationStatus::Approved;

        $jamaahTotal = 0;
        if ($travel && Schema::hasTable('jamaah')) {
            $jamaahTotal = Jamaah::query()->where('travel_id', $travel->id)->count();
        }

        $bapStats = Schema::hasTable('bap')
            ? BAP::query()
                ->where('user_id', $user->id)
                ->selectRaw("
                    SUM(CASE WHEN status = 'diajukan' THEN 1 ELSE 0 END) as diajukan,
                    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima
                ")
                ->first()
            : null;

        $bapDiajukan = (int) ($bapStats->diajukan ?? 0);
        $bapDiproses = (int) ($bapStats->diproses ?? 0);
        $bapDiterima = (int) ($bapStats->diterima ?? 0);

        $steps = [];

        if ($registrationStatus === TravelRegistrationStatus::Pending) {
            $steps[] = [
                'label' => 'Registrasi menunggu verifikasi Kanwil',
                'done' => false,
                'hint' => 'Tim Kanwil sedang memeriksa data dan dokumen Anda.',
                'tone' => 'warning',
            ];
        } elseif ($registrationStatus === TravelRegistrationStatus::Rejected) {
            $steps[] = [
                'label' => 'Registrasi ditolak',
                'done' => false,
                'hint' => $travel?->registration_notes ?: 'Silakan hubungi Kanwil atau daftar ulang.',
                'tone' => 'danger',
            ];
        } else {
            $steps[] = [
                'label' => 'Akun travel aktif',
                'done' => true,
                'hint' => $travel?->Penyelenggara ?: 'Registrasi disetujui',
                'tone' => 'success',
            ];
        }

        $steps[] = [
            'label' => 'Tambah data jamaah',
            'done' => $isApproved && $jamaahTotal > 0,
            'hint' => $jamaahTotal > 0
                ? "{$jamaahTotal} jamaah terdaftar"
                : 'Belum ada jamaah. Tambahkan minimal satu jamaah sebelum mengajukan BA.',
            'url' => $isApproved ? route('jamaah.umrah') : null,
            'tone' => $jamaahTotal > 0 ? 'success' : 'secondary',
        ];

        $hasActivePackage = $isApproved
            && Schema::hasTable('travel_packages')
            && $travel
            && \App\Models\TravelPackage::query()
                ->where('travel_id', $travel->id)
                ->where('is_active', true)
                ->exists();

        $steps[] = [
            'label' => 'Atur paket umrah',
            'done' => $hasActivePackage,
            'hint' => $hasActivePackage
                ? 'Paket aktif tersedia untuk pengisian BA otomatis.'
                : ($isApproved
                    ? 'Simpan harga standar agar form BA terisi otomatis.'
                    : 'Tersedia setelah registrasi disetujui.'),
            'url' => $isApproved ? route('travel.packages') : null,
            'tone' => $hasActivePackage ? 'success' : 'secondary',
        ];

        $steps[] = [
            'label' => 'Ajukan BA Pemberangkatan',
            'done' => $bapDiajukan + $bapDiproses + $bapDiterima > 0,
            'hint' => ($bapDiajukan + $bapDiproses + $bapDiterima) > 0
                ? "{$bapDiajukan} diajukan, {$bapDiproses} diproses, {$bapDiterima} diterima"
                : 'Pilih paket atau isi harga per orang, tanggal berangkat, dan maskapai.',
            'url' => $isApproved ? route('bap') : null,
            'tone' => $bapDiterima > 0 ? 'success' : (($bapDiajukan + $bapDiproses) > 0 ? 'warning' : 'secondary'),
        ];

        $steps[] = [
            'label' => 'Pantau jadwal keberangkatan',
            'done' => $bapDiterima > 0,
            'hint' => $bapDiterima > 0
                ? 'Lihat jadwal yang sudah disetujui'
                : 'Tersedia setelah BA Pemberangkatan disetujui.',
            'url' => $isApproved ? route('keberangkatan') : null,
            'tone' => $bapDiterima > 0 ? 'success' : 'secondary',
        ];

        return [
            'travel_name' => $travel?->Penyelenggara,
            'registration_status' => $registrationStatus,
            'registration_notes' => $travel?->registration_notes,
            'steps' => $steps,
            'stats' => [
                'bap_diajukan' => $bapDiajukan,
                'bap_diproses' => $bapDiproses,
                'bap_diterima' => $bapDiterima,
                'jamaah_total' => $jamaahTotal,
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, hint: string, tone: string, url: string, icon: string}>
     */
    public static function travelAlerts(User $user): array
    {
        $alerts = [];
        $user->loadMissing('travel');
        $travel = $user->travel;

        if ($travel?->license_expiry) {
            if ($travel->isLicenseExpired()) {
                $alerts[] = [
                    'key' => 'license_expired',
                    'label' => 'Izin PPIU sudah habis',
                    'hint' => 'Berakhir '.$travel->license_expiry->format('d/m/Y').'. Segera perpanjang izin operasional.',
                    'tone' => 'danger',
                    'url' => route('travel.certificates'),
                    'icon' => 'bx-error-circle',
                ];
            } elseif ($travel->license_expiry->lte(now()->addDays(60))) {
                $alerts[] = [
                    'key' => 'license_expiring',
                    'label' => 'Izin PPIU akan habis',
                    'hint' => 'Berlaku sampai '.$travel->license_expiry->format('d/m/Y').'. Persiapkan perpanjangan izin.',
                    'tone' => 'warning',
                    'url' => route('travel.certificates'),
                    'icon' => 'bx-time-five',
                ];
            }
        }

        if (Schema::hasTable('pengawasan_followups') && $user->travel_id) {
            $revisionCount = Followup::query()
                ->where('status', FollowupStatus::RevisionRequired->value)
                ->whereHas('finding.inspection', fn ($q) => $q->where('travel_id', $user->travel_id))
                ->count();

            if ($revisionCount > 0) {
                $alerts[] = [
                    'key' => 'followup_revision',
                    'label' => 'Tindak lanjut perlu revisi',
                    'hint' => $revisionCount.' temuan pemeriksaan membutuhkan perbaikan bukti.',
                    'tone' => 'danger',
                    'url' => route('v2.followup.index', ['status' => FollowupStatus::RevisionRequired->value]),
                    'icon' => 'bx-task',
                ];
            }
        }

        if (Schema::hasTable('bap')) {
            $draftCount = BAP::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();

            if ($draftCount > 0) {
                $alerts[] = [
                    'key' => 'bap_draft',
                    'label' => 'BA belum selesai diajukan',
                    'hint' => $draftCount.' pengajuan masih draft. Lengkapi PDF dan ajukan ke Kabupaten.',
                    'tone' => 'info',
                    'url' => route('bap'),
                    'icon' => 'bx-file',
                ];
            }
        }

        return $alerts;
    }

    /**
     * @return list<array{
     *     id: int,
     *     package: string,
     *     people: int,
     *     datetime: string,
     *     datetime_raw: ?string,
     *     status: string,
     *     badge_label: string,
     *     badge_class: string,
     *     url: string
     * }>
     */
    public static function recentActiveBap(User $user, int $limit = 5): array
    {
        if (! Schema::hasTable('bap')) {
            return [];
        }

        return BAP::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (BAP $bap) {
                $badge = \App\Support\BapWizardStatus::travelBadge($bap);
                $wizardRoute = \App\Support\BapWizardStatus::wizardRouteName($bap);
                $url = $wizardRoute
                    ? route($wizardRoute, $bap->id)
                    : route('detail.bap', $bap->id);

                return [
                    'id' => $bap->id,
                    'package' => $bap->package ?: 'Paket keberangkatan',
                    'people' => (int) $bap->people,
                    'datetime' => $bap->datetime
                        ? Carbon::parse($bap->datetime)->format('d/m/Y')
                        : '-',
                    'datetime_raw' => $bap->datetime,
                    'status' => $bap->status,
                    'badge_label' => $badge['label'],
                    'badge_class' => $badge['class'],
                    'url' => $url,
                ];
            })
            ->all();
    }

    /**
     * @return list<array{
     *     id: int,
     *     package: string,
     *     people: int,
     *     datetime: string,
     *     airlines: ?string,
     *     url: string
     * }>
     */
    public static function upcomingDepartures(User $user, int $limit = 3): array
    {
        if (! Schema::hasTable('bap')) {
            return [];
        }

        $user->loadMissing('travel');

        $query = BAP::query()
            ->where('status', 'diterima')
            ->where('datetime', '>=', now()->startOfDay())
            ->orderBy('datetime');

        if ($user->travel?->Penyelenggara) {
            $query->where('ppiuname', $user->travel->Penyelenggara);
        } else {
            $query->where('user_id', $user->id);
        }

        return $query
            ->limit($limit)
            ->get()
            ->map(fn (BAP $bap) => [
                'id' => $bap->id,
                'package' => $bap->package ?: 'Paket keberangkatan',
                'people' => (int) $bap->people,
                'datetime' => Carbon::parse($bap->datetime)->format('d/m/Y'),
                'airlines' => $bap->airlines,
                'url' => route('detail.bap', $bap->id),
            ])
            ->all();
    }

    /**
     * @return list<array{key: string, label: string, hint: string, tone: string, url: string, icon: string}>
     */
    public static function kabupatenAlerts(string $kabupaten): array
    {
        $alerts = [];
        $bapPending = self::countBapPending($kabupaten);

        if ($bapPending > 0) {
            $alerts[] = [
                'key' => 'bap_pending',
                'label' => 'BA menunggu persetujuan',
                'hint' => $bapPending.' pengajuan keberangkatan perlu ditinjau di wilayah Anda.',
                'tone' => 'warning',
                'url' => route('bap'),
                'icon' => 'bx-file',
            ];
        }

        if (Schema::hasTable('bap')) {
            $staleCount = self::countStaleBap($kabupaten);

            if ($staleCount > 0) {
                $alerts[] = [
                    'key' => 'bap_stale',
                    'label' => 'BA menunggu lebih dari 3 hari',
                    'hint' => $staleCount.' pengajuan sudah diajukan lebih dari 3 hari tanpa tindakan.',
                    'tone' => 'danger',
                    'url' => route('bap'),
                    'icon' => 'bx-time-five',
                ];
            }
        }

        $pengaduanOpen = self::countOpenPengaduan($kabupaten);

        if ($pengaduanOpen > 0) {
            $alerts[] = [
                'key' => 'pengaduan_open',
                'label' => 'Pengaduan belum selesai',
                'hint' => $pengaduanOpen.' pengaduan masih menunggu penanganan.',
                'tone' => 'danger',
                'url' => route('pengaduan'),
                'icon' => 'bx-message-square-dots',
            ];
        }

        return $alerts;
    }

    /**
     * @return array{
     *     jamaah_haji: int,
     *     jamaah_umrah: int,
     *     bap_diajukan: int,
     *     bap_diproses: int,
     *     bap_diterima: int
     * }
     */
    public static function kabupatenSummary(string $kabupaten): array
    {
        $currentMonth = now()->month;
        $summary = [
            'jamaah_haji' => 0,
            'jamaah_umrah' => 0,
            'bap_diajukan' => 0,
            'bap_diproses' => 0,
            'bap_diterima' => 0,
        ];

        if (Schema::hasTable('jamaah') && Schema::hasTable('travels')) {
            $jamaahQuery = Jamaah::query()
                ->join('travels', 'jamaah.travel_id', '=', 'travels.id')
                ->whereMonth('jamaah.created_at', $currentMonth);

            self::applyKabupatenColumn($jamaahQuery, 'travels.kab_kota', $kabupaten);

            $jamaahStats = $jamaahQuery
                ->selectRaw("
                    SUM(CASE WHEN jamaah.jenis_jamaah = 'haji' THEN 1 ELSE 0 END) as haji,
                    SUM(CASE WHEN jamaah.jenis_jamaah = 'umrah' THEN 1 ELSE 0 END) as umrah
                ")
                ->first();

            $summary['jamaah_haji'] = (int) ($jamaahStats->haji ?? 0);
            $summary['jamaah_umrah'] = (int) ($jamaahStats->umrah ?? 0);
        }

        if (Schema::hasTable('bap')) {
            $bapQuery = BAP::query();
            self::applyKabupatenColumn($bapQuery, 'kab_kota', $kabupaten);

            $bapStats = $bapQuery
                ->selectRaw("
                    SUM(CASE WHEN status = 'diajukan' THEN 1 ELSE 0 END) as diajukan,
                    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima
                ")
                ->first();

            $summary['bap_diajukan'] = (int) ($bapStats->diajukan ?? 0);
            $summary['bap_diproses'] = (int) ($bapStats->diproses ?? 0);
            $summary['bap_diterima'] = (int) ($bapStats->diterima ?? 0);
        }

        return $summary;
    }

    /**
     * @return list<array{
     *     id: int,
     *     travel_name: string,
     *     package: string,
     *     people: int,
     *     datetime: string,
     *     status: string,
     *     badge_label: string,
     *     badge_class: string,
     *     created_at: string,
     *     url: string
     * }>
     */
    public static function recentPendingBap(?string $kabupaten, int $limit = 5): array
    {
        if (! Schema::hasTable('bap')) {
            return [];
        }

        $query = BAP::query()
            ->with('user.travel')
            ->whereIn('status', ['diajukan', 'diproses'])
            ->latest();

        if ($kabupaten) {
            self::applyKabupatenColumn($query, 'kab_kota', $kabupaten);
        }

        return $query->limit($limit)->get()->map(function (BAP $bap) {
            $badge = match ($bap->status) {
                'diajukan' => ['label' => 'Diajukan', 'class' => 'bg-primary text-white'],
                'diproses' => ['label' => 'Diproses', 'class' => 'bg-warning text-dark'],
                default => ['label' => BapStatus::labelFor($bap->status), 'class' => 'bg-secondary text-white'],
            };

            return [
                'id' => $bap->id,
                'travel_name' => $bap->user?->travel?->Penyelenggara ?? $bap->ppiuname ?? 'Travel',
                'kabupaten' => $bap->kab_kota ?: '-',
                'package' => $bap->package ?: 'Paket keberangkatan',
                'people' => (int) $bap->people,
                'datetime' => $bap->datetime
                    ? Carbon::parse($bap->datetime)->format('d/m/Y')
                    : '-',
                'status' => $bap->status,
                'badge_label' => $badge['label'],
                'badge_class' => $badge['class'],
                'created_at' => $bap->created_at?->format('d/m/Y') ?? '-',
                'url' => route('detail.bap', $bap->id),
            ];
        })->all();
    }

    /**
     * @return list<array{
     *     id: int,
     *     travel_name: string,
     *     subject: string,
     *     status: string,
     *     badge_label: string,
     *     badge_class: string,
     *     created_at: string,
     *     url: string
     * }>
     */
    public static function recentOpenPengaduan(?string $kabupaten = null, int $limit = 5): array
    {
        if (! Schema::hasTable('pengaduan')) {
            return [];
        }

        $query = Pengaduan::query()
            ->with('travel')
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest();

        if ($kabupaten) {
            KabupatenScopeFilter::applyOnTravelRelation(
                $query,
                self::kabupatenFilters($kabupaten)
            );
        }

        return $query->limit($limit)->get()->map(fn (Pengaduan $pengaduan) => [
            'id' => $pengaduan->id,
            'travel_name' => $pengaduan->travel?->Penyelenggara ?? 'Travel',
            'subject' => $pengaduan->hal_aduan ?: 'Pengaduan',
            'status' => $pengaduan->status,
            'badge_label' => $pengaduan->getStatusLabel(),
            'badge_class' => str_replace('badge ', '', $pengaduan->getStatusBadgeClass()),
            'created_at' => $pengaduan->created_at?->format('d/m/Y') ?? '-',
            'url' => route('pengaduan.show', $pengaduan->id),
        ])->all();
    }

    /**
     * @return list<array{
     *     id: int,
     *     travel_name: string,
     *     package: string,
     *     people: int,
     *     datetime: string,
     *     url: string
     * }>
     */
    public static function upcomingDeparturesForKabupaten(?string $kabupaten = null, int $limit = 3): array
    {
        if (! Schema::hasTable('bap')) {
            return [];
        }

        $query = BAP::query()
            ->with('user.travel')
            ->where('status', 'diterima')
            ->where('datetime', '>=', now()->startOfDay())
            ->orderBy('datetime');

        if ($kabupaten) {
            self::applyKabupatenColumn($query, 'kab_kota', $kabupaten);
        }

        return $query->limit($limit)->get()->map(fn (BAP $bap) => [
            'id' => $bap->id,
            'travel_name' => $bap->user?->travel?->Penyelenggara ?? $bap->ppiuname ?? 'Travel',
            'package' => $bap->package ?: 'Paket keberangkatan',
            'people' => (int) $bap->people,
            'datetime' => Carbon::parse($bap->datetime)->format('d/m/Y'),
            'url' => route('detail.bap', $bap->id),
        ])->all();
    }

    /**
     * @return list<array{key: string, label: string, hint: string, tone: string, url: string, icon: string}>
     */
    public static function adminAlerts(): array
    {
        $alerts = [];

        $registration = self::countRegistrationPending();
        if ($registration > 0) {
            $alerts[] = [
                'key' => 'registration_pending',
                'label' => 'Registrasi travel menunggu verifikasi',
                'hint' => $registration.' pendaftaran PPIU baru perlu ditinjau Kanwil.',
                'tone' => 'warning',
                'url' => route('travel', ['filter' => 'pending']),
                'icon' => 'bx-user-plus',
            ];
        }

        $bapPending = self::countBapPending(null);
        if ($bapPending > 0) {
            $alerts[] = [
                'key' => 'bap_pending',
                'label' => 'BA menunggu persetujuan',
                'hint' => $bapPending.' pengajuan keberangkatan belum selesai diproses di NTB.',
                'tone' => 'warning',
                'url' => route('bap'),
                'icon' => 'bx-file',
            ];
        }

        if (Schema::hasTable('bap')) {
            $staleCount = self::countStaleBap(null);

            if ($staleCount > 0) {
                $alerts[] = [
                    'key' => 'bap_stale',
                    'label' => 'BA menunggu lebih dari 3 hari',
                    'hint' => $staleCount.' pengajuan sudah diajukan lebih dari 3 hari tanpa tindakan.',
                    'tone' => 'danger',
                    'url' => route('bap'),
                    'icon' => 'bx-time-five',
                ];
            }
        }

        $pengaduanOpen = self::countOpenPengaduan(null);
        if ($pengaduanOpen > 0) {
            $alerts[] = [
                'key' => 'pengaduan_open',
                'label' => 'Pengaduan belum selesai',
                'hint' => $pengaduanOpen.' pengaduan masih menunggu penanganan.',
                'tone' => 'danger',
                'url' => route('pengaduan'),
                'icon' => 'bx-message-square-dots',
            ];
        }

        $riskHigh = self::countHighRisk(null);
        if ($riskHigh > 0) {
            $alerts[] = [
                'key' => 'risk_high',
                'label' => 'Travel berisiko tinggi',
                'hint' => $riskHigh.' PPIU berstatus HIGH/CRITICAL perlu pemantauan.',
                'tone' => 'danger',
                'url' => route('v2.risk.index'),
                'icon' => 'bx-shield-quarter',
            ];
        }

        return $alerts;
    }

    /**
     * @return array{
     *     registration_pending: int,
     *     bap_pending: int,
     *     pengaduan_open: int,
     *     risk_high: int,
     *     jamaah_bulan_ini: int
     * }
     */
    public static function adminSummary(): array
    {
        $jamaahBulanIni = 0;

        if (Schema::hasTable('jamaah')) {
            $jamaahBulanIni = Jamaah::query()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
        }

        return [
            'registration_pending' => self::countRegistrationPending(),
            'bap_pending' => self::countBapPending(null),
            'pengaduan_open' => self::countOpenPengaduan(null),
            'risk_high' => self::countHighRisk(null),
            'jamaah_bulan_ini' => $jamaahBulanIni,
        ];
    }

    /**
     * @return list<array{
     *     id: int,
     *     name: string,
     *     kabupaten: string,
     *     created_at: string,
     *     url: string
     * }>
     */
    public static function recentPendingRegistrations(int $limit = 5): array
    {
        return TravelCompany::pendingRegistration()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (TravelCompany $travel) => [
                'id' => $travel->id,
                'name' => $travel->Penyelenggara ?: 'Travel',
                'kabupaten' => $travel->kab_kota ?: '-',
                'created_at' => $travel->created_at?->format('d/m/Y') ?? '-',
                'url' => route('travel', ['filter' => 'pending']),
            ])
            ->all();
    }

    /**
     * @return list<array{
     *     travel_name: string,
     *     kabupaten: string,
     *     risk_label: string,
     *     badge_class: string,
     *     score: float,
     *     url: string
     * }>
     */
    public static function recentHighRiskTravels(int $limit = 5): array
    {
        if (! Schema::hasTable('risk_scores')) {
            return [];
        }

        return RiskScore::query()
            ->with('travel')
            ->whereIn('risk_level', ['HIGH', 'CRITICAL'])
            ->orderByDesc('total_score')
            ->limit($limit)
            ->get()
            ->map(function (RiskScore $score) {
                $level = $score->risk_level instanceof RiskLevel
                    ? $score->risk_level->value
                    : (string) $score->risk_level;

                return [
                    'travel_name' => $score->travel?->Penyelenggara ?? 'Travel',
                    'kabupaten' => $score->travel?->kab_kota ?? '-',
                    'risk_label' => RiskLevel::labelFor($level),
                    'badge_class' => 'bg-'.RiskLevel::badgeFor($level),
                    'score' => (float) $score->total_score,
                    'url' => $score->travel
                        ? route('v2.risk.show', $score->travel)
                        : route('v2.risk.index'),
                ];
            })
            ->all();
    }

    /**
     * @return list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>
     */
    public static function pimpinanQueues(?string $kabupaten = null): array
    {
        return array_map(function (array $card): array {
            return match ($card['key']) {
                'registration_pending' => array_merge($card, [
                    'route' => 'travel',
                    'params' => ['filter' => 'pending'],
                    'hint' => 'Pendaftaran PPIU menunggu verifikasi',
                ]),
                'bap_pending' => array_merge($card, [
                    'route' => 'v2.monitoring.index',
                    'params' => [],
                    'hint' => 'Pantau pengajuan keberangkatan via Monitoring',
                ]),
                'pengaduan_open' => array_merge($card, [
                    'route' => 'v2.monitoring.index',
                    'params' => [],
                    'hint' => 'Lihat detail pengaduan per wilayah',
                ]),
                'risk_high' => array_merge($card, [
                    'route' => 'v2.monitoring.index',
                    'params' => [],
                    'hint' => 'Profil risiko penyelenggara di Monitoring',
                ]),
                default => $card,
            };
        }, self::queueCards($kabupaten, includeRegistration: true));
    }

    /**
     * @return list<array{key: string, label: string, hint: string, tone: string, url: string, icon: string}>
     */
    public static function pimpinanAlerts(?string $kabupaten = null): array
    {
        $alerts = [];
        $monitoringUrl = route('v2.monitoring.index');
        $pengaduanUrl = $kabupaten
            ? route('v2.monitoring.kabupaten.pengaduan', ['kabupaten' => $kabupaten])
            : $monitoringUrl;

        $registration = self::countRegistrationPending($kabupaten);
        if ($registration > 0) {
            $alerts[] = [
                'key' => 'registration_pending',
                'label' => 'Registrasi travel menunggu verifikasi',
                'hint' => $registration.' pendaftaran PPIU perlu ditinjau tim operasional.',
                'tone' => 'warning',
                'url' => route('travel', ['filter' => 'pending']),
                'icon' => 'bx-user-plus',
            ];
        }

        $riskHigh = self::countHighRisk($kabupaten);
        if ($riskHigh > 0) {
            $alerts[] = [
                'key' => 'risk_high',
                'label' => 'Travel berisiko tinggi',
                'hint' => $riskHigh.' PPIU berstatus HIGH/CRITICAL perlu pemantauan.',
                'tone' => 'danger',
                'url' => $monitoringUrl,
                'icon' => 'bx-shield-quarter',
            ];
        }

        $pengaduanOpen = self::countOpenPengaduan($kabupaten);
        if ($pengaduanOpen > 0) {
            $alerts[] = [
                'key' => 'pengaduan_open',
                'label' => 'Pengaduan belum selesai',
                'hint' => $pengaduanOpen.' pengaduan masih terbuka di wilayah terpilih.',
                'tone' => 'danger',
                'url' => $pengaduanUrl,
                'icon' => 'bx-message-square-dots',
            ];
        }

        if (Schema::hasTable('bap')) {
            $staleCount = self::countStaleBap($kabupaten);
            if ($staleCount > 0) {
                $alerts[] = [
                    'key' => 'bap_stale',
                    'label' => 'BA menunggu lebih dari 3 hari',
                    'hint' => $staleCount.' pengajuan keberangkatan sudah lama tanpa tindakan operasional.',
                    'tone' => 'warning',
                    'url' => $monitoringUrl,
                    'icon' => 'bx-time-five',
                ];
            }
        }

        return $alerts;
    }

    /**
     * @return list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>
     */
    private static function queueCards(?string $kabupaten, bool $includeRegistration): array
    {
        $cacheKey = ($kabupaten ?? '__all__').':'.($includeRegistration ? '1' : '0');

        if (isset(self::$queueCache[$cacheKey])) {
            return self::$queueCache[$cacheKey];
        }

        $cards = [];

        if ($includeRegistration) {
            $cards[] = [
                'key' => 'registration_pending',
                'label' => 'Registrasi Travel',
                'count' => self::countRegistrationPending($kabupaten),
                'route' => 'travel',
                'params' => ['filter' => 'pending'],
                'color' => '#f1b44c',
                'icon' => 'bx-user-plus',
                'hint' => 'Menunggu verifikasi Kanwil',
            ];
        }

        $cards[] = [
            'key' => 'bap_pending',
            'label' => 'BA Pemberangkatan',
            'count' => self::countBapPending($kabupaten),
            'route' => 'bap',
            'color' => '#556ee6',
            'icon' => 'bx-file',
            'hint' => 'Status diajukan atau diproses',
        ];

        $cards[] = [
            'key' => 'pengaduan_open',
            'label' => 'Pengaduan',
            'count' => self::countOpenPengaduan($kabupaten),
            'route' => 'pengaduan',
            'color' => '#f46a6a',
            'icon' => 'bx-message-square-dots',
            'hint' => 'Belum selesai diproses',
        ];

        if ($includeRegistration) {
            $cards[] = [
                'key' => 'risk_high',
                'label' => 'Risiko Tinggi',
                'count' => self::countHighRisk($kabupaten),
                'route' => 'v2.risk.index',
                'color' => '#343a40',
                'icon' => 'bx-shield-quarter',
                'hint' => 'Travel berisiko HIGH/CRITICAL',
            ];
        }

        return self::$queueCache[$cacheKey] = $cards;
    }

    public static function countRegistrationPending(?string $kabupaten = null): int
    {
        $cacheKey = 'registration_pending:'.($kabupaten ?? '__all__');

        return self::cachedCount($cacheKey, function () use ($kabupaten): int {
            $query = TravelCompany::pendingRegistration();

            if ($kabupaten) {
                $query->where('kab_kota', $kabupaten);
            }

            return $query->count();
        });
    }

    public static function countBapPendingForScope(?string $kabupaten = null): int
    {
        return self::countBapPending($kabupaten);
    }

    private static function countBapPending(?string $kabupaten): int
    {
        if (! Schema::hasTable('bap')) {
            return 0;
        }

        $cacheKey = 'bap_pending:'.($kabupaten ?? '__all__');

        return self::cachedCount($cacheKey, function () use ($kabupaten): int {
            $query = BAP::query()->whereIn('status', ['diajukan', 'diproses', 'pending']);

            if ($kabupaten) {
                self::applyKabupatenColumn($query, 'kab_kota', $kabupaten);
            }

            return $query->count();
        });
    }

    private static function countOpenPengaduan(?string $kabupaten): int
    {
        if (! Schema::hasTable('pengaduan')) {
            return 0;
        }

        $cacheKey = 'pengaduan_open:'.($kabupaten ?? '__all__');

        return self::cachedCount($cacheKey, function () use ($kabupaten): int {
            $query = Pengaduan::query()->whereIn('status', ['pending', 'in_progress']);

            if ($kabupaten) {
                KabupatenScopeFilter::applyOnTravelRelation($query, self::kabupatenFilters($kabupaten));
            }

            return $query->count();
        });
    }

    private static function countHighRisk(?string $kabupaten): int
    {
        if (! Schema::hasTable('risk_scores')) {
            return 0;
        }

        $cacheKey = 'risk_high:'.($kabupaten ?? '__all__');

        return self::cachedCount($cacheKey, function () use ($kabupaten): int {
            $query = RiskScore::query()->whereIn('risk_level', ['HIGH', 'CRITICAL']);

            if ($kabupaten) {
                $travelQuery = TravelCompany::query();
                self::applyKabupatenColumn($travelQuery, 'kab_kota', $kabupaten);
                $query->whereIn('travel_id', $travelQuery->select('id'));
            }

            return $query->count();
        });
    }

    /** Cache di atas hanya berlaku per-request; tes berbagi proses, jadi perlu direset. */
    public static function flushCountCache(): void
    {
        self::$countCache = [];
        self::$queueCache = [];
    }

    private static function cachedCount(string $key, callable $resolver): int
    {
        if (! array_key_exists($key, self::$countCache)) {
            self::$countCache[$key] = (int) $resolver();
        }

        return self::$countCache[$key];
    }

    private static function applyKabupatenColumn(Builder $query, string $column, string $kabupaten): void
    {
        $values = NtbKabupatenMap::queryValues($kabupaten);

        if ($values === []) {
            $query->where($column, $kabupaten);

            return;
        }

        if (count($values) === 1) {
            $query->where($column, $values[0]);

            return;
        }

        $query->whereIn($column, $values);
    }

    /** @return array<string, mixed> */
    private static function kabupatenFilters(string $kabupaten): array
    {
        $values = NtbKabupatenMap::queryValues($kabupaten);

        if ($values === []) {
            return ['kabupaten' => $kabupaten];
        }

        return count($values) === 1
            ? ['kabupaten' => $values[0]]
            : ['kabupatens' => $values];
    }

    /** @return Builder<BAP> */
    private static function staleBapQuery(?string $kabupaten = null): Builder
    {
        $query = BAP::query()
            ->where('status', 'diajukan')
            ->where('created_at', '<=', now()->subDays(3));

        if ($kabupaten) {
            self::applyKabupatenColumn($query, 'kab_kota', $kabupaten);
        }

        return $query;
    }

    private static function countStaleBap(?string $kabupaten): int
    {
        if (! Schema::hasTable('bap')) {
            return 0;
        }

        $cacheKey = 'bap_stale:'.($kabupaten ?? '__all__');

        return self::cachedCount($cacheKey, fn (): int => self::staleBapQuery($kabupaten)->count());
    }

    /**
     * @param  list<array{key: string, label: string, count: int, route: string, params?: array<string, mixed>, color: string, icon: string, hint: string}>  $queues
     */
    private static function findQueueCount(array $queues, string $key): int
    {
        foreach ($queues as $queue) {
            if (($queue['key'] ?? '') === $key) {
                return (int) ($queue['count'] ?? 0);
            }
        }

        return 0;
    }
}
