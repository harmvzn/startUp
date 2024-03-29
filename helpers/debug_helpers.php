<?php

if (!function_exists('std'))
{
	/**
	 * Usable keys:
	 * _show
	 * _output_type
	 * _output_depth
	 * _max_string
	 * @global \StartUp\Debug_controller $debug_controller
	 * @return \StartUp\Debug_controller
	 */
	function std()
	{
		global $debug_controller;
		$args = func_get_args();
		if (!isset($debug_controller)) {
			$debug_controller = new \StartUp\Debug_controller();
		}
		$return = null;
		if ($args) {
			$return = $debug_controller->index($args);
		}
		return  $return ?: $debug_controller;
	}
}

if (!function_exists('start_up_shut_down_handler')) {
	function start_up_shut_down_handler()
	{
		global $debug_controller;
		if (isset($debug_controller)) {
			$debug_controller->finish();
		}
	}
}

if ( ! function_exists('_exception_handler'))
{
	function _exception_handler($exception)
	{		
		StartUp\start_up_ini::custom_exception_handler($exception);
		$_error =& load_class('Exceptions', 'core');
		$_error->log_exception('error', 'Exception: '.$exception->getMessage(), $exception->getFile(), $exception->getLine());

		if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors')))
		{
			$_error->show_exception($exception);
		}
		exit(1);
	}
}

if ( ! function_exists('_error_handler'))
{
	function _error_handler($severity = E_NOTICE, $message = '', $filepath = '', $line = 0)
	{
		StartUp\start_up_ini::custom_error_handler($severity, $message, $filepath, $line);

		$is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

		// When an error occurred, set the status header to '500 Internal Server Error'
		// to indicate to the client something went wrong.
		// This can't be done within the $_error->show_php_error method because
		// it is only called when the display_errors flag is set (which isn't usually
		// the case in a production environment) or when errors are ignored because
		// they are above the error_reporting threshold.
		if ($is_error)
		{
			set_status_header(500);
		}

		// Should we ignore the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		if (($severity & error_reporting()) !== $severity)
		{
			return;
		}

		$_error =& load_class('Exceptions', 'core');
		$_error->log_exception($severity, $message, $filepath, $line);

		if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors')))
		{
			$_error->show_php_error($severity, $message, $filepath, $line);
		}

		// If the error is fatal, the execution of the script should be stopped because
		// errors can't be recovered from. Halting the script conforms with PHP's
		// default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
		if ($is_error)
		{
			exit(1);
		}
	}
}

register_shutdown_function('start_up_shut_down_handler');
