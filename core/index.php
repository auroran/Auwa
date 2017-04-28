<?php
namespace Auwa;
/**
 * CORE ADMINISTRATION
 *
 * index.php file for admin
 *
 * Call the Auwa Core Launcher
 */
$is_core=true;
require_once 'core.php';
Error::report(1); // E_ERROR

Auwa::coreLaunch();
?>
