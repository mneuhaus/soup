<?php
namespace Famelo\Soup\TYPO3;

use Famelo\Soup\Core\BookInterface;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class Typo3Book implements BookInterface {
	/**
	 * @var string
	 */
	public $name = 'TYPO3 Extensions';

	public function isRelevantToDirectory() {
		$finder = new Finder();
		foreach ($finder->directories()->in(getcwd())->depth('== 0') as $directory) {
			if (file_exists($directory->getRealPath() . '/ext_emconf.php')) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public function getRecipies() {
		$finder = new Finder();
		$recipies = array();
		foreach ($finder->directories()->in(getcwd())->depth('== 0') as $directory) {
			if (file_exists($directory->getRealPath() . '/ext_emconf.php')) {
				$recipies[] = new ExtensionRecipe(String::relativePath($directory->getRealPath()));
			}
		}
		return $recipies;
	}

	public function getType() {
		return String::relativeClass('\Famelo\Soup\TYPO3\ExtensionRecipe');
	}
}