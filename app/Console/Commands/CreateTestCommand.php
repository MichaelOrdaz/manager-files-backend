<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puller-test:create {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando crea archivos utilizados para test de características de un recurso';

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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStubCrud()
    {
      return __DIR__.'/Stubs/model-crud-test.stub';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStubRelaciones()
    {
      return __DIR__.'/Stubs/model-relaciones-test.stub';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStubEndPoints()
    {
      return __DIR__.'/Stubs/model-end-points-test.stub';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStubRolesPermisos()
    {
      return __DIR__.'/Stubs/model-roles-permisos-test.stub';
    }

    protected function replaceModelNameSingularVariable($file,$name){
      return str_replace("[% model_name_singular_variable %]",strtolower($name),$file);
    }

    protected function replaceModelName($file,$name){
      return str_replace("[% model_name %]",$name,$file);
    }


    protected function buildClass($name,$template)
    {
        $stub = File::get($this->$template());
        $stub = $this->replaceModelName($stub,$name);
        $stub = $this->replaceModelNameSingularVariable($stub,$name);
        return $stub;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->option('model');

        try {
          if(!$this->option('model')){
            throw new Exception("ERROR : Parámetro model no esta definido --model=name", 1);
          }

          if(!File::exists('tests/Feature/'.$model)) {
            File::makeDirectory('tests/Feature/'.$model);
          }

          $templates = [
            [
              "path" => "tests/Feature/{$model}/{$model}CrudTest.php",
              "function" => "getStubCrud",
            ],
            [
              "path" => "tests/Feature/{$model}/{$model}RelacionesTest.php",
              "function" => "getStubRelaciones",
            ],
            [
              "path" => "tests/Feature/{$model}/{$model}RolesEndPointsTest.php",
              "function" => "getStubEndPoints",
            ],
            [
              "path" => "tests/Feature/{$model}/{$model}RolesPermisosTest.php",
              "function" => "getStubRolesPermisos",
            ],
          ];

          foreach ($templates as $documentation => $value) {
            if(!File::exists($value["path"])) {
              File::put($value["path"],$this->buildClass($model,$value["function"]));
            }else{
              $this->warn("Ya existe este fichero ". $value["path"] );
            }
          }

        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return 0;
        }

        $this->info("Console command created successfully.");

        return 0;
    }
}
