<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr;

use Bldr\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Bldr\DependencyInjection\Loader\YamlFileLoader;
use Bldr\DependencyInjection\Loader\XmlFileLoader;
use Bldr\DependencyInjection\Loader\PhpFileLoader;
use Bldr\DependencyInjection\Loader\IniFileLoader;
use Bldr\DependencyInjection\Loader\JsonFileLoader;
use Symfony\Component\Yaml\Yaml;
use Zend\Json\Json;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Config
{
    /**
     * @var string $NAME
     */
    public static $NAME = '.bldr';

    /**
     * @var array $TYPES
     */
    public static $TYPES = ['yml', 'xml', 'php', 'ini', 'json'];

    /**
     * @var string $DEFAULT_TYPE
     */
    public static $DEFAULT_TYPE = 'yml';

    /**
     * @param ContainerBuilder $container
     */
    public static function read(ContainerBuilder $container)
    {
        list($file, $type) = static::getFile();

        $locator  = new FileLocator([getcwd(), getcwd() . '/.bldr/']);
        $resolver = new LoaderResolver(
            [
                new YamlFileLoader($container, $locator),
                new XmlFileLoader($container, $locator),
                new PhpFileLoader($container, $locator),
                new IniFileLoader($container, $locator),
                new JsonFileLoader($container, $locator)
            ]
        );

        $loader = new DelegatingLoader($resolver);
        $loader->load($file);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getFile()
    {
        $tried = [];
        foreach ([getcwd(), getcwd().'/.bldr/'] as $dir) {
            foreach (static::$TYPES as $type) {

                $file = static::$NAME . '.' . $type;

                if (file_exists($dir.'/'.$file)) {
                    return [$file, $type];
                }

                $tried[] = $file;
                $file .= ".dist";

                if (file_exists($dir.'/'.$file)) {
                    return [$file, $type];
                }

                $tried[] = $file;
            }
        }

        throw new \Exception("Couldn't find a config file. Tried: " . implode(', ', $tried));
    }
}
