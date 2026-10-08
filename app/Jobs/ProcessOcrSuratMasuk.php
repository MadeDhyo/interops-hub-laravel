<?php

namespace App\Jobs;

use App\Models\SuratMasuk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessOcrSuratMasuk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout    = 240;
    public int $tries      = 2;
    public int $backoff    = 10;

    public function __construct(
        private int    $suratMasukId,
        private string $filePath
    ) {}

    public function handle(): void
    {
        $apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        if (!$apiKey) {
            Log::warning("ProcessOcrSuratMasuk: GEMINI_API_KEY not set, skipping OCR for surat #{$this->suratMasukId}");
            return;
        }

        $fullPath = Storage::disk('local')->path($this->filePath);
        if (!file_exists($fullPath)) {
            Log::error("ProcessOcrSuratMasuk: File not found at {$fullPath}");
            return;
        }

        $pdfBase64 = base64_encode(file_get_contents($fullPath));
        $prompt = "Kamu adalah sistem OCR kearsipan instansi kepolisian. Dokumen terlampir adalah surat dinas resmi berbahasa Indonesia. "
            . "Ekstrak dan kembalikan SELURUH TEKS dari dokumen tersebut dalam Bahasa Indonesia, termasuk: nomor surat, tanggal, perihal, isi surat, dan nama penandatangan. "
            . "Abaikan: cap basah/stempel, background watermark, header/footer halaman, dan metadata file. "
            . "Batas output maksimum: 8000 karakter.";

        try {
            $payload = [
                'contents' => [['parts' => [
                    ['text' => $prompt],
                    ['inlineData' => ['mimeType' => 'application/pdf', 'data' => $pdfBase64]]
                ]]],
                'generationConfig' => [
                    'thinkingConfig' => ['thinkingBudget' => 0]
                ]
            ];

            $response = Http::withoutVerifying()->timeout(120)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}", $payload);

            if ($response->failed()) {
                $response = Http::withoutVerifying()->timeout(120)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", $payload);
            }

            if ($response->failed()) {
                Log::error("ProcessOcrSuratMasuk: Gemini API error for surat #{$this->suratMasukId}: " . $response->body());
                return;
            }

            $text = $response->json('candidates.0.content.parts.0.text');
            if ($text) {
                SuratMasuk::where('id', $this->suratMasukId)
                    ->update(['full_text_content' => mb_substr($text, 0, 8000)]);
            }
        } catch (\Exception $e) {
            Log::error("ProcessOcrSuratMasuk exception for surat #{$this->suratMasukId}: " . $e->getMessage());
            throw $e; // allow retry
        }
    }
}
