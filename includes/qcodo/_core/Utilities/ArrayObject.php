<?php
namespace Qcodo\Utilities;

class ArrayObject extends \ArrayObject {
	/**
	 * Returns whether or not a given item is an array or an ArrayObject
	 * @param mixed $object
	 * @return boolean
	 */
	public static function IsArray($object) {
		if (is_array($object)) return true;
		if ($object instanceof \ArrayObject) return true;
		return false;
	}
}