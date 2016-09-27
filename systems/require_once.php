<?php 
	#
	define('PHP_Ezy_Plugin_Version','1.1.0');			
	#################################################### 
	global $__sys_mem_usage, $__scipt_start_time;#######
    global $gShutdownScheduler;#########################
	####################################################

	######## Please don't remove the below line#########
    ob_start();   									   #
    ####################################################

    ##########<Memory_Usage_Records>#####################
    $__sys_mem_usage = ( function_exists('memory_get_usage') ? memory_get_usage() : 0 );
    list($usec, $sec) = explode(" ", microtime());
    ##########</Memory_Usage_Records>#######################

    ##########<Time_Usage_Records>#####################
    $__scipt_start_time = ((float)$usec + (float)$sec);
    ##########<Time_Usage_Records>#####################

    #
    require_once ( dirname(__FILE__) . '/tgr_plugin_engine.php');