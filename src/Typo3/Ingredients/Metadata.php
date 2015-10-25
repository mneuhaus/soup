<?php
namespace Famelo\Soup\Typo3\Ingredients;

use Famelo\Archi\ComposerFacade;
use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Core\Ingredients\AbstractIngredient;
use Famelo\Archi\Utility\Path;
use Famelo\Archi\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class Metadata extends AbstractIngredient {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var array
	 */
	public $data;

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
		if ($filepath === NULL || !file_exists($filepath)) {
			$filepath = Path::joinPaths(BASE_DIRECTORY, '../Resources/CodeTemplates/Typo3/ext_emconf.php');
		} else {
			$this->extensionKey = basename(getcwd());
			$this->filepath = $filepath;
		}
		$_EXTKEY = 'foo';
		$EM_CONF = array();
		require($filepath);
		$this->data = $EM_CONF[$_EXTKEY];

		$this->composer = new ComposerFacade('composer.json');
	}

	public static function getInstances() {
		if (file_exists('ext_emconf.php')) {
			return array(new Metadata('ext_emconf.php'));
		}
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
		foreach ($fieldValues as $key => $value) {
			if (isset($this->data[$key])) {
				$this->data[$key] = $value;
			}
		}
		$output = sprintf('<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "%s"
 *
 * Auto generated by famelo/soup %s
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = %s;',
			basename(WORKING_DIRECTORY),
			date('Y-m-d'),
			var_export($this->data, TRUE)
		);
		file_put_contents('ext_emconf.php', $output);

		$namespace = '';
		if (!empty($fieldValues['company'])) {
			$namespace = $fieldValues['company'] . '\\';
		}
		$namespace.= String::underscoreToCamelcase($fieldValues['extension_key']);
		$this->composer->setNamespace($namespace, 'Classes/');
		$this->composer->save();
	}

	public function getDescription() {
		return $this->data['description'];
	}

	public function getAuthor() {
		return $this->data['author'];
	}

	public function getAuthorEmail() {
		return $this->data['author_email'];
	}

	public function getExtensionKey() {
		return $this->extensionKey;
	}

	public function getCompany() {
		$namespace = $this->composer->getNamespace();
		if (stristr($namespace, '\\')) {
			$parts = explode('\\', $namespace);
			return array_shift($parts);
		}
	}

	public function getExtensionTypes() {
		return array(
			'fe' => 'Frontend',
			'plugin' => 'Frontend Plugins',
			'be' => 'Backend',
			'module' => 'Backend Modules',
			'services' => 'Services',
			'example' => 'Examples',
			'misc' => 'Miscellaneous',
			'templates' => 'Templates',
			'doc' => 'Documentation'
		);
	}

	public function getExtensionType() {
		return $this->data['category'];
	}

	public function getExtensionStates() {
		return array(
			'alpha' => 'Alpha (Very initial development)',
			'beta' => 'Beta (Under current development, should work partly)',
			'stable' => 'Stable (Stable and used in production)',
			'experimental' => 'Experimental (Nobody knows if this is going anywhere yet...)',
			'test' => 'Test (Test extension, demonstrates concepts etc.)'
		);
	}

	public function getExtensionState() {
		return $this->data['state'];
	}

	public function getVersion() {
		return $this->data['version'];
	}
}