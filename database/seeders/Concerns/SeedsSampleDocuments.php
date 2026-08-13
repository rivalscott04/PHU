<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Berkas contoh untuk seeder development. Tanpa ini tombol pratinjau dokumen
 * di layar verifikasi tidak punya apa apa untuk ditampilkan.
 */
trait SeedsSampleDocuments
{
    protected function sampleDocument(string $path): string
    {
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $this->placeholderPdf());
        }

        return $path;
    }

    private function placeholderPdf(): string
    {
        return "%PDF-1.4\n"
            . "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
            . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
            . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]>>endobj\n"
            . "trailer<</Root 1 0 R>>\n"
            . "%%EOF\n";
    }
}
