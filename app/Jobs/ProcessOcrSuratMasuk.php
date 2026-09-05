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

        $fullPath = Storage::disk('local')->path(str_replace('arsip_pdf/', '', $this->filePath));
        if (!file_exists($fullPath)) {
            // Try absolute path directly
            $fullPath = Storage::disk('local')->path('arsip_pdf/' . basename($this->filePath));
        }
        if (!file_exists($fullPath)) {
            Log::error("ProcessOcrSuratMasuk: File not found at {$fullPath}");
            return;
        }

        $pdfBase64 = base64_encode(file_get_contents($fullPath));
        $prompt = "Kamu adalah sistem AI pintar kearsipan dinas kepolisian. Tugasmu wajib menganalisis file dokumen visual PDF/Scan terlampir, lakukan OCR, dan kembalikan HANYA teks mentah dari dokumen tersebut (full text content) tanpa format apapun.";

        try {
            $response = Http::withoutVerifying()->timeout(180)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [
                        ['text' => $prompt],
                        ['inlineData' => ['mimeType' => 'application/pdf', 'data' => $pdfBase64]]
                    ]]],
                ]);

            if ($response->failed()) {
                Log::error("ProcessOcrSuratMasuk: Gemini API error for surat #{$this->suratMasukId}: " . $response->body());
                return;
            }

            $text = $response->json('candidates.0.content.parts.0.text');
            if ($text) {
                SuratMasuk::where('id', $this->suratMasukId)
                    ->update(['full_text_content' => $text]);
            }
        } catch (\Exception $e) {
            Log::error("ProcessOcrSuratMasuk exception for surat #{$this->suratMasukId}: " . $e->getMessage());
            throw $e; // allow retry
        }
    }
}
