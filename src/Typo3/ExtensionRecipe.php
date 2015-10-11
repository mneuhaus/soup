<?php
namespace Famelo\Soup\TYPO3;

use Famelo\Soup\Typo3\Ingredients\Metadata;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class ExtensionRecipe {
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $ingredients = array(
		array(
			'title' => 'Metadata',
			'className' => '\Famelo\Soup\Typo3\Ingredients\Metadata',
			'multiple' => FALSE,
			'paths' => array(
				'' => 'ext_emconf.php'
			)
		),
		array(
			'title' => 'Controller',
			'className' => '\Famelo\Soup\Typo3\Ingredients\Controller',
			'multiple' => TRUE,
			'paths' => array(
				'Classes/Controller/' => '*Controller.php'
			)
		)
	);

	public function __construct($path = NULL) {
		$this->path = $path;
	}

	public function getName() {
		$metadata = new Metadata(String::joinPaths($this->path, 'ext_emconf.php'));
		return $metadata->getTitle() . ' (' . basename($this->path) . ')';
	}

	public function getId() {
		return String::slugify($this->path);
	}

	public function getType() {
		return String::relativeClass(get_class($this));
	}

	public function getPath() {
		return $this->path;
	}

	public function getIngredients() {
		$ingredients = $this->ingredients;
		foreach ($ingredients as $key => $ingredientConfiguration) {
			$ingredients[$key]['instances'] = array();

			foreach ($ingredientConfiguration['paths'] as $path => $filter) {
				if ($path !== '' && !file_exists($path)) {
					continue;
				}
				$finder = new Finder();
				if (!empty($path)) {
					$files = $finder->files()->in('.')->path($path)->name($filter);
				} else {
					$files = $finder->files()->in('.')->name($filter)->depth(0);
				}
				foreach ($files as $file) {
					$ingredients[$key]['instances'][] = new $ingredientConfiguration['className']($file->getRealPath());
				}
			}
		}
		return $ingredients;
	}

	public function create($fieldValues) {
		foreach ($fieldValues['ingredients'] as $ingredientData) {
			if ($ingredientData['_class'] === '\Famelo\Soup\Typo3\Ingredients\Metadata') {
				if (file_exists($ingredientData['extension_key'])) {
					throw new \Exception('Extension directory already exists! (' . $ingredientData['extension_key'] . ')');
				} else {
					mkdir($ingredientData['extension_key']);
				}
				chdir($ingredientData['extension_key']);
				break;
			}
		}

		foreach ($fieldValues['ingredients'] as $ingredientData) {
			if (isset($ingredientData['_arguments'])) {
				$reflection = new \ReflectionClass($ingredientData['_class']);
				$ingredient = $reflection->newInstanceArgs($ingredientData['_arguments']);
			} else {
				$ingredient = new $ingredientData['_class']();
			}
			$ingredient->save($ingredientData);
		}
	}

	public function saveFields($fieldValues) {
		foreach ($fieldValues['ingredients'] as $ingredientData) {
			$reflection = new \ReflectionClass($ingredientData['_class']);
			$ingredient = $reflection->newInstanceArgs($ingredientData['_arguments']);
			$ingredient->save($ingredientData);
		}
	}
}