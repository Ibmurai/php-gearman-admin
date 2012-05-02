<?php
/**
 * This file is part of the php-gearman-admin framework.
 * @link https://github.com/Ibmurai/php-gearman-admin
 *
 * @copyright Copyright 2012 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
/**
 * A class to access the status information returned by gearman.
 *
 * @author Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class GearmanAdminStatus {
	/** @var integer The index of the status array containing the number of available workers for a given function. */
	const TOTAL = 0;

	/** @var integer The index of the status array containing the number of available workers for a given function. */
	const RUNNING = 1;

	/** @var integer The index of the status array containing the number of available workers for a given function. */
	const AVAILABLE = 2;

	/** @var integer[] An array of [function name => [total, running, available workers]] */
	private $_functions = array();

	/**
	 * Construct a new GearmanAdminStatus instance, parsing the given status strings.
	 *
	 * @param string[] $status The status lines, as returned by the gearman server.
	 *
	 * @return null
	 */
	public function __construct(array $status) {
		$this->_parseStatus($status);
	}

	/**
	 * Get the status as a pretty string.
	 *
	 * @see http://www.php.net/manual/en/language.oop5.magic.php#object.tostring
	 *
	 * @return string
	 */
	public function __toString() {
		$res  = "Function:    | Total: | Running: | Available:\n";
		$res .= "----------------------------------------------\n";

		foreach ($this->getFunctions() as $function) {
			$res .= sprintf("%-12s | %6d | %8d | %10d\n", $function, $this->getTotal($function), $this->getRunning($function), $this->getAvailable($function));
		}

		return $res;
	}

	/**
	 * Get an array of all registered function names.
	 *
	 * @return string[]
	 */
	public function getFunctions() {
		return array_keys($this->_functions);
	}

	/**
	 * Get the number of available workers, by function name.
	 *
	 * @param string $function
	 *
	 * @return integer
	 */
	public function getAvailable($function) {
		return array_key_exists($function, $this->_functions) ? (integer) $this->_functions[$function][self::AVAILABLE] : 0;
	}

	/**
	 * Get the total number incomplete jobs by function name.
	 *
	 * @param string $function
	 *
	 * @return integer
	 */
	public function getTotal($function) {
		return array_key_exists($function, $this->_functions) ? (integer) $this->_functions[$function][self::TOTAL] : 0;
	}

	/**
	 * Get the number of running workers, by function name.
	 *
	 * @param string $function
	 *
	 * @return integer
	 */
	public function getRunning($function) {
		return array_key_exists($function, $this->_functions) ? (integer) $this->_functions[$function][self::RUNNING] : 0;
	}

	/**
	 * Get the full status array of a given function.
	 *
	 * @param string $function
	 *
	 * @return integer[] An array of [total, running, available]. Use the class constants to index into the info array.
	 */
	public function getInfo($function) {
		return array(
			self::TOTAL     => $this->getTotal($function),
			self::RUNNING   => $this->getRunning($function),
			self::AVAILABLE => $this->getAvailable($function),
		);
	}

	/**
	 * Parse a status string array, as returned from the gearman server.
	 *
	 * @param string[] $status
	 *
	 * @return null
	 */
	private function _parseStatus(array $status) {
		foreach ($status as $line) {
			if (($explosion = explode("\t", $line)) && count($explosion) == 4) {
				$this->_functions[$explosion[0]] = array_slice($explosion, 1, 3);
			}
		}
	}
}
