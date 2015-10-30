<?php
namespace Famelo\Soup\Typo3\Ingredients;

use Famelo\Archi\ComposerFacade;
use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Core\Ingredients\AbstractIngredient;
use Famelo\Archi\Utility\Path;
use Famelo\Archi\Utility\String;
use Famelo\Archi\Typo3\ExtTablesFacade;
use Symfony\Component\Finder\Finder;
use Famelo\Archi\Typo3\ExtEmconfFacade;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class FluidTypo3 extends AbstractIngredient {

	const PATTERN_PROVIDER_REGISTER = '/.*Core::registerProviderExtensionKey\(([^;]*);/';

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var array
	 */
	public $fluidpages = array();

	/**
	 * @var array
	 */
	public $fluidcontent = array();

	/**
	 * @var array
	 */
	public $fluidbackend = array();

	/**
	 * @var array
	 */
	public $vhsActive = FALSE;

	/**
	 * @var array
	 */
	public $providers = FALSE;

	/**
	 * @var string
	 */
	protected $composer = array(
		"autoload" => array(
			"psr-4" => array(
			)
		)
	);

	/**
	 * @var string
	 */
	protected $filepath;

	/**
	 * @var string
	 */
	protected $extensionKey;

	/**
	 * @var array
	 */
	static protected $paths = array(
		'Classes/Controller/'
	);

	public function __construct($filepath = NULL) {
		$this->extTablesFacade = new ExtTablesFacade();
		$this->providers = $this->extTablesFacade->getFunctions(self::PATTERN_PROVIDER_REGISTER, 1);

		$providers = array(
			'Page' => 'fluidpages',
			'Content' => 'fluidcontent',
			'Backend' => 'fluidbackend'
		);
		foreach ($providers as $providerName => $providerExtension) {
			$templatePath = Path::joinPaths( 'Resources/Private/Templates', $providerName);
			if (!file_exists($templatePath)) {
				continue;
			}
			$files = scandir($templatePath);
			foreach ($files as $file) {
				if (substr($file, 0, 1) == '.') {
					continue;
				}
				array_push($this->$providerExtension, basename($file, '.html'));
			}
		}
	}

	public static function getInstances() {
		$fluidTypo3 = new FluidTypo3();
		if (count($fluidTypo3->fluidpages) == 0 && count($fluidTypo3->fluidcontent) == 0 && count($fluidTypo3->fluidbackend) ==  0) {
			return array();
		}
		return array($fluidTypo3);
	}

	public function getArguments() {
		return array($this->filepath);
	}

	public function getTitle() {
		return $this->data['title'];
	}

	public function getFilepath() {
		return $this->filepath;
	}

	public function save($fieldValues) {
		$extEmconfFacade = new ExtEmconfFacade();

		$providers = array(
			'Page' => 'fluidpages',
			'Content' => 'fluidcontent',
			'Backend' => 'fluidbackend'
		);

		foreach ($providers as $providerName => $providerExtension) {
			if (isset($fieldValues[$providerExtension])) {
				if (!isset($this->providers[$providerName])) {
					$this->extTablesFacade->addCode("\FluidTYPO3\Flux\Core::registerProviderExtensionKey('Foo.FooBuilder', '" . $providerName . "');");
				}
				$extEmconfFacade->addDependency($providerExtension);
				$this->addTemplates($fieldValues[$providerExtension], $providerName);
			} else {
				if (isset($this->providers[$providerName])) {
					$this->extTablesFacade->removeCode($this->providers[$providerName]['code']);
				}
				$extEmconfFacade->removeDependency($providerExtension);
			}
		}

		$this->extTablesFacade->save();
		$extEmconfFacade->save();
	}

	public function addTemplates($templates, $providerName) {
		foreach ($templates as $templateName => $template) {
			$oldTemplatePath = Path::joinPaths( 'Resources/Private/Templates', $providerName, $templateName . '.html');
			if (isset($template['_remove'])) {
				unlink($oldTemplatePath);
				continue;
			}

			$templatePath = Path::joinPaths( 'Resources/Private/Templates', $providerName, ucfirst($template['name']) . '.html');
			if (file_exists($templatePath)) {
				continue;
			}
			if (!file_exists(dirname($templatePath))) {
				mkdir(dirname($templatePath), 0775, TRUE);
			}

			if (file_exists($oldTemplatePath)) {
				rename($oldTemplatePath, $templatePath);
			} else {
				file_put_contents($templatePath, 'foo');
			}
		}
	}

	public function remove() {
		$extEmconfFacade = new ExtEmconfFacade();

		$providers = array(
			'Page' => 'fluidpages',
			'Content' => 'fluidcontent',
			'Backend' => 'fluidbackend'
		);

		foreach ($providers as $providerName => $providerExtension) {
			if (isset($this->providers[$providerName])) {
				$this->extTablesFacade->removeCode($this->providers[$providerName]['code']);
			}
			$extEmconfFacade->removeDependency($providerExtension);

			$templatePath = Path::joinPaths( 'Resources/Private/Templates', $providerName);
			if (!file_exists($templatePath)) {
				continue;
			}
			$files = scandir($templatePath);
			foreach ($files as $file) {
				if (substr($file, 0, 1) == '.') {
					continue;
				}
				unlink(Path::joinPaths($templatePath, $file));
			}
			rmdir($templatePath);
		}

		$this->extTablesFacade->save();
		$extEmconfFacade->save();
	}

}
