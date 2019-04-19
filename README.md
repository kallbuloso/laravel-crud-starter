
## Introduction

With just one cli command you can create a crud (with only a title field) controller, model, migration, view templates and add a entry in routes/web.php.
This is just a tool to create some starter templates, **not** a crud generator such as Laravel Voyager or CrudBooster.

### Requirements
Laravel 5.5 or higher

## Installation

1. To download this package, use the cli to execute the following command:

	```
	composer require mvd81/laravel-crud-starter --dev
	````

2. **Configuration, open the config file: */config/crud_starter.php***

	Name space for your models.
	````
	'models_namespace' => 'App',
	````

	Path where your models are stored.
	````
	models_path' => 'app/',
	````
	
	Middleware for the crud routes. Leave empty for no middleware.
    ````
    'crud_middleware' => 'auth',
    ````
    	
	Location to extend the view app.
	````
	'view_app_template' => 'layouts.app',
	````
	
	Name of the view section to show the crud.
	````
	'view_content_section_name' => 'content',
	````
	
	Does this project use Font Awesome.
	````
	'use_fontawesome' => false,
	````
	
    	
	
## Create a crud starter template

````
php artisan command:create-crud CrudName
````

### This command creates
<ul>
	<li>A controller for this crud</li>
	<li>A model for this crud</li>
	<li>A migration for this crud</li>
	<li>The view templates for this crud</li>
	<li>The routes in routes/web.php for this crud</li>
</ul>

### Uninstall / remove this package
```
composer remove mvd81/laravel-crud-starter --dev
```
##### Manually remove 
<ul>
<li>The folder '/resources/crud-starter-templates'</li>
<li>The config file, /config/crud_starter.php</li>
</ul>

## Todo
<ul>
	<li>Complete this manual</li>
	<li>Create a tutorial how to use this package</li>
</ul>

## License

"Laravel crud starter" is an open-sourced software licensed under the <a href="https://opensource.org/licenses/MIT" target="_blank" title="MIT license">MIT license</a>

