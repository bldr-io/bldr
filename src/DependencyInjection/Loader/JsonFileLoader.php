3<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Zend\Json\Json;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class JsonFileLoader extends YamlFileLoader
{
    private $jsonParser;

    /**
     * Loads the Json File
     *
     * @param string $file
     *
     * @throws \Zend\Json\Exception\RuntimeException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @return array
     */
    protected function loadFile($file)
    {

        if (!stream_is_local($file)) {
            throw new InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
        }

        if (!file_exists($file)) {
            throw new InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        if (null === $this->jsonParser) {
            $this->jsonParser = new Json();
        }

        return $this->validate($this->jsonParser->decode(file_get_contents($file), true), $file);
    }

    /**
     * Validates a JSON file.
     *
     * @param mixed  $content
     * @param string $file
     *
     * @return array
     *
     * @throws InvalidArgumentException When service file is not valid
     */
    private function validate($content, $file)
    {

        if (null === $content) {
            return $content;
        }

        if (!is_array($content)) {
            throw new InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        foreach (array_keys($content) as $namespace) {
            if (in_array($namespace, array('imports', 'parameters', 'services'))) {
                continue;
            }

            if (!$this->container->hasExtension($namespace)) {
                $extensionNamespaces = array_filter(
                    array_map(
                        function ($ext) {
                            return $ext->getAlias();
                        },
                        $this->container->getExtensions()
                    )
                );
                throw new InvalidArgumentException(
                    sprintf(
                        'There is no extension able to load the configuration for "%s" (in %s). ' .
                        'Looked for namespace "%s", found %s',
                        $namespace,
                        $file,
                        $namespace,
                        $extensionNamespaces ? sprintf('"%s"', implode('", "', $extensionNamespaces)) : 'none'
                    )
                );
            }
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        if (is_string($resource) && 'dist' === pathinfo($resource, PATHINFO_EXTENSION)) {
            return $this->supports(str_replace('.dist', '', $resource), $type);
        }

        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
