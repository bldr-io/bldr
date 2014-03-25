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

use Composer\Json\JsonFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Config extends ParameterBag
{
    /**
     * @var string $NAME
     */
    public static $NAME = '.bldr';

    /**
     * @var array $TYPES
     */
    public static $TYPES = ['yml' => true, 'json' => true];

    /**
     * @var string $DEFAULT_TYPE
     */
    public static $DEFAULT_TYPE = 'yml';

    /**
     * @throws \Exception
     * @internal param string $file
     * @return Config
     */
    public static function factory()
    {
        list($file, $type) = static::getFile();
        static::isTypeAllowed($type);

        // Yaml
        if ($type === 'yml') {
            $data = file_get_contents($file);

            return new static(Yaml::parse($data));
        }

        // Json
        $json = new JsonFile($file);

        return new static($json->read());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getFile()
    {
        $tried = [];
        foreach (array_keys(static::$TYPES) as $type) {

            $file = static::$NAME . '.' . $type;

            if (file_exists($file)) {
                return [$file, $type];
            }

            $tried[] = $file;
            $file .= ".dist";

            if (file_exists($file)) {
                return [$file, $type];
            }

            $tried[] = $file;
        }

        throw new \Exception("Couldn't find a config file. Tried: " . implode(', ', $tried));
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     */
    public static function isTypeAllowed($type)
    {
        if (!array_key_exists($type, static::$TYPES)) {
            throw new \Exception(sprintf("%s is not a valid extension. Feel free to make a PR", $type));
        }
    }

    /**
     * @param string  $type
     * @param array   $data
     * @param Boolean $dist
     * @param Boolean $delete
     *
     * @throws \Exception
     * @return static
     */
    public static function create($type, $data = [], $dist = false, $delete = false)
    {
        static::isTypeAllowed($type);

        $file = static::$NAME . '.' . $type . ($dist ? '.dist' : '');

        static::checkForFile($file, $delete);

        // Yaml
        if ($type === 'yml') {
            $yaml = Yaml::dump($data, 8);
            file_put_contents($file, $yaml);

            return new static($data);
        }

        // Json
        $json = new JsonFile($file);
        $json->write($data);

        return new static($json->read());
    }

    /**
     * @param string  $file
     * @param Boolean $delete
     *
     * @throws \Exception
     */
    public static function checkForFile($file, $delete = false)
    {
        if (file_exists($file)) {
            if (!$delete) {
                throw new \Exception(sprintf("File '%s' already exists."));
            }
            unlink($file);
        }
    }
}
