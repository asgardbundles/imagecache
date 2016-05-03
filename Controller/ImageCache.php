<?php
namespace Asgard\Imagecache\Controller;

/**
 * @Prefix("imagecache")
 */
class ImageCache extends \Asgard\Http\Controller {
	private function apply(\Imagine\Gd\Image $img, $preset) {
		try {
			$preset = $this->container['imagecache']->getPreset($preset);
		} catch(\Exception $e) {
			$this->notFound();
		}
		foreach($preset as $op=>$params) {
			switch($op) {
				case 'resize':
					if(isset($params['width']) && isset($params['height']) && isset($params['force']) && $params['force'])
						$img->resize(new Box($params['width'], $params['height']));
					elseif(isset($params['width']))
						$img->resize($img->getSize()->widen($params['width']));
					elseif(isset($params['height']))
						$img->resize($img->getSize()->heighten($params['height']));
					break;
				case 'crop':
					$img->crop(new Point(0, 0), new Box($params['width'], $params['height']));
					break;
			}
		}
		return $img;
	}

	/**
	 * @Route(value = ":preset/:src", requirements = {
	 * 	"src" : {
	 * 		"type" : "regex",
	 * 		"regex" : ".+"
	 * 	}
	 * })
	 */
	public function imgAction(\Asgard\Http\Request $request) {
		$webdir = $this->container['config']['webdir'];

		$imagine = new \Imagine\Gd\Imagine();
		if($this->container['config']['imagecache']) {
			$file = $webdir.'/cache/imagecache/'.$request['preset'].'/'.$request['src'];
			$mime = image_type_to_mime_type(exif_imagetype($file));
			if($mime == 'image/jpeg')
				$format = 'jpg';
			elseif($mime == 'image/png')
				$format = 'png';
			elseif($mime == 'image/gif')
				$format = 'gif';
			else
				throw new \Exception('Invalid image.');

			if(file_exists($file))
				$imagine->open($file)->show($format);
			else {
				$img = $imagine->open($webdir.'/'.$request['src']);
				$this->apply($img, $request['preset']);
				$img->save($file);
				$img->show($format);
			}
		}
		else {
			$file = $webdir.'/'.$request['src'];
			$mime = image_type_to_mime_type(exif_imagetype($file));
			if($mime == 'image/jpeg')
				$format = 'jpg';
			elseif($mime == 'image/png')
				$format = 'png';
			elseif($mime == 'image/gif')
				$format = 'gif';
			else
				throw new \Exception('Invalid image.');

			$img = $imagine->open($file);
			$this->apply($img, $request['preset']);
			$img->show($format);
		}

		$this->response->setHeader('Content-Type', $mime);
	}
}