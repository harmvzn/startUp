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


	private $stored_objects = [];
	/**
	 * @var Debug_content_cache
	 */
	private $cache;

	public function __construct(bool $master)
	{
		$this->cache = new Debug_content_cache($master);
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

		$this->cache->save($main_outputs_id, $this->clean($label.$valid_json));

		foreach ($output->reference_objects as $reference_object) {
			/* @var $reference_object Object_export */
			$this->cache->save(strval($reference_object->id), $this->clean(json_encode($reference_object)), true);
		}
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

}
