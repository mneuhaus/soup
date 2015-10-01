<?php
namespace Famelo\Soup\Recipes;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class TYPO3 {
	public function relevantToDirectory($directory) {
		return file_exists($directory . '/typo3conf/');
	}
}