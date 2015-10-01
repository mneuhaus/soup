<?php
namespace Famelo\Soup\Core;

use Famelo\Soup\Core\RecipeHandler;
use TYPO3Fluid\Fluid\View\TemplateView;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class WebController {

	public function index($arguments) {
		$recipeHandler = new RecipeHandler();
		$relevantRecipes = $recipeHandler->getRelevantRecipies(WORKING_DIRECTORY);
		$this->render('index', array(
			'relevantRecipes' => $relevantRecipes
		));
	}

	public function recipe($arguments) {
		$recipeHandler = new RecipeHandler();
		$recipeInstance = $recipeHandler->getRecipe($arguments['recipe']);

		$this->render('recipe/' . $arguments['recipe'], array(
			'recipe' => $recipeInstance
		));
	}

	public function saveRecipe($arguments) {
		$recipeHandler = new RecipeHandler();
		$recipeInstance = $recipeHandler->getRecipe($arguments['recipe']);
		$recipeInstance->saveFields($_POST);

		$this->redirect('recipe/' . $arguments['recipe']);
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

		// $variableProvider = new ExposeVariableProvider();
		// $this->view->getRenderingContext()->setVariableProvider($variableProvider);

		// $viewHelperResolver = new \Famelo\Cider\Fluid\ViewHelperResolver();
		// $view->setViewHelperResolver($viewHelperResolver);

		// $view->assign('foobar', 'MVC template');
		// echo $view->render('Default');
	}
}