<?php
/*########################[General_Info]################################
# @Script file : index.php
# @Version     : 1.0.0
# @Author      : Bijaya Kumar
# @Email       : it.bijaya@gmail.com
# @Description : Example File
##########################[General Info]################################
*/
define('PHP_Ezy_Plugin_DS', DIRECTORY_SEPARATOR);
define('PHP_Ezy_Plugin_System_Dir', dirname(__FILE__) . PHP_Ezy_Plugin_DS .'..' . PHP_Ezy_Plugin_DS. 'systems' . PHP_Ezy_Plugin_DS);
define('PHP_Ezy_Plugin_Dir', dirname(__FILE__) . PHP_Ezy_Plugin_DS . 'plugins' . PHP_Ezy_Plugin_DS);
define('PHP_Ezy_Plugin_Cache_Dir', PHP_Ezy_Plugin_Dir . '.cache' . PHP_Ezy_Plugin_DS);
require_once (PHP_Ezy_Plugin_System_Dir .'require_once.php');