<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CatBiometricService;

class IndexCatBiometricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cats:index-biometrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index high-accuracy spatial fingerprints, colors, and hashes for all cat photos across all tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting biometric indexing across all cat tables...');

        $results = CatBiometricService::indexAllMissingData();

        $this->info("Indexing completed successfully!");
        $this->table(
            ['Source Table', 'Indexed Records'],
            [
                ['PTMA Cat Censuses (ptma_cat_censuses)', $results['census']],
                ['Member Cats (cats)', $results['cats']],
                ['Member Cat Gallery (cat_photos)', $results['cat_photos']],
                ['Stray Cat Surveys (stray_cat_surveys)', $results['surveys']],
            ]
        );

        return 0;
    }
}
