<?php
/**
 * This file is part of the php-gearman-admin framework.
 * @link https://github.com/Ibmurai/php-gearman-admin
 *
 * @copyright Copyright 2012 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
require_once dirname(__FILE__) . '/GearmanAdminStatus.php';
require_once dirname(__FILE__) . '/GearmanAdminWorker.php';
require_once dirname(__FILE__) . '/GearmanAdminWorkers.php';
/**
 * Query a gearman server to get the status of workers, jobs and functions, set max queue function settings or shut the server down.
 *
 * @author Jens Riisom
 */
class GearmanAdmin {
	/** @var string Host. */
	private $_host;

	/** @var integer Port. */
	private $_port;

	/** @var integer Timeout in milliseconds (for connection and reading/writing data). */
	private $_timeout;

	/** @var GearmanAdminStatus The status object. */
	private $_status;

	/** @var GearmanAdminWorkers The workers object. */
	private $_workers;

	/** @var string The version of the gearman server. */
	private $_version;

	/**
	 * Construct a new GearmanAdmin instance.
	 *
	 * @param string  $host    Host.
	 * @param integer $port    Port.
	 * @param integer $timeout Timeout in milliseconds (for connection and reading/writing data).
	 */
	public function __construct($host = '127.0.0.1', $port = 4730, $timeout = 500) {
		$this->_host    = $host;
		$this->_port    = $port;
		$this->_timeout = $timeout;
	}

	/**
	 * Open a connection to the gearman server.
	 *
	 * @return resource An open telnet socket to a gearman server.
	 *
	 * @throws RuntimeException If a connection error occurs.
	 */
	private function _connect() {
		$errno  = 0;
		$errstr = '';
		$resource = fsockopen($this->_host, $this->_port, $errno, $errstr, $this->_timeout / 1000);

		if (!$resource) {
			throw new RuntimeException("Failed to connect to gearman server at {$this->_host}:{$this->_port}. Error number [$errno], message: $errstr");
		} else {
			stream_set_timeout($resource, 0, $this->_timeout * 1000);
			return $resource;
		}
	}

	/**
	 * Get the gearman version from the gearman server.
	 *
	 * @return string The gearman version.
	 */
	public function getVersion() {
		if (!isset($this->_version)) {
			return $this->_getVersion();
		} else {
			return $this->_version;
		}
	}

	/**
	 * Get the GearmanAdminStatus object describing the gearman server status.
	 *
	 * @return GearmanAdminStatus The status.
	 */
	public function getStatus() {
		if (!isset($this->_status)) {
			return $this->_getStatus();
		} else {
			return $this->_status;
		}
	}

	/**
	 * Get the GearmanAdminWorkers object describing the gearman server workers status.
	 *
	 * @return GearmanAdminWorkers Contains information about the registered workers of a gearman server.
	 */
	public function getWorkers() {
		if (!isset($this->_workers)) {
			return $this->_getWorkers();
		} else {
			return $this->_workers;
		}
	}

	/**
	 * Refresh all information about the gearman server.
	 *
	 * @return null
	 */
	public function refresh() {
		$gearman = $this->_connect();

		$this->_getStatus($gearman);
		$this->_getWorkers($gearman);
		$this->_getVersion($gearman);

		fclose($gearman);
	}

	/**
	 * Refresh the status from the gearman server.
	 *
	 * @return GearmanAdminStatus The refreshed status.
	 */
	public function refreshStatus() {
		return $this->_getStatus();
	}

	/**
	 * Refresh the gearman version from the gearman server.
	 *
	 * @return string The refreshed version.
	 */
	public function refreshVersion() {
		return $this->_getVersion();
	}

	/**
	 * Refresh the status from the gearman server.
	 *
	 * @return GearmanAdminStatus The refreshed status.
	 */
	public function refreshWorkers() {
		return $this->_getWorkers();
	}

	/**
	 * Shut the gearman server down!
	 *
	 * @param boolean $gracefully Set to false to shut it down in a careless way.
	 *
	 * @return null
	 */
	public function shutdown($gracefully = true) {
		$gearman = $this->_connect();

		fputs($gearman, 'shutdown' . ($gracefully ? ' graceful' : ''));

		fclose($gearman);
	}

	/**
	 * This sets the maximum queue size for a function.
	 * If no size is given, the default is used.
	 * If the size is negative, then the queue is set to be unlimited.
	 *
	 * @param string       $function  The function to set the queue size for.
	 * @param integer|null $queueSize The size to set. If the size is negative, then the queue is set to be unlimited. If no size is given, the default is used.
	 */
	public function maxQueue($function, $queueSize = null) {
		$gearman = $this->_connect();

		fputs($gearman, "maxqueue $function" . ($queueSize !== null ? " $queueSize" : ''));

		fclose($gearman);
	}

	/**
	 * Get and/or refresh the status from the gearman server.
	 *
	 * @param resource $gearman A gearman telnet resource.
	 *
	 * @return GearmanAdminStatus
	 */
	private function _getStatus($connection = null) {
		if ($connection === null) {
			$gearman = $this->_connect();
		} else {
			$gearman = $connection;
		}

		fputs($gearman, "status\n");
		$rawStatus = array();
		while (!feof($gearman) && ($line = fgets($gearman)) && ($line != ".\n")) {
			$rawStatus[] = $line;
		}
		$this->_status = new GearmanAdminStatus($rawStatus);

		if ($connection === null) {
			fclose($gearman);
		}

		return $this->_status;
	}

	/**
	 * Get and/or refresh the workers information from the gearman server.
	 *
	 * @param resource $gearman A gearman telnet resource.
	 *
	 * @return null
	 */
	private function _getWorkers($connection = null) {
		if ($connection === null) {
			$gearman = $this->_connect();
		} else {
			$gearman = $connection;
		}

		fputs($gearman, "workers\n");
		$rawWorkers = array();
		while (!feof($gearman) && ($line = fgets($gearman)) && ($line != ".\n")) {
			$rawWorkers[] = $line;
		}
		$this->_workers = new GearmanAdminWorkers($rawWorkers);

		if ($connection === null) {
			fclose($gearman);
		}

		return $this->_workers;
	}

	/**
	 * Get and/or refresh the gearman version from the gearman server.
	 *
	 * @param resource $gearman A gearman telnet resource.
	 *
	 * @return string
	 */
	private function _getVersion($connection = null) {
		if ($connection === null) {
			$gearman = $this->_connect();
		} else {
			$gearman = $connection;
		}

		fputs($gearman, "version\n");
		$rawVersion = array();
		while (!feof($gearman) && ($line = fgets($gearman)) && ($line != ".\n")) {
			$rawVersion[] = $line;
		}
		if (count($rawVersion) == 1) {
			$this->_version = trim($rawVersion[0]);
		} else {
			unset($this->_version);
		}

		if ($connection === null) {
			fclose($gearman);
		}

		return $this->_version;
	}
}
