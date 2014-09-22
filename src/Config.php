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
use Bldr\DependencyInjection\Loader;
use Bldr\Exception\ConfigurationFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\Input\InputInterface;

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
     *
     * @throws Exception\ConfigurationFileNotFoundException
     * @throws \Exception
     */
    public static function read(ContainerBuilder $container)
    {
        /** @var InputInterface $input */
        $input = $container->get('input');

        $locations = [getcwd(), getcwd().'/.bldr/'];
        if ($input->hasParameterOption('--global')) {
            $locations = array_merge($locations, [getenv('HOME'), getenv('HOME').'/.bldr/']);
        }

        $locator  = new FileLocator($locations);
        $resolver = new LoaderResolver(
            [
                new Loader\YamlFileLoader($container, $locator),
                new Loader\XmlFileLoader($container, $locator),
                new Loader\PhpFileLoader($container, $locator),
                new Loader\IniFileLoader($container, $locator),
                new Loader\JsonFileLoader($container, $locator)
            ]
        );

        $loader      = new DelegatingLoader($resolver);
        $files       = static::findFiles($input);
        $foundConfig = false;
        foreach ($files as $file) {
            try {
                $loader->load($file);
                $foundConfig = true;
            } catch (\Exception $e) {
                if (get_class($e) !== 'InvalidArgumentException') {
                    throw $e;
                }
            }
        }

        if (!$foundConfig) {
            throw new ConfigurationFileNotFoundException(
                sprintf(
                    "Either couldn't find the configuration file, or couldn't read it. ".
                    "Make sure the extension is valid (%s). Tried: %s",
                    implode(', ', static::$TYPES),
                    implode(', ', $files)
                )
            );
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return array
     * @throws \Exception
     */
    public static function findFiles(InputInterface $input)
    {
        if ($input->hasParameterOption('--config-file')) {
            $file = $input->getParameterOption('--config-file');
            if (file_exists($file)) {
                return [$file];
            }

            throw new ConfigurationFileNotFoundException(
                sprintf("Couldn't find the configuration file: %s", $file)
            );
        }

        $format = $input->hasParameterOption('--config-format')
            ? $input->getParameterOption('--config-format')
            : static::$DEFAULT_TYPE
        ;

        return [
            sprintf("%s.%s", static::$NAME, $format),
            sprintf("%s.%s.dist", static::$NAME, $format)
        ];
    }
}
