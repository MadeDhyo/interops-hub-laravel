<?php

namespace App\Jobs;

use App\Models\SuratKeluar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractPdfTextSuratKeluar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 2;

    public function __construct(
        private int    $suratKeluarId,
        private string $fileName
    ) {}

    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path('arsip_pdf/' . $this->fileName);
        if (!file_exists($fullPath)) {
            Log::warning("ExtractPdfTextSuratKeluar: File not found at {$fullPath}");
            return;
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();

            if ($text) {
                SuratKeluar::where('id', $this->suratKeluarId)
                    ->update(['full_text_content' => $text]);
            }
        } catch (\Exception $e) {
            Log::error("ExtractPdfTextSuratKeluar failed for #{$this->suratKeluarId}: " . $e->getMessage());
        }
    }
}
