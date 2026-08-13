<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Kepemilikan data operasional milik akun travel.
 *
 * Pusat dan cabang diisolasi penuh: pusat tidak melihat data cabangnya, cabang
 * tidak melihat data pusat maupun cabang lain. Koordinasi antar mereka urusan
 * di luar sistem, misalnya saling kirim hasil ekspor.
 *
 * travel_id menunjuk pemegang izin PPIU dan dipakai untuk menentukan jenis
 * jamaah yang boleh dikelola. cabang_id yang menentukan pemilik barisnya, dan
 * NULL berarti milik kantor pusat.
 */
final class OperatorScope
{
    /** Kolom pemilik untuk baris baru yang dibuat akun travel. */
    public static function ownerColumns(User $user): array
    {
        return [
            'travel_id' => $user->operatingTravelId(),
            'cabang_id' => $user->cabang_id,
        ];
    }

    /** Batasi query hanya pada baris milik akun ini. */
    public static function apply(Builder $query, User $user, string $prefix = ''): Builder
    {
        $travelColumn = $prefix . 'travel_id';
        $cabangColumn = $prefix . 'cabang_id';

        $query->where($travelColumn, $user->operatingTravelId());

        return $user->cabang_id
            ? $query->where($cabangColumn, $user->cabang_id)
            : $query->whereNull($cabangColumn);
    }

    /** Apakah baris ini milik akun tersebut. */
    public static function owns(User $user, ?int $travelId, ?int $cabangId): bool
    {
        return (int) $travelId === (int) $user->operatingTravelId()
            && (int) $cabangId === (int) $user->cabang_id;
    }
}
