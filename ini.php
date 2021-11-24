<?php
/*
 * Debug ini.php is used to install the debugger
 *
 */
namespace StartUp;

require_once START_UP_BASE_PATH . '/helpers/debug_helpers.php';
require_once START_UP_BASE_PATH . '/controllers/debug_controller.php';

class start_up_ini
{
	public function __construct()
	{
		$this->init();
		$this->installErrorHandlers();
	}

	/**
	 * The custom error handler caches the error and sends it to the custom show error function
	 */
	static function custom_error_handler($errno, $errstr, $errfile, $errline)
	{
		$debug = new Debug_controller();
		$content = new \stdClass();
		$content->type = "error";
		$content->file = $errfile;
		$content->line = $errline;
        $content->machine = gethostname();
        $content->backtrace = debug_backtrace();
        $debug->set_output_depth(7);
        $debug->show($content, ['error', $errno, $errstr]);
    }

	static function custom_exception_handler($ex)
	{
		$debug = new Debug_controller();
		$content = new \stdClass();
		$content->type = "exception";
		$content->file = $ex->getFile();
		$content->line = $ex->getLine();
        $content->machine = gethostname();
		$content->exception = $ex;
		$content->backtrace = debug_backtrace();
		$debug->show($content, ['exception', $ex->getMessage()]);
	}

	private function init()
	{
		if (!is_dir(START_UP_FILES_PATH)) {
			mkdir(START_UP_FILES_PATH);
			chmod(START_UP_FILES_PATH, 0777);
			$exportFolder = START_UP_FILES_PATH . '/export';
			mkdir($exportFolder);
			chmod($exportFolder, 0777);
		    $referencesFolder = $exportFolder . '/references';
			mkdir($referencesFolder);
			chmod($referencesFolder, 0777);
		}
	}

	private function installErrorHandlers()
	{
		if (empty(getenv('ignore_errors')) && empty(get_cfg_var('ignore_errors'))) {
			set_error_handler('\\StartUp\\Start_up_ini::custom_error_handler');
		}
		set_exception_handler('\\StartUp\\Start_up_ini::custom_exception_handler');
	}
}


