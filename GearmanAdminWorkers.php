<?php
/**
 * This file is part of the php-gearman-admin framework.
 * @link https://github.com/Ibmurai/php-gearman-admin
 *
 * @copyright Copyright 2012 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
/**
 * Contains information about the registered workers of a gearman server.
 *
 * @author Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class GearmanAdminWorkers {
	/** @var GearmanAdminWorker[] The contained workers. */
	private $_workers = array();

	/**
	 * Construct a new GearmanAdminWorkers, parsing the given worker strings.
	 *
	 * @param string[] $workers
	 */
	public function __construct(array $workers) {
		$this->_parseWorkers($workers);
	}

	/**
	 * Get the workers as a pretty string.
	 *
	 * @see http://www.php.net/manual/en/language.oop5.magic.php#object.tostring
	 *
	 * @return string
	 */
	public function __toString() {
		$res  = "File descriptor: | IP address:     | Client id: | Functions:\n";
		$res .= "-------------------------------------------------------------\n";

		foreach ($this->getWorkers() as $worker) {
			$res .= sprintf("%16d | %15s | %-10s | %s\n", $worker->getFd(), $worker->getIp(), $worker->getClientId(), implode(' ', $worker->getFunctions()));
		}

		return $res;
	}

	/**
	 * Get all the registered workers.
	 *
	 * @return GearmanAdminWorker[]
	 */
	public function getWorkers() {
		return $this->_workers;
	}

	/**
	 * Parse a workers string array as returned by the gearman server.
	 *
	 * @param string[] $workers
	 *
	 * @return null
	 */
	private function _parseWorkers(array $workers) {
		foreach ($workers as $line) {
			$matches = array();
			if (preg_match('/^(?<FD>\S+)\s(?<IP>\S+)\s(?<CLIENTID>\S+)\s:\s(?<FUNCTIONS>.*)\s$/', $line, $matches)) {
				$this->_workers[] = new GearmanAdminWorker((integer) $matches['FD'], $matches['IP'], $matches['CLIENTID'], explode(' ', $matches['FUNCTIONS']));
			}
		}
	}
}
