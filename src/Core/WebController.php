<?php
namespace Famelo\Soup\Core;

use Famelo\Soup\Core\BookHandler;
use Famelo\Soup\Command\Edit;
use Famelo\Archi\Utility\String;
use TYPO3Fluid\Fluid\View\TemplateView;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class WebController {

	public function index($arguments) {
		$bookHandler = new BookHandler();
		$books = $bookHandler->findRelevantBooks(WORKING_DIRECTORY);
		$this->render('index', array(
			'books' => $books
		));
	}

	public function newRecipe($arguments) {
		$recipeClassName = String::classNameFromPath($arguments['recipe']);
		$recipe = new $recipeClassName();

		$this->render(str_replace('.', '/', String::cutSuffix($arguments['recipe'], 'Recipe')) . '/New', array(
			'recipe' => $recipe
		));
	}

	public function createRecipe($arguments) {
		$recipeClassName = String::classNameFromPath($arguments['recipe']);
		$recipe = new $recipeClassName();
		$recipe->create($_POST);

		$this->redirect('');
	}

	public function editRecipe($arguments) {
		$recipeClassName = String::classNameFromPath($arguments['recipe']);
		$recipe = new $recipeClassName();
		chdir($arguments['path']);

		$this->render(str_replace('.', '/', String::cutSuffix($arguments['recipe'], 'Recipe')) . '/Edit', array(
			'recipe' => $recipe
		));
	}

	public function saveRecipe($arguments) {
		$recipeClassName = String::classNameFromPath($arguments['recipe']);
		$recipe = new $recipeClassName();
		chdir($arguments['path']);
		$recipe->saveFields($_POST);

		$this->redirect('recipe/' . $arguments['recipe'] . '/' . $arguments['path']);
	}

	public function redirect($path) {
		header('Location: /' . $path);
		exit;
	}

	public function render($template, $variables = array()) {
		$paths = new \TYPO3Fluid\Fluid\View\TemplatePaths();
		// $paths->setTemplateRootPaths(array(__DIR__ . '/../Templates/'));
		$paths->setLayoutRootPaths(array(BASE_DIRECTORY . '/../Resources/Layouts/'));
		$paths->setPartialRootPaths(array(BASE_DIRECTORY . '/../Resources/Partials/'));

		$parts = explode('/', $template);
		array_walk($parts, function(&$value, $key){
			$value = ucfirst($value);
		});
		$path = implode('/', $parts);
		$templateFile = BASE_DIRECTORY . '/../Resources/Templates/' . $path . '.html';
		$paths->setTemplatePathAndFilename($templateFile);

		$view = new TemplateView($paths);
		$view->assignMultiple($variables);

		$view->getViewHelperResolver()->registerNamespace('s', 'Famelo\\Soup\\ViewHelpers');

		echo $view->render();
	}
}
