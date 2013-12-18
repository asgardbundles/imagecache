<?php
namespace Coxis\Imagecache\Libs;

class ImageCache {
	private static $presets = array();

	public static function getPreset($presetName) {
		if(!isset(static::$presets[$presetName]))
			throw new \Exception('Preset '.$presetName.' does not exist.');
		return static::$presets[$presetName];
	}

	public static function addPreset($presetName, $params) {
		static::$presets[$presetName] = $params;
	}
	
	public static function src($src, $preset) {
		return 'imagecache/'.$preset.'/'.trim($src, '/');
	}
	
	public static function clearFile($file) {
		if(!file_exists('web/cache/imagecache/'))
			return;
		if ($handle = opendir('web/cache/imagecache/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && is_dir('web/cache/imagecache/'.$entry)) {
					if(file_exists('web/cache/imagecache/'.$entry.'/'.$file)) 
						unlink('web/cache/imagecache/'.$entry.'/'.$file);
				}
			}
			closedir($handle);
		}
	}
	
	public static function clearPreset($preset) {
		\Coxis\Utils\FileManager::rmdir('web/cache/imagecache/'.$preset);
	}
}