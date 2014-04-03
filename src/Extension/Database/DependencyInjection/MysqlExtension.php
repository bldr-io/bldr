<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Database\DependencyInjection;

use Bldr\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class MysqlExtension extends AbstractExtension
{
	/**
	 * Loads a specific configuration.
	 *
	 * @param array            $config    An array of configuration values
	 * @param ContainerBuilder $container A ContainerBuilder instance
	 *
	 * @throws \InvalidArgumentException When provided tag is not defined in this extension
	 *
	 * @api
	 */
	public function load( array $config, ContainerBuilder $container )
	{
		$container->setDefinition(
			'bldr_database.mysql.user',
			new Definition('Bldr\Extension\Database\Service\Mysql\CreateUserService')
		)
			->addTag('bldr');
	}

} 
