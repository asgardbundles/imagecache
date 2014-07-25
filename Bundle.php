<?php
namespace Asgard\Imagecache;

class Bundle extends \Asgard\Core\BundleLoader {
	public function buildApp($container) {
		$container->register('imagecache', function($container) { return new Libs\ImageCache($container); });
	}
}