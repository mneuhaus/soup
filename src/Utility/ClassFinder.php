<?php
namespace Famelo\Soup\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class ClassFinder {

	public static function findInPath($path) {
		$finder = new Finder();
		$recipes = $finder->files()->in($path);
		$relevantRecipes = array();
		foreach ($recipes as $recipe) {
			$recipe = str_replace('.php', '', $recipe->getBasename());
			$recpieClassName = '\Famelo\Soup\Recipes\\' . $recipe;
		}
	}
}
?>