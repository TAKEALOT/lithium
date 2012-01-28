<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace lithium\g11n;
use lithium\core\Libraries;

/**
 * The `Multibyte` class helps operating with UTF-8 encoded strings. Here
 * multibyte is synonymous with UTF-8 the most widespread multibyte encoding
 * in web application development.
 *
 * Over time - as the understanding of the importance of the problem evolved -
 * a variety of extensions has been created. While each achieves its goal
 * through a different implementation they still all try to solve that one
 * problem that comes with multibyte encoded strings.

 * So - that problem is targeted by extensions out there. Something left to a
 * framework is to provide means allowing code to stay portable. As already
 * mentioned this is an evolution and thus certain extensions are more
 * wide-spread and enabled in some environments while others are not. This
 * class - as with any adapter based class - allows to adapt to those different
 * environments by just changing the configuration and not the code.
 *
 * Technically this class is not so much an abstraction as it very it does very
 * little to abstract away the specifics of the adapters.
 */
class Multibyte extends \lithium\core\Adaptable {

	/**
	 * `Libraries::locate()`-compatible path to adapters for this class.
	 *
	 * @see lithium\core\Libraries::locate()
	 * @var string Dot-delimited path.
	 */
	protected static $_adapters = 'adapter.g11n.multibyte';

	/**
	 * Checks if a given string is UTF-8 encoded and is valid UTF-8.
	 *
	 * In _quick_ mode it will check only for non ASCII characters being used
	 * indicating any multibyte encoding. Don't use quick mode for integrity
	 * validation of UTF-8 encoded strings.
	 *
	 * @link http://www.w3.org/International/questions/qa-forms-utf-8.en
	 * @param string $string The string to analyze.
	 * @param array $options Allows to toggle mode via the `'quick'` option, defaults to `false`.
	 * @return boolean Returns `true` if the string is UTF-8.
	 */
	public static function is($string, array $options = array()) {
		$defaults = array('quick' => false);
		$options += $defaults;

		if ($options['quick']) {
			$regex = '/[^\x09\x0A\x0D\x20-\x7E]/m';
		} else {
			$regex  = '/\A(';
			$regex .= '[\x09\x0A\x0D\x20-\x7E]';            // ASCII
			$regex .= '|[\xC2-\xDF][\x80-\xBF]';            // non-overlong 2-byte
			$regex .= '|\xE0[\xA0-\xBF][\x80-\xBF]';        // excluding overlongs
			$regex .= '|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'; // straight 3-byte
			$regex .= '|\xED[\x80-\x9F][\x80-\xBF]';        // excluding surrogates
			$regex .= '|\xF0[\x90-\xBF][\x80-\xBF]{2}';     // planes 1-3
			$regex .= '|[\xF1-\xF3][\x80-\xBF]{3}';         // planes 4-15
			$regex .= '|\xF4[\x80-\x8F][\x80-\xBF]{2}';     // plane 16
			$regex .= ')*\z/m';
		}
		return (boolean) preg_match($regex, $string);
	}

	/**
	 * Gets the string length. Multibyte enabled version of `strlen()`.
	 *
	 * @link http://php.net/manual/en/function.strlen.php
	 * @param string $string The string being measured for length.
	 * @param array $options Allows for selecting the adapter to use via the
	 *               `name` options. Will use the `'default'` adapter by default.
	 * @return integer The length of the string on success.
	 */
	public static function strlen($string, array $options = array()) {
		$defaults = array('name' => 'default');
		$options += $defaults;
		return static::adapter($options['name'])->strlen($string);
	}
}

?>