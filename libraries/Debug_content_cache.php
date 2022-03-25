<?php

namespace StartUp;

class Debug_content_cache
{
	/**
	 * @var bool
	 */
	private $master;
	public $ref_suffix = 'references/';

	public function __construct(bool $master)
	{
		$this->master = $master;
	}

	public function save(string $file_name, string $file_contents, bool $reference)
	{
		if ($this->master) {
			$dir = START_UP_FILES_PATH . '/export/';
			$this->affirmExportDir($dir);
			if ($reference) {
				$dir .= $this->ref_suffix;
			}
			file_put_contents($dir.$file_name, $file_contents);
		} else {
			$this->delete_all(); // clean up

			$curl = curl_init();
			$file = new \CURLStringFile($file_name, $file_contents);
			curl_setopt_array($curl, [
				CURLOPT_URL => 'http://startUp/api/save_debug_content' . ($reference ? '/reference' : ''),
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => [
					'file' => $file
				]
			]);
			curl_exec($curl);
			curl_close($curl);
		}
	}

	/**
	 * @param $dir
	 */
	private function affirmExportDir($dir)
	{
		if (!is_dir(START_UP_FILES_PATH)) {
			mkdir(START_UP_FILES_PATH);
		}
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		if (!is_dir($dir . $this->ref_suffix)) {
			mkdir($dir . $this->ref_suffix);
		}
	}

	public function delete_all()
	{
		$dir = realpath(START_UP_FILES_PATH . '/export');

		if ( ! $dir || ! is_dir($dir) ) {
			return;
		}
		$command = "rm -rf $dir;";
		`$command`;
	}

}
