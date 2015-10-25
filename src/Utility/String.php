<?php
namespace Famelo\Soup\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class String {

	public static function formNameToPath($formName) {
		$parts = explode('[', $formName);
		array_walk($parts, function(&$value, $key){
			$value = trim($value, ']');
		});
		return implode('.', $parts);
	}

	public static function pathToformName($path) {
		$parts = explode('.', $path);
		array_walk($parts, function(&$value, $key){
			$value = '[' . $value . ']';
		});
		$parts[0] = trim($parts[0], '[]');
		return implode('', $parts);
	}

    public static function pathToFormId($path) {
        return str_replace('.', '-', $path);
    }

    public static function underscoreToCamelcase($underscore) {
        return preg_replace_callback(
            "/(^|_)([a-z])/",
            function($word) {
                return strtoupper("$word[2]");
            },
            $underscore
        );
    }

    public static function camelcaseToUnderscore($underscore) {
        return preg_replace_callback(
           "/(^|[a-z])([A-Z])/",
            function($word) {
                return strtolower(strlen($word[1]) ? "$word[1]_$word[2]" : "$word[2]");
            },
            $underscore
        );
    }

	public static function pathToTranslationId($path) {
		return preg_replace('/\.[0-9]*\./', '.', $path);
	}

    public static function cutSuffix($string, $suffix) {
        if (!static::endsWith($string, $suffix)) {
            return $string;
        }
        return substr($string, 0, strlen($suffix) * -1);
    }

    public static function addSuffix($string, $suffix) {
        if (static::endsWith($string, $suffix)) {
            $string = static::cutSuffix($string, $suffix);
        }
        return $string . $suffix;
    }

	public static function endsWith($string, $suffix) {
		return substr($string, strlen($suffix) * -1) === $suffix;
	}

    public static function relativePath($absolutePath) {
        return trim(str_replace(getcwd(), '', $absolutePath), '/');
    }

    public static function relativeClass($className) {
        return trim(str_replace(array('Famelo\Soup', '\\'), array('', '.'), $className), '.');
    }

    public static function classNameFromPath($path) {
        return 'Famelo\Soup\\' . str_replace('.', '\\', $path);
    }

    public static function prefixLinesWith($string, $prefix) {
        $lines = explode(chr(10), $string);
        foreach ($lines as $key => $line) {
            $lines[$key] = $prefix . $line;
        }
        return implode("\r\n", $lines);
    }
}
?>