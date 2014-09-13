<?php
namespace Asgard\Imagecache;

class Bundle extends \Asgard\Core\BundleLoader {
	public function buildContainer(\Asgard\Container\Container $container) {
		$container->register('imagecache', function($container) { return new Libs\ImageCache($container); });
	}
}