<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePoster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-poster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the KucingMu event PDF poster';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating poster-kucingmu.pdf...');

        $pdf = Pdf::loadView('pdf.poster');
        $pdf->setPaper('a4', 'portrait');
        $pdf->save(base_path('poster-kucingmu.pdf'));

        $this->info('Success! Poster saved to ' . base_path('poster-kucingmu.pdf'));
    }
}
