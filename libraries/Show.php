<?php

namespace Harm;

require_once HARM_START_UP_BASE_PATH . '/libraries/Container.php';

class Show
{
	private static $counter = 0;
	private $prepaired = array();
	public $output_type = 'html';
	public $max_depth = 3;
	public $max_string_length = 100;
	public $ref_suffix = 'references/';

	private $stored_objects = array();


	public function get_prepaired()
	{
		return 'deprecated';
	}

	public function clear()
	{
		$this->prepaired = array();
		$this->stored_objects = array();
	}


	public function prepaire($to_be_shown, $tags = array(), $first = true)
	{
		$container = new \Harm\Container($this->max_depth);
		$container->set_var($to_be_shown);
		$output = $container->construct_export();

		if ( ! $output) {
			return '';
		}

		$dir = HARM_START_UP_FILES_PATH . '/export/';

		$this->affirmExportDir($dir);

		$label = $this->get_label_prefix($tags);
		$main_outputs_id = $output->main_object->get_id()->export_id;
		file_put_contents($dir.$main_outputs_id, $this->clean($label.json_encode($output->main_object->construct_export())));

		foreach ($output->reference_objects as $reference_object) {
			file_put_contents($dir.$this->ref_suffix.$reference_object->id, $this->clean(json_encode($reference_object)));
		}
		return '';
	}

	public function get_label_prefix( $tags ) {
		if (! is_array($tags)) {
			$tags = [$tags];
		}
		array_unshift($tags, gethostname());
		$number = self::$counter++;
		$encoded_tags = json_encode((array) $tags);
		if (!trim((string) $encoded_tags)) {
			$encoded_tags = json_encode([var_export($tags, true)]);
		}
		return '{ "tags" : '. $encoded_tags .', "index" : '.$number.', "pid" : "'.getmypid().'"},';
	}

	public function clean($string) {
		return str_replace('\"', "'", $string);
	}

	public function export()
	{
		echo 'deprecated';
	}

	/**
	 * @param $dir
	 */
	private function affirmExportDir($dir)
	{
		if (!is_dir(HARM_START_UP_FILES_PATH)) {
			mkdir(HARM_START_UP_FILES_PATH);
		}
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		if (!is_dir($dir . $this->ref_suffix)) {
			mkdir($dir . $this->ref_suffix);
		}
	}

}
