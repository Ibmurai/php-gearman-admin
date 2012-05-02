<?php
/**
 * This file is part of the php-gearman-admin framework.
 * @link https://github.com/Ibmurai/php-gearman-admin
 *
 * @copyright Copyright 2012 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
/**
 * Data about a registered worker.
 *
 * @author Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class GearmanAdminWorker {
	/** @var integer File descriptor. */
	private $_fd;

	/** @var string IP address. */
	private $_ip;

	/** @var string Client id. */
	private $_clientId;

	/** @var string[] Registered functions. */
	private $_functions;

	/**
	 * Construct a new GearmanAdminWorker.
	 *
	 * @param integer  $fd        File descriptor.
	 * @param string   $ip        IP address.
	 * @param string   $clientId  Client id.
	 * @param string[] $functions Registered functions.
	 *
	 * @return null
	 */
	public function __construct($fd, $ip, $clientId, array $functions) {
		$this->_fd        = $fd;
		$this->_ip        = $ip;
		$this->_clientId  = $clientId;
		$this->_functions = $functions;
	}

	/**
	 * Get the file descriptor.
	 *
	 * @return integer
	 */
	public function getFd() {
		return $this->_fd;
	}

	/**
	 * Get the IP address.
	 *
	 * @return string
	 */
	public function getIp() {
		return $this->_ip;
	}

	/**
	 * Get the client id.
	 *
	 * @return string
	 */
	public function getClientId() {
		return $this->_clientId;
	}

	/**
	 * Get the registered function names.
	 *
	 * @return string[]
	 */
	public function getFunctions() {
		return $this->_functions;
	}
}
