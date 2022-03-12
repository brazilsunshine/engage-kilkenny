<?php

namespace App\Console\Commands\Engage;

use App\Models\Engage\BuildingsData;
use Illuminate\Console\Command;

class MigrateFileToSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'engage:migrate-file-to-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate the buildings.geojson file to BuildingsData.php';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = file_get_contents(public_path('/js/geojson/buildings.geojson'));

        $contents = json_decode($file, true);

        $features = $contents['features'];

        foreach ($features as $feature)
        {
            $properties = $feature['properties'];

            foreach ($properties as $key => $value)
            {
                if (!$value)
                {
                    unset($properties[$key]);
                }
            }

            $coordinates = json_encode($feature['geometry']['coordinates']);
            $attributes = json_encode($properties);

            BuildingsData::create([
                'osm_id' => $properties['osm_id'],
                'attributes' => $attributes,
                'polygon' => $coordinates
            ]);

        }
    }
}
