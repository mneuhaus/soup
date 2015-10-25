<?php
namespace Famelo\Soup\ViewHelpers\String;

use Famelo\Archi\Utility\String;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class CutSuffixViewHelper extends AbstractViewHelper {

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('string', 'string', 'String', FALSE, NULL);
		$this->registerArgument('suffix', 'string', 'Suffix');
	}

	/**
	 *
	 * @return string Rendered string
	 * @api
	 */
	public function render() {
		$string = $this->arguments['string'];
		if ($string === NULL) {
			$string = $this->renderChildren();
		}
		return String::cutSuffix($string, $this->arguments['suffix']);
	}
}

?>