<?php

namespace App\Observers;

use App\Models\Inspection;
use App\Models\InspectionStatusLog;
use BackedEnum;

class InspectionObserver
{
    public function created(Inspection $inspection): void
    {
        $this->record($inspection, null, $inspection->status, $inspection->created_by);
    }

    public function updated(Inspection $inspection): void
    {
        if (! $inspection->wasChanged('status')) {
            return;
        }

        $this->record(
            $inspection,
            $inspection->getRawOriginal('status'),
            $inspection->status,
            $inspection->updated_by,
        );
    }

    private function record(Inspection $inspection, mixed $from, mixed $to, ?int $fallbackUserId): void
    {
        InspectionStatusLog::create([
            'inspection_id' => $inspection->id,
            'from_status' => $this->plain($from),
            'to_status' => $this->plain($to),
            'created_by' => auth()->id() ?? $fallbackUserId,
        ]);
    }

    private function plain(mixed $status): ?string
    {
        return $status instanceof BackedEnum ? $status->value : $status;
    }
}
