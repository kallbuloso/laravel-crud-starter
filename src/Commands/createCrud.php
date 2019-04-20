<?php

namespace Mvd81\LaravelCrudStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Createcrud extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-crud {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a crud starter/boiler template';

    /**
     * Source crud files.
     *
     * @var string
     */
    protected $sourceDirectory;

    /**
     * The crud name.
     *
     * @var
     */
    protected $name;

    /**
     * Check if we override a current crud (if so don't create another route resource line).
     *
     * @var bool
     */
    protected $override = false;

    /**
     * Create a new command instance.
     *
     */
    public function __construct() {

        $this->signature .= ' {name : crud name}';
        parent::__construct();
        $this->sourceDirectory = resource_path('crud-starter-templates');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        // Crud name.
        $this->name = $this->argument('name');

        // Check if crud source templates directories exists.
        if (!File::isDirectory($this->sourceDirectory)) {
            $this->error('Source directory for the crud templates does not exist (resources/crud-starter).');
            exit();
        }

        // Create the controller.
        $this->createController($this->name);

        // Create the model file.
        $this->createModel($this->name);

        // Create the migration file.
        $this->createMigration($this->name);

        // Create the view templates.
        $this->createBladeTemplates($this->name);

        // Add resource to the route file, ony if not is created.
        if (!$this->override) {
            $this->createRoute($this->name);
        }

        // Crud create, show a notification in th cli.
        $this->line('Starters crud <fg=blue>' . $this->name . '</> created! (don\'t forget to run the migration if you finished this crud).');
    }

    /**
     * Create the model.
     *
     * @param $name
     */
    protected function createModel($name) {
        $sourceTemplate = $this->getCrudTemplate('crudModel.stub');

        // Replace the variables.
        $controllerContent = $this->replaceCrudVariables($sourceTemplate, $name);

        // Copy the file to the models directory.
        $this->createFile(config('crud_starter.models_path') . $name . '.php', $controllerContent);
    }

    /**
     * Create the controller.
     *
     * @param $name
     */
    protected function createController($name) {

        // Get the controller source template.
        $sourceTemplate = $this->getCrudTemplate('crudController.stub');

        // Replace the variables.
        $controllerContent = $this->replaceCrudVariables($sourceTemplate, $name);

        // Copy the file to the controllers directory.
        $this->createFile('app/Http/Controllers/' . $name . 'Controller.php', $controllerContent);
    }

    /**
     * Create the crud view template files.
     *
     * @param $name
     */
    protected function createBladeTemplates($name) {

        // Create view directory.
        $viewPath = resource_path('views\\' . $this->camelCaseToUnderscore($name));
        if (!File::isDirectory($viewPath)) {
            File::makeDirectory($viewPath);
        }

        // Collect all the view files to parse.
        $allViewTemplates = [
            'index',
            'show',
            'create',
            'edit',
            'create_edit_form'
        ];
        foreach ($allViewTemplates as $templateFile) {

            // Get the controller source template.
            $template = $this->getCrudTemplate('/views/' . $templateFile . '.blade.php');

            // Replace the variables.
            $bladeTemplate = $this->replaceCrudVariables($template, $name);

            // Copy the file to the view directory.
            $this->createFile($viewPath . '\\' . $templateFile . '.blade.php', $bladeTemplate);
        }
    }

    /**
     * Create the routes for this crud.
     *
     * @param $name
     */
    protected function createRoute($name) {

        // Check if the route need a prefix.
        $routePrefix = '';
        if ($routePrefix = config('crud_starter.route_prefix', false)) {
            $routePrefix = strtolower($routePrefix) . '/';
        }

        // Create the resource route.
        $addRoute = "\rRoute::resource('" . $routePrefix . strtolower($this->camelCaseToDash($name)) . "', '" . $name . "Controller')";

        // Check if we need to add middleware to this route.
        if ($middleware = config('crud_starter.crud_middleware', false)) {
            if (is_array($middleware)) {
                $middleware = implode(', ', $middleware);
            }
            $addRoute .= "->middleware(" . "'" . str_replace(",", "','", $middleware) . "'" . ")";
        }


        // Close route function.
        $addRoute .=";";

        // Add the routes to the file.
        File::append($_SERVER['DOCUMENT_ROOT'].'routes/web.php', $addRoute);
    }

