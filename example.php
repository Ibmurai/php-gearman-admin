<?php

require dirname(__FILE__) . '/GearmanAdmin.php';

$admin = new GearmanAdmin();

echo $admin->refreshStatus();

