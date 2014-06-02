<?php
namespace Asgard\Imagecache\Controllers;

/**
@Prefix("imagecache")
*/
class ImageCacheController extends \Asgard\Http\Controller {
	private function apply($img, $preset) {
		try {
			$preset = $this->app['imagecache']->getPreset($preset);
		} catch(\Exception $e) {
			return $this->response->setCode(404);
		}
		foreach($preset as $op=>$params) {
			switch($op) {
				case 'resize':
					$img->resize($params, (isset($params['force']) && $params['force']));
					break;
				case 'crop':
					$img->crop($params);
					break;
			}
		}
		return $img;
	}

	/**
	@Route(value = ":preset/:src", requirements = {
		"src" : {
			"type" : "regex",
			"regex" : ".+"
		}	
	})
	*/
	public function imgAction(\Asgard\Http\Request $request) {
		$webdir = $this->app['kernel']['webdir'];

		if($this->app['config']['imagecache']) {
			$file = $webdir.'/cache/imagecache/'.$request['preset'].'/'.$request['src'];
			if(file_exists($file)) {
				$img = \Asgard\Utils\ImageManager::load($file);
				$img->output();
			}
			else {
				$img = \Asgard\Utils\ImageManager::load($webdir.'/'.$request['src']);
				$this->apply($img, $request['preset'])->save($file);
				$img->output();
			}
		}
		else {
			$img = \Asgard\Utils\ImageManager::load($webdir.'/'.$request['src']);
			$this->apply($img, $request['preset']);
			$img->output();
		}
		
		$this->response->setHeader('Content-Type', image_type_to_mime_type($img->type));
	}
}