<?php
namespace Famelo\Soup\Core\Ingredients;

use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

abstract class AbstractIngredient implements IngredientInterface {
	/**
	 * @var string
	 */
	public $name;

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