    /**
     * Create the database migration file.
     *
     * @param $name
     */
    protected function createMigration($name) {
        // Get the crud migration template.
        $sourceTemplate = $this->getCrudTemplate('crudMigration.stub');

        // Replace the variables.
        $migrationContent = $this->replaceCrudVariables($sourceTemplate, $name);

        // Check if we need to override (remove) a migration
        if ($this->option('force')) {
            foreach ($migrations = File::glob('database/migrations/*_create_' . $this->camelCaseToUnderscore($name) . 's_table.php') as $migration) {
                File::delete($migration);
            }
        }

        // Copy the file to the migrations directory.
        $this->createFile('database/migrations/' . date('Y_m_d_His') . '_create_' . $this->camelCaseToUnderscore($name) . 's_table.php', $migrationContent);
    }

    /**
     * Replace the variables for the new crud type.
     *
     * @param $source
     * @param $name
     * @return mixed
     */
    protected function replaceCrudVariables($source, $name) {

        $crudVar = lcfirst($name);                                              // CrudName -> crudName
        $crudUnderscore = strtolower($this->camelCaseToUnderscore($name));      // CrudName -> crud_name
        $crudDash = strtolower($this->camelCaseToDash($name));                  // CrudName -> crud-name
        $crudSpace = strtolower($this->camelCaseToSpace($name));                // CrudName -> crud name
        $crudTitle = ucfirst($crudSpace);                                       // CrudName -> Crud name
        $modelsNamespace = config('crud_starter.models_namespace', 'App\\');    // Models namespace
        $modelsPath = config('crud_starter.models_path', 'app/');               // Models path
        $btnEdit = 'Edit';
        if (config('crud_starter.use_fontawesome', false)) {
            $btnEdit = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
        }
        $btnShow = 'Show';
        if (config('crud_starter.use_fontawesome', false)) {
            $btnShow = '<i class="fa fa-eye" aria-hidden="true"></i>';
        }
        $btnDelete = 'Delete';
        if (config('crud_starter.use_fontawesome', false)) {
            $btnDelete = '<i class="fa fa-times" aria-hidden="true"></i>';
        }
        $btnAdd = '';
        if (config('crud_starter.use_fontawesome', false)) {
            $btnAdd = '<i class="fa fa-plus" aria-hidden="true"></i> ';
        }

        $appTemplate = config('crud_starter.view_app_template', 'layouts.app');
        $viewContentName = config('crud_starter.view_content_section_name', 'content');

        // Replace the variables.
        return str_replace(
            array('[%Crud%]', '[%crud_var%]', '[%crud_underscore%]', '[%crud_dash%]', '[%crud_space%]', '[%crud_title%]', '[%models_namespace%]', '[%models_path%]', '[%btn_edit%]', '[%btn_show%]', '[%btn_delete%]', '[%btn_add%]', '[%app_template%]', '[%view_section_name%]'),
            array($name, $crudVar, $crudUnderscore, $crudDash, $crudSpace, $crudTitle, $modelsNamespace, $modelsPath, $btnEdit, $btnShow, $btnDelete, $btnAdd, $appTemplate, $viewContentName),
            $source
        );
    }

    /**
     * Replace camelCase to underscores.
     *
     * @param $input
     * @return string
     */
    protected function camelCaseToUnderscore($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    /**
     * Replace camelCase to dashes.
     *
     * @param $input
     * @return string
     */
    protected function camelCaseToDash($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }

    /**
     * Replace camelCase to spaces.
     *
     * @param $input
     * @return string
     */
    protected function camelCaseToSpace($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $input));
    }

    /**
     * Create a new template.
     *
     * @param $file
     * @param $content
     */
    protected function createFile($file, $content) {

        // First check if this file not exists.
        if (!$this->option('force') && File::exists($file)) {
            $this->error($file . ' already exists (use --force to override).');
            exit();
        }
        // Check if we override a current crud.
        elseif ($this->option('force') && File::exists($file)) {
            $this->override = true;
        }
        File::put($file, $content);
    }

    /**
     * Get a crud source template.
     *
     * @param $fileName
     * @return string
     */
    protected function getCrudTemplate($fileName) {
        return File::get($this->sourceDirectory . '/' . $fileName);
    }
}
