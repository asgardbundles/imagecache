<?php
namespace Asgard\Imagecache\Libs;

class ImageCache {
	protected $presets = [];
	protected $app;

	public function __construct($app) {
		$this->app = $app;
	}

	public function getPreset($presetName) {
		if(!isset($this->presets[$presetName]))
			throw new \Exception('Preset '.$presetName.' does not exist.');
		return $this->presets[$presetName];
	}

	public function addPreset($presetName, $params) {
		$this->presets[$presetName] = $params;
	}
	
	public function url($src, $preset) {
		return $this->app['request']->url->to('imagecache/'.$preset.'/'.trim($src, '/'));
	}
	
	public function clearFile($file) {
		$webdir = $this->app['kernel']['webdir'];
		if(!file_exists($webdir.'/cache/imagecache/'))
			return;
		if ($handle = opendir($webdir.'/cache/imagecache/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && is_dir($webdir.'/cache/imagecache/'.$entry)) {
					if(file_exists($webdir.'/cache/imagecache/'.$entry.'/'.$file)) 
						unlink($webdir.'/cache/imagecache/'.$entry.'/'.$file);
				}
			}
			closedir($handle);
		}
	}
	
	public function clearPreset($preset) {
		\Asgard\File\FileSystem::delete($webdir.'/cache/imagecache/'.$preset);
	}
}