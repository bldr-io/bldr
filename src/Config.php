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
    public static $NAME = '.bldr';

    /**
     * @var array $TYPES
     */
    public static $TYPES = ['yml' => true, 'json' => true];

    public static $DEFAULT_TYPE = 'yml';

    /**
     * @throws \Exception
     * @internal param string $file
     * @return Config
     */
    public static function factory()
    {
        list($file, $type) = static::getFile();
        if (!array_key_exists($type, static::$TYPES)) {
            throw new \Exception(sprintf("%s is not a valid extension. Feel free to make a PR", $type));
        }

        switch ($type) {
            case 'yml':
                $data = file_get_contents($file);

                return new static(Yaml::parse($data));
            case 'json':
                $json = new JsonFile($file);

                return new static($json->read());
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function getFile()
    {
        $tried = [];
        foreach (static::$TYPES as $type => $allowed) {
            if (!$allowed) {
                continue;
            }

            $file = static::$NAME . '.' . $type;

            if (file_exists($file)) {
                return [$file, $type];
            } else {
                $tried[] = $file;
                $file .= ".dist";
                if (file_exists($file)) {
                    return [$file, $type];
                } else {
                    $tried[] = $file;
                }
            }
        }

        throw new \Exception("Couldn't find a config file. Tried: " . implode(', ', $tried));
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
        if (!array_key_exists($type, static::$TYPES)) {
            throw new \Exception(sprintf("%s is not a valid extension. Feel free to make a PR", $type));
        }

        $file = static::$NAME . '.' . $type;
        if ($dist) {
            $file .= '.dist';
        }

        if (file_exists($file) && !$delete) {
            throw new \Exception(sprintf("File '%s' already exists."));
        } elseif (file_exists($file) && $delete) {
            unlink($file);
        }

        switch ($type) {
            case 'yml':
                $yaml = Yaml::dump($data, 8);
                file_put_contents($file, $yaml);

                return new static($data);
            case 'json':
                $json = new JsonFile($file);
                $json->write($data);

                return new static($json->read());
        }
    }
}
