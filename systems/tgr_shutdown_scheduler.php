<?php
class TGR_ShutdownScheduler {
    private $callbacks; // array to store user callbacks
    private $exclusive = '' ;
    public function __construct() {
        $this->callbacks = array();
        register_shutdown_function(array($this, 'callRegisteredShutdown'));
    }
    public function __destruct() {
    	$this->callbacks = array();
    }
    public function exclusive($identifier = null) {
    	if ( $identifier === null  ){
    		return $this->exclusive ;
    	}
    	return $this->exclusive = $identifier;
    }
    public function registerShutdownEvent() {
       $callback = func_get_args();
       $identifier = array_shift($callback);
       // don't overwrite the systems shutdown
       if ( isset($this->callbacks[$identifier]) && $identifier ==='__onAppShutDown' ) {
       		return false;
       }

       if (empty($callback)) {
            trigger_error('No callback passed to '.__FUNCTION__.' method', E_USER_ERROR);
            return false;
        }
		
        if (!is_callable($callback[0])) {
		       trigger_error('Invalid callback passed to the '.__FUNCTION__.' method', E_USER_ERROR);
            return false;
        }
        $this->callbacks[$identifier] = $callback;
		return true;
    }
	/**
   * Function...
   */
    public function callRegisteredShutdown() {	
    	foreach ($this->callbacks as $identifier=>$arguments) {
    		if (  $this->exclusive ==''  || ($identifier === $this->exclusive) ) {
            	$callback = array_shift($arguments);
            	call_user_func_array($callback, $arguments);
        	}
        }
    }
	/**
   * Function...
   */
	function unRegisterShutdownEvent($identifier) {
		if ( isset( $this->callbacks[$identifier]) && $identifier !=='__onAppShutDown')
			unset( $this->callbacks[$identifier]);
	}
	/**
   * Function...
   */
    public function failForViewContents($output_buffer_contents = '') {
		$lErr=  error_get_last();
	    $str_err = array( 1=> "FatalError", 4=>'ParseError', 16=>'CoreError', 64=>'CompileError', 256=> 'UserGeneratedError', 4096=>'RecovarableError');
	    if ( is_null($lErr) or(  isset($lErr['type']) && !array_key_exists($lErr['type'], $str_err) ) )
	   	  return ;
	 	$what_errors = ob_get_contents();
	 	ob_end_clean();
	 	$rev_msg ="<br />" . chr(10) ."<b>". $str_err[$lErr['type']]." error</b>:  {$lErr['message']} in <b>{$lErr['file']}</b> on line <b>{$lErr['line']}</b><br />";
	 	$what_errors = str_replace($rev_msg,'',$what_errors);
	 	if ( isset($lErr['type']) && array_key_exists($lErr['type'], $str_err) ) {
		   $lErr['str_type']= $str_err[$lErr['type']];
		   // show_where_errors ??
		   $errors_lines_in_code_file= array() ;
		   if(   file_exists($lErr['file']) && is_readable($lErr['file'])   )  {
			   	$fc= file_get_contents($lErr['file']);
				$psli_lines = explode("\r\n", $fc);
				$lc=  count($psli_lines)+1 ;
				$is_above =   $lErr['line'] > 2 ;
				$is_below =   $lErr['line'] + 2 < $lc ;
				$from_above = 1 ;
				$from_below = 1 ;				
				for( $i=1;$i<=2;$i++) {
					if( $lErr['line']+ $i < $lc ) {
						$from_below = $lErr['line']+ $i;
						continue;
					}
				}
				for( $i=1;$i<=2;$i++) {
					if( $lErr['line']-$i > 1)  {
						$from_above =  $lErr['line'] - $i;
						continue;
					}
				}
				
				$errors_lines = array(); 
				
			
			
				
				for( $j=$lErr['line']-1 ; $j>=$from_above-1;$j--) {
					$s = ($j  == $lErr['line']-1 ? "<font style='background-color:red'>" . htmlentities($psli_lines[$j]) ."</font>" : htmlentities($psli_lines[$j]) );	
					array_unshift( $errors_lines,$s);
				}
				if ($is_above ) {
					array_unshift( $errors_lines,'...');
					array_unshift( $errors_lines,'...');
				}
			
				for( $j=$lErr['line']; $j<=$from_below;$j++)
					$errors_lines[] =  htmlentities($psli_lines[$j] );
				if ($is_below ) {
					$errors_lines[] ='...';
					$errors_lines[] ='...';
				}			
					
		   }
		   
		   $output_buffer_contents_m1 = ob_get_contents();
	   	   ob_end_clean();	
	   	   #$php_errors = "<hr /><span style='color:red;font-weight:bold'>{$m[2][0]}{@$m[4][0]}{$m[6][0]}</span><hr />";
	   	   $output_buffer_contents =$output_buffer_contents .$output_buffer_contents_m1. $what_errors;
		   send_status_header(500);
		   app_write_log(print_r($lErr, true));
	       app_on_error('script_err');
		   // $what_errors =str_replace($m[0],"<hr /><span style='color:red;font-weight:bold'>$m[0]</span>", $what_errors);
		   $missing=  SYSTEM_ERROR_PAGE_ROOT . DS . 'php-errors.html.php';
		   include($missing );
		   exit(0);
	   }
	  echo $output_buffer_contents . $what_errors;
    }
	/**
   * Function...
   * @static
   */
   public function  __onAppShutDown() {
	   $lErr=  error_get_last();
	   $str_err = array( 1=> "Fatal", 4=>'Parse');
	   if ( is_null($lErr) or(  isset($lErr['type']) && !array_key_exists($lErr['type'], $str_err) ) )
	   	 return ;
		  
	   $what_errors = ob_get_contents();	 
	   while (  ob_get_level()>  0 )  ob_end_clean();
	   $rev_msg ="<br />" . chr(10) ."<b>". $str_err[$lErr['type']]." error</b>:  {$lErr['message']} in <b>{$lErr['file']}</b> on line <b>{$lErr['line']}</b><br />";
	   $output = str_replace($rev_msg,'',$what_errors);
	   if ( isset($lErr['type']) && array_key_exists($lErr['type'], $str_err) ) {
		   $lErr['str_type']= $str_err[$lErr['type']];
		   // show_where_errors ??
		   $errors_lines_in_code_file= array() ;
		   if(   file_exists($lErr['file']) && is_readable($lErr['file'])   )  {
			   	$fc= file_get_contents($lErr['file']);
				$psli_lines = explode("\r\n", $fc);
				$lc=  count($psli_lines)+1 ;
				$is_above =   $lErr['line'] > 2 ;
				$is_below =   $lErr['line'] + 2 < $lc ;
				$from_above = 1 ;
				$from_below = 1 ;				
				for( $i=1;$i<=2;$i++) {
					if( $lErr['line']+ $i < $lc ) {
						$from_below = $lErr['line']+ $i;
						continue;
					}
				}
				for( $i=1;$i<=2;$i++) {
					if( $lErr['line']-$i > 1)  {
						$from_above =  $lErr['line'] - $i;
						continue;
					}
				}
				
				$errors_lines = array(); 
				
			
			
				
				for( $j=$lErr['line']-1 ; $j>=$from_above-1;$j--) {
					$s = ($j  == $lErr['line']-1 ? "<font style='background-color:red'>" . htmlentities($psli_lines[$j]) ."</font>" : htmlentities($psli_lines[$j]) );	
					array_unshift( $errors_lines,$s);
				}
				if ($is_above ) {
					array_unshift( $errors_lines,'...');
					array_unshift( $errors_lines,'...');
				}
			
				for( $j=$lErr['line']; $j<=$from_below;$j++)
					$errors_lines[] = isset($psli_lines[$j] ) ? htmlentities($psli_lines[$j] ): '';
				if ($is_below ) {
					$errors_lines[] ='...';
					$errors_lines[] ='...';
				}			
					
		   }
		   $output_buffer_contents=$output;
		   send_status_header(500);
		   app_write_log(print_r($lErr, true));
	       app_on_error('script_err');
		   $missing=  SYSTEM_ERROR_PAGE_ROOT . DS . 'php-errors.html.php';
		   include($missing );
		   exit(1);
	   }
	}
   public static function staticTest() {
        echo '_SERVER array is '.count($_SERVER).' elements long.<br />';
    }
}
?>