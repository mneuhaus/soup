<?php
namespace Famelo\Soup\Typo3\Ingredients;

use Famelo\Archi\ComposerFacade;
use Famelo\Archi\Php\ClassFacade;
use Famelo\Archi\Typo3\ExtLocalconfFacade;
use Famelo\Archi\Typo3\PluginFacade;
use Famelo\Soup\Core\Ingredients\AbstractIngredient;
use Famelo\Soup\Utility\Path;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class Plugin extends AbstractIngredient {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var PluginFacade
	 */
	public $facade;

	public function __construct($name = NULL) {
		$this->name = $name;
		$this->facade = new PluginFacade($name);
	}

	public static function getInstances() {
		$facade = new ExtLocalconfFacade();
		$instances = array();
		foreach ($facade->getPlugins() as $plugin) {
			$instances[] = new Plugin($plugin['name']);
		}
		return $instances;
	}

	public function getArguments() {
		return array($this->name);
	}

	public function getFilepath() {
		return $this->filepath;
	}

	public function getCachedControllers() {
		return $this->facade->cachedControllers;
	}

	public function getUncachedControllers() {
		return $this->facade->uncachedControllers;
	}

	public function getName() {
		return $this->facade->name;
	}

	public function getTitle() {
		return $this->facade->title;
	}

	public function remove($arguments) {
		$this->facade->remove();
	}

	public function save($arguments) {
		$this->facade->name = $arguments['name'];
		$this->facade->title = $arguments['title'];
		$this->facade->title = $arguments['title'];
		$this->facade->cachedControllers = array();
		foreach ($arguments['cachedControllers'] as $cachedController) {
			$this->facade->cachedControllers[$cachedController['name']] = $cachedController['actions'];
		}
		$this->facade->save();
	}
}