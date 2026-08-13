<?php

namespace App\Models\Concerns;

use App\Enums\TravelRegistrationStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Status pendaftaran yang dipakai bersama travel pusat dan cabang.
 * Keduanya menyimpan kolom registration_status yang sama.
 */
trait HasRegistrationStatus
{
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('registration_status', TravelRegistrationStatus::Approved);
    }

    public function scopePendingRegistration(Builder $query): Builder
    {
        return $query->where('registration_status', TravelRegistrationStatus::Pending);
    }

    public function isRegistrationPending(): bool
    {
        return $this->registration_status === TravelRegistrationStatus::Pending;
    }

    public function isAwaitingKanwil(): bool
    {
        return $this->registration_status === TravelRegistrationStatus::MenungguKanwil;
    }

    public function isRegistrationApproved(): bool
    {
        return $this->registration_status === TravelRegistrationStatus::Approved;
    }

    public function isRegistrationRejected(): bool
    {
        return $this->registration_status === TravelRegistrationStatus::Rejected;
    }

    /** Masih berjalan di meja verifikator, belum ada keputusan akhir. */
    public function isRegistrationOpen(): bool
    {
        return $this->isRegistrationPending() || $this->isAwaitingKanwil();
    }
}
