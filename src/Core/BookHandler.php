<?php
namespace Famelo\Soup\Core;

use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class BookHandler {
	public function findRelevantBooks($path) {
		$finder = new Finder();
		$classFiles = $finder->files()->in(BASE_DIRECTORY . '/')->name('*Book.php');
		$books = array();
		foreach ($classFiles as $classFile) {
			$className = '\Famelo\Soup' . str_replace(
				array(BASE_DIRECTORY, '.php', '/'),
				array('', '', '\\'),
				$classFile->getRealPath()
			);
			$book = new $className(WORKING_DIRECTORY);
			if ($book->isRelevantToDirectory()) {
				$books[] = $book;
			}
		}
		return $books;
	}

	public function getRecipe($name) {
		$recpieClassName = '\Famelo\Soup\Recipes\\' . $name;
		return new $recpieClassName;
	}
}