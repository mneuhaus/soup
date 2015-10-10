<?php
namespace Famelo\Soup\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class IngredientsViewHelper extends AbstractViewHelper {

	/**
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('className', 'string', 'Ingredient Classname');
	}

	/**
	 *
	 * @return string Rendered string
	 * @api
	 */
	public function render() {
		$className = $this->arguments['className'];
		if ($className === NULL) {
			$className = $this->renderChildren();
		}
		return $className::getExistingInstances();
	}
}

?>