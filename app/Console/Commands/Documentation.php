<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class Documentation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puller:documentation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Documentacion , ';

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
        $yaml = Yaml::parseFile(public_path('swagger/openapi.yaml'));

        $basecomponent = Yaml::parseFile(public_path('swagger/resources/Usuarios.yaml'));
        $basecomponent_id = Yaml::parseFile(public_path('swagger/resources/Usuarios-by-id.yaml'));


        $raiz = [];

        foreach ($yaml['components']['schemas'] as $nombre => $schema) {
            $nc = $basecomponent;

            $nc['post']['tags'] = [$nombre];
            $nc['post']['summary'] = "Crear un nuevo {$nombre}";
            $nc['post']['requestBody']['content']['application/json']['schema']['$ref'] = "../schemas/{$nombre}";
            $nc['post']['responses'][200]['content']['application/json']['schema']['$ref'] = "../schemas/{$nombre}";

            $nc['get']['tags'] = [$nombre];
            $nc['get']['summary'] = "Lista {$nombre}";
            $nc['get']['description'] = "Lista {$nombre}";
            $nc['get']['responses'][200]['content']['application/json']['schema']['type'] = "array";
            $nc['get']['responses'][200]['content']['application/json']['schema']['items']['$ref'] = "../schemas/{$nombre}";
            $nc['get']['responses'][200]['content']['application/json']['schema']['maxItems'] = 10;

            $yaml = Yaml::dump($nc,2,2);

            file_put_contents(public_path("swagger/resources/{$nombre}.yaml"), $yaml);

            $nc_id = $basecomponent_id;

            $nc_id['get']['tags'] = [$nombre];
            $nc_id['get']['summary'] = "Obtiene el  {$nombre}";
            $nc_id['get']['description'] = "Obtiene el  {$nombre}";
            $nc_id['get']['responses'][200]['content']['application/json']['schema'] = ['$ref' => "../schemas/{$nombre}"];

            $nc_id['put']['tags'] = [$nombre];
            $nc_id['put']['summary'] = "Actualiza el   {$nombre}";
            $nc_id['put']['description'] = "Actualiza el  {$nombre}";
            $nc_id['put']['requestBody']['content']['application/json']['schema'] = ['$ref' => "../schemas/{$nombre}"];
            $nc_id['put']['responses'][200]['content']['application/json']['schema'] = ['$ref' => "../schemas/{$nombre}"];

            $nc_id['delete']['tags'] = [$nombre];
            $nc_id['delete']['summary'] = "Borra el   {$nombre}";
            $nc_id['delete']['description'] = "Borra el  {$nombre}";
            $nc_id['delete']['responses'][200]['content']['application/json']['schema'] = ['$ref' => "../schemas/{$nombre}"];

            $yaml2 = Yaml::dump($nc_id,2,2);
            file_put_contents(public_path("swagger/resources/{$nombre}-by-id.yaml"), $yaml2);


            $raiz["/{$nombre}"]['$ref'] = "./resources/{$nombre}.yaml";
            $raiz["/{$nombre}/{id}"]['$ref'] = "./resources/{$nombre}-by-id.yaml";


        }

        $yaml3 = Yaml::dump($raiz,2,2);
        file_put_contents(public_path('swagger/raiz.yaml'), $yaml3);

    }
}
