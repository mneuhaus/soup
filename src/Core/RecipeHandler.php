<?php
namespace Famelo\Soup\Core;

use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class RecipeHandler {
	public function getRelevantRecipies($path) {
		$finder = new Finder();
		$recipes = $finder->files()->in(BASE_DIRECTORY . '/Recipes/');
		$relevantRecipes = array();
		foreach ($recipes as $recipe) {
			$recipe = str_replace('.php', '', $recipe->getBasename());
			$recpieClassName = '\Famelo\Soup\Recipes\\' . $recipe;
			$recipeInstance = new $recpieClassName();
			$relevant = $recipeInstance->relevantToDirectory(WORKING_DIRECTORY);
			$relevantRecipes[] = $recipe;
		}
		return $relevantRecipes;
	}

	public function getRecipe($name) {
		$recpieClassName = '\Famelo\Soup\Recipes\\' . $name;
		return new $recpieClassName;
	}
}