<?php

class FluidCache_partial_Table_Header_1394dfad665f10059bcae357b5ce539c4b0a4e5c extends \TYPO3\Fluid\Core\Compiler\AbstractCompiledTemplate {

public function getVariableContainer() {
	// TODO
	return new \TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer();
}
public function getLayoutName(\TYPO3\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
$self = $this;

return NULL;
}
public function hasLayout() {
return FALSE;
}

/**
 * Main Render function
 */
public function render(\TYPO3\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
$self = $this;
$output0 = '';

$output0 .= '
';
// Rendering ViewHelper TYPO3\Fluid\ViewHelpers\IfViewHelper
$arguments1 = array();
// Rendering Boolean node
$stack2 = unserialize('a:1:{i:0;O:53:"TYPO3\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode":2:{s:13:" * objectPath";s:21:"schema.listProperties";s:13:" * childNodes";a:0:{}}}');
$arguments1['condition'] = \TYPO3\Fluid\Core\Parser\SyntaxTree\BooleanNode::evaluateStack($renderingContext, $stack2);
$arguments1['then'] = NULL;
$arguments1['else'] = NULL;
$renderChildrenClosure3 = function() use ($renderingContext, $self) {
$output4 = '';

$output4 .= '
	<tr>
		';
// Rendering ViewHelper TYPO3\Fluid\ViewHelpers\ForViewHelper
$arguments5 = array();
$arguments5['each'] = \TYPO3\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode::getPropertyPath($renderingContext->getTemplateVariableContainer(), 'schema.listProperties', $renderingContext);
$arguments5['as'] = 'property';
$arguments5['key'] = NULL;
$arguments5['reverse'] = false;
$arguments5['iteration'] = NULL;
$renderChildrenClosure6 = function() use ($renderingContext, $self) {
$output7 = '';

$output7 .= '
			<th>
				';
// Rendering ViewHelper TYPO3\Fluid\ViewHelpers\Format\HtmlspecialcharsViewHelper
$arguments8 = array();
// Rendering ViewHelper Flowpack\Expose\ViewHelpers\WrapViewHelper
$arguments9 = array();
$arguments9['name'] = 'property';
// Rendering Array
$array10 = array();
$array10['property'] = \TYPO3\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode::getPropertyPath($renderingContext->getTemplateVariableContainer(), 'property.name', $renderingContext);
$arguments9['arguments'] = $array10;
$renderChildrenClosure11 = function() use ($renderingContext, $self) {
$output12 = '';

$output12 .= '<i class="fa"></i> ';
// Rendering ViewHelper TYPO3\Fluid\ViewHelpers\Format\HtmlspecialcharsViewHelper
$arguments13 = array();
$arguments13['value'] = \TYPO3\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode::getPropertyPath($renderingContext->getTemplateVariableContainer(), 'property.label', $renderingContext);
$arguments13['keepQuotes'] = false;
$arguments13['encoding'] = 'UTF-8';
$arguments13['doubleEncode'] = true;
$renderChildrenClosure14 = function() use ($renderingContext, $self) {
return NULL;
};
$value15 = ($arguments13['value'] !== NULL ? $arguments13['value'] : $renderChildrenClosure14());

$output12 .= !is_string($value15) && !(is_object($value15) && method_exists($value15, '__toString')) ? $value15 : htmlspecialchars($value15, ($arguments13['keepQuotes'] ? ENT_NOQUOTES : ENT_COMPAT), $arguments13['encoding'], $arguments13['doubleEncode']);
return $output12;
};
$viewHelper16 = $self->getViewHelper('$viewHelper16', $renderingContext, 'Flowpack\Expose\ViewHelpers\WrapViewHelper');
$viewHelper16->setArguments($arguments9);
$viewHelper16->setRenderingContext($renderingContext);
$viewHelper16->setRenderChildrenClosure($renderChildrenClosure11);
// End of ViewHelper Flowpack\Expose\ViewHelpers\WrapViewHelper
$arguments8['value'] = $viewHelper16->initializeArgumentsAndRender();
$arguments8['keepQuotes'] = false;
$arguments8['encoding'] = 'UTF-8';
$arguments8['doubleEncode'] = true;
$renderChildrenClosure17 = function() use ($renderingContext, $self) {
return NULL;
};
$value18 = ($arguments8['value'] !== NULL ? $arguments8['value'] : $renderChildrenClosure17());

$output7 .= !is_string($value18) && !(is_object($value18) && method_exists($value18, '__toString')) ? $value18 : htmlspecialchars($value18, ($arguments8['keepQuotes'] ? ENT_NOQUOTES : ENT_COMPAT), $arguments8['encoding'], $arguments8['doubleEncode']);

$output7 .= '
			</th>
		';
return $output7;
};

$output4 .= TYPO3\Fluid\ViewHelpers\ForViewHelper::renderStatic($arguments5, $renderChildrenClosure6, $renderingContext);

$output4 .= '
	</tr>
';
return $output4;
};
$viewHelper19 = $self->getViewHelper('$viewHelper19', $renderingContext, 'TYPO3\Fluid\ViewHelpers\IfViewHelper');
$viewHelper19->setArguments($arguments1);
$viewHelper19->setRenderingContext($renderingContext);
$viewHelper19->setRenderChildrenClosure($renderChildrenClosure3);
// End of ViewHelper TYPO3\Fluid\ViewHelpers\IfViewHelper

$output0 .= $viewHelper19->initializeArgumentsAndRender();

return $output0;
}


}