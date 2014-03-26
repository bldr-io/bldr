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

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Yaml;
use Zend\Json\Json;

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

        $data = file_get_contents($file);

        // Yaml
        if ($type === 'yml') {
            return new static(Yaml::parse($data));
        }

        // Json
        return new static(Json::decode($data, Json::TYPE_ARRAY));
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

        $content = '';

        // Yaml
        if ($type === 'yml') {
            $content = Yaml::dump($data, 8);
        }

        // Json
        if ($type === 'json') {
            $content = Json::prettyPrint(Json::encode($data));
        }

        file_put_contents($file, $content);
        return new static($data);
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
