<?php
namespace Famelo\Soup\Core\Ingredients;

use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class AbstractIngredient {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var array
	 */
	static protected $paths = array(
	);

	public static function getExistingInstances() {
		$finder = new Finder();
		$instances = array();
		foreach (static::$paths as $path) {
			$files = $finder->files()->in(WORKING_DIRECTORY . $path);
			foreach ($files as $file) {
				$ingredientClassName = static::class;
				$instances[] = new $ingredientClassName($file->getRealPath());
			}
		}
		return $instances;
	}

	public function getId() {
		return sha1(spl_object_hash($this));
	}

	public function getPrefix() {
		$path = trim(
			str_replace(
				array(
					'Famelo\Soup\Ingredients',
					'\\'
				),
				array(
					'',
					'-'
				),
				get_class($this)
			),
			'-'
		) . '.' . $this->getId();
		return String::pathToformName($path);
	}
}