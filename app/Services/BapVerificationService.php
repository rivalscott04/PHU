<?php

namespace App\Services;

use App\Models\BAP;

class BapVerificationService
{
    public function ensureTravelToken(BAP $bap): BAP
    {
        if ($bap->travel_token) {
            return $bap;
        }

        $bap->travel_token = $this->generateTravelToken($bap);
        $bap->save();

        return $bap->fresh();
    }

    public function ensureKanwilToken(BAP $bap): BAP
    {
        if ($bap->kanwil_token) {
            return $bap;
        }

        $bap->kanwil_token = $this->generateKanwilToken($bap);
        $bap->save();

        return $bap->fresh();
    }

    public function generateTravelToken(BAP $bap): string
    {
        $raw = hash('sha256', 'travel|'.$bap->id.'|'.$bap->user_id.'|'.$bap->ppiuname.'|'.$bap->created_at, true);

        return $this->encodeToken($raw, 20);
    }

    public function generateKanwilToken(BAP $bap): string
    {
        $raw = hash('sha256', 'kanwil|'.$bap->id.'|'.$bap->nomor_surat.'|'.$bap->updated_at, true);

        return $this->encodeToken($raw, 20);
    }

    public function combinedToken(BAP $bap): ?string
    {
        if (! $bap->travel_token || ! $bap->kanwil_token) {
            return null;
        }

        return $bap->travel_token.$bap->kanwil_token;
    }

    public function verificationUrl(string $token, int $bapId): string
    {
        $baseUrl = request()->getSchemeAndHttpHost();

        return $baseUrl.'/public/verify-e-sign?token='.urlencode($token).'&bap_id='.$bapId;
    }

    public function qrDataUri(string $verificationUrl): ?string
    {
        try {
            $qrCode = \Endroid\QrCode\QrCode::create($verificationUrl)
                ->setSize(300)
                ->setMargin(10);

            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);

            return 'data:image/png;base64,'.base64_encode($result->getString());
        } catch (\Throwable $e) {
            \Log::error('BAP QR generation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function matchesToken(BAP $bap, string $token): bool
    {
        $token = trim($token);

        if ($token === '') {
            return false;
        }

        if ($bap->travel_token && hash_equals($bap->travel_token, $token)) {
            return true;
        }

        $combined = $this->combinedToken($bap);

        return $combined !== null && hash_equals($combined, $token);
    }

    private function encodeToken(string $raw, int $length): string
    {
        return substr(strtr(base64_encode($raw), '+/=', 'xyz'), 0, $length);
    }
}
