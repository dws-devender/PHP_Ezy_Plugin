<?php
class TGR {
	const HOOK_NONE = 0x001;
	const HOOK_RETURN = 0x002;
	const HOOK_EXCEPTION = 0x004;
	static function _hook_signature($hkname, $hkfun, $hkp) {
		# Force to converted into string 
		$hkname = $hkname . "";

		# Guess 
		if ( is_string($hkfun) )
		    return md5($hkfun);
		# Object
		if ( is_object($hkfun) ) {
		    // Closures are currently implemented as objects
		    $hkfun = array( $hkfun, '' );
		} else {
		    $hkfun = (array) $hkfun;
		}

		#
		if (is_object($hkfun[0]) ) {
			// Object Class Calling
			if ( !function_exists('spl_object_hash') ) {
		         return spl_object_hash($hkfun[0]) . $hkfun[1];
		    } else {
		        $obj_idx = get_class($hkfun[0]) . "::" .  (empty($hkfun[1]) ? 'unknown' : $hkfun[1]) . "::" . $hkp;
		       	return $obj_idx;
		    }
		} elseif ( is_string( $hkfun[0] ) ) {
		   // Static Calling
		   return md5($hkfun[0] . '::' . $hkfun[1] . "::" . $hkp);
		}
		return md5(time());
	}
	static function register_hook($hkname, $hkfun, $hkp = 10, $hkorders = array(), $hkrule = TGR::HOOK_RETURN, $xparams =  array()) {
		#  Force to int
		$hkp = (int) $hkp;
		$hkp = ( $hkp >=1 and $hkp <= 10 ) ? $hkp : 10;
		$hkname = preg_replace('/[^0-9a-z_]/i', '', $hkname);
		$hkrule = ( in_array($hkrule, array(self::HOOK_NONE, self::HOOK_RETURN, self::HOOK_EXCEPTION) ) ? $hkrule: self::HOOK_RETURN);
		$hkguid   = self::_hook_signature($hkname, $hkfun, $hkp)	;
		#'xparams'=> array(), 'orders' => FALSE, 'priority' => 
		if ( !isset(TGR_AppShare::$hooks[$hkname]))
			TGR_AppShare::$hooks[$hkname] = array( 10 => array(), 9 => array(), 8 => array(), 7 => array(), 6 => array(), 5 => array(), 4 => array(), 3 => array(), 2 => array(), 1 => array());
		if ( !isset(TGR_AppShare::$hooks[$hkname][$hkp]))
			TGR_AppShare::$hooks[$hkname][$hkp] = array(); 
		
		# Object
		if ( is_object($hkfun) ) {
		    // Closures are currently implemented as objects
		    $hkfun = array( $hkfun, '' );
		} else {
		    $hkfun = (array) $hkfun;
		}

		$new = array( 'xparams'=> array(), 'orders' => FALSE, 'callback'=> null);
		$new['xparams'] = (array) $xparams;
		$new['orders'] = $hkorders ;
		$new['callback'] = $hkfun ;

		#
		foreach (TGR_AppShare::$hooks[$hkname][$hkp] as $key => $value) {
			$guid = key($value)
			if ( $key == $hkguid)
				TGR_AppShare::$hooks[$hkname][$hkp]
		}
		TGR_AppShare::$hooks[$hkname][$hkp][$hkguid] = $new;


		print_r(TGR_AppShare::$hooks);
		die;

	}
}