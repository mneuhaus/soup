<?php
namespace Famelo\Soup\Core\Ingredients;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

interface IngredientInterface {

	/**
	 * returns arguments that will be used to initialize the seperate instances
	 * of ingredients. Most of the time this will primarily be the filepath
	 * of related to that ingredient.
	 *
	 * @return array
	 */
	public function getArguments();

	/**
	 * save the data collected in the forms
	 *
	 * @param array $arguments
	 * @return void
	 */
	public function save($arguments);

}