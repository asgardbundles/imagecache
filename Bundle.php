<?php
namespace Asgard\Imagecache;

class Bundle extends \Asgard\Core\BundleLoader {
	public function buildApp($app) {
		$app->register('imagecache', function($app) { return new Libs\ImageCache($app); });
	}
}