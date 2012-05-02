<?php
/**
 * This file is part of the php-gearman-admin framework.
 * @link https://github.com/Ibmurai/php-gearman-admin
 *
 * Examine the API to obtain more precise information.
 *
 * Example: Getting the number of available workers for a given function:
 *
 * <?php
 * $admin = new GearmanAdmin();
 * echo $admin->getStatus()->getAvailable('somefunction');
 * ?>
 *
 * @copyright Copyright 2012 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
require dirname(__FILE__) . '/GearmanAdmin.php';

$admin = new GearmanAdmin();

echo 'gearman version: ' . $admin->getVersion() . "\n";
echo $admin->getStatus();
echo $admin->getWorkers();
