<?php

namespace StartUp;

require_once START_UP_BASE_PATH . '/libraries/container.php';

class Show
{
	private static $counter = 0;
	private $prepared = [];
	public $output_type = 'html';
	public $max_depth = 3;
	public $max_string_length = 100;
	public $ref_suffix = 'references/';

	private $stored_objects = [];


	public function get_prepared()
	{
		return 'deprecated';
	}

	public function clear()
	{
		$this->prepared = array();
		$this->stored_objects = array();
	}


	/**
	 * @param $to_be_shown
	 * @param array $tags
	 * @param $output_type
	 */
	public function prepare($to_be_shown, $tags = [], $output_type)
	{
		$container = new \StartUp\Container($this->max_depth);
		$container->set_var($to_be_shown);
		$output = $container->construct_export();

		if (!$output) {
			return;
		}

		$dir = START_UP_FILES_PATH . '/export/';

		$this->affirmExportDir($dir);

		$label = $this->get_label_prefix($tags, $output_type);
		$main_outputs_id = $output->main_object->get_id()->export_id;

		$valid_json = null;
		if ($output_type === 'json') {
			try {
				json_decode($output->main_object->construct_export());
				$valid_json = $output->main_object->construct_export();
			} catch (\Exception $e) {
				// fail
			}
		}
		if (!$valid_json) {
			$valid_json = json_encode($output->main_object->construct_export());
		}

		file_put_contents($dir.$main_outputs_id, $this->clean($label.$valid_json));

		foreach ($output->reference_objects as $reference_object) {
			/* @var $reference_object Object_export */
			file_put_contents($dir.$this->ref_suffix . strval($reference_object->id), $this->clean(json_encode($reference_object)));
		}
		return;
	}

	public function get_label_prefix( $tags , $output_type) {
		if (! is_array($tags)) {
			$tags = [$tags];
		}
		array_unshift($tags, gethostname());
		$number = self::$counter++;
		$encoded_tags = json_encode((array) $tags);
		if (!trim((string) $encoded_tags)) {
			$encoded_tags = json_encode([var_export($tags, true)]);
		}
		return '{ "tags" : '. $encoded_tags .', "index" : '.$number.', "pid" : "'.getmypid().'", "type" : "' . $output_type . '"},';
	}

	public function clean($string) {
		return str_replace('\"', "'", $string);
	}

	/**
	 * @param $dir
	 */
	private function affirmExportDir($dir)
	{
		if (!is_dir(START_UP_FILES_PATH)) {
			mkdir(START_UP_FILES_PATH);
			chmod(START_UP_FILES_PATH, 0777);
		}
		if (!is_dir($dir)) {
			mkdir($dir);
			chmod($dir, 0777);
		}
		if (!is_dir($dir . $this->ref_suffix)) {
			mkdir($dir . $this->ref_suffix);
			chmod($dir . $this->ref_suffix, 0777);
		}
	}
}
