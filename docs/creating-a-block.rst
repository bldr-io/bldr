Creating A Block
================

Creating a `Bldr Block` for `Bldr` is fairly similar to creating a Bundle for Symfony. Here's a quick guide:

1. Create a repo
----------------

Try and stay with the naming convention used by the other blocks: <name>-block

2. Initialize composer in the repo
----------------------------------

.. code-block:: shell

    cd your-repo && composer init


3. Add Bldr as a dev dependency
-------------------------------

In your composer.json file, you will want to add `bldr-io/bldr` as a `require-dev` dependency.
Because embedded composer and composer are unstable packages by definition or they do not have a
stable release you will have to add them too into your composer.json as below:

.. code-block:: json

    {
        "require-dev": {
            "bldr-io/bldr":              "~7.0.0",
            "dflydev/embedded-composer": "dev-master@dev",
            "composer/composer":         "dev-master@dev"
        }
    }

4. Create a Block class
-----------------------

All of bldr, and the official extensions follow `PSR-4`_ (as well as all the other applicable PSR's, and most, if not all, of the bylaws).
With that, create your directory structure and your Block class:

.. code-block:: shell

    mkdir src && vim src/AcmeDemoBlock.php

All blocks must extend the `Bldr\DependencyInjection\AbstractBlock`_, so your class, empty, will look something like this:

*src/AcmeDemoBlock.php*

.. code-block:: php

    <?php

    /**
     * License Information
     */

    namespace Acme\Block\Demo;

    use Bldr\DependencyInjection\AbstractBlock;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    /**
     * @author John Doe <john@doh.com>
     */
    class AcmeDemoBlock extends AbstractBlock
    {
        /**
         * {@inheritDoc}
         */
        protected function assemble(array $config, ContainerBuilder $container)
        {
        }
    }

The assemble function is where the magic happens. If you take a look at the AbstractBlock, there are some helper functions
in there to make it easier to add new calls, services, and parameters to the Container.

5. Create your Task
-------------------

As a demo, let's say we want to make a task that will output a random number to the user when running the task.

First, lets create the task. Directory structure doesn't really matter, but the core structure is normally `src/Task/<Name>Task.php`.
Similar to blocks, all tasks should extend `Bldr\Block\Core\Task\AbstractTask`_ and must implement `Bldr\Task\TaskInterface`_.

Lets make the `src/Task` directory, and create the new task:
.. code-block:: shell

    mkdir src/Task && vim src/Task/OutputRandomNumberCall.php

Then, let's build the task class! Extending the AbstractTask, suggests that we implement `configure`_ and requires that we
implement `run`_.

*src/Task/OutputRandomNumberTask.php*

.. code-block:: php

    <?php

    /**
     * License Information
     */

    namespace Acme\Block\Demo\Task;

    use Bldr\Block\Core\Task\AbstractTask;
    use Symfony\Component\Console\Output\OutputInterface;

    /**
     * @author John Doe <john@doh.com>
     */
    class OutputRandomNumberTask extends AbstractTask
    {
        /**
         * {@inheritDoc}
         */
        public function configure()
        {
            $this->setName('acme_demo:output_random_number')
                ->setDescription('This call outputs a random number. If min and max are specified, it will use those as the range')
                ->addParameter('min', true, 'Minimum number in range', 0)
                ->addParameter('max', true, 'Maximum number in range', 100)
            ;
        }

        /**
         * {@inheritDoc}
         */
        public function run(OutputInterface $output)
        {
            $random = rand($this->getParameter('min'), $this->getParameter('max'));
            $output->writeln(['', 'Random Number: '.$random, '']);
        }
    }

Next, we need to add the task to the container, so we can use it in .bldr.yml(.dist) files:

*src/AcmeDemoBlock.php*

.. code-block:: php

    <?php

    /**
     * License Information
     */

    namespace Acme\Block\Demo;

    use Bldr\DependencyInjection\AbstractBlock;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    /**
     * @author John Doe <john@doh.com>
     */
    class AcmeDemoBlock extends AbstractBlock
    {
        /**
         * {@inheritDoc}
         */
        protected function assemble(array $config, ContainerBuilder $container)
        {
            // Here's one of the shortcut methods! This method will return a Symfony DI Definition
            // that is tagged as `bldr`. If you need to, you can easily add arguments to the constructor,
            // or calls to methods.
            $task = $this->addTask('acme_demo.output_random_number', 'Acme\Block\Demo\Task\OutputRandomNumberTask');

            // If you need dependencies, you could do the following:
            // $task->setArgument(0, new Reference('some_service'));
            // or
            // $arguments = array(new Reference('some_service'));
            // $task->addMethodCall('someMethodName', $arguments);

            // If you want to add a service, that isn't a task, you can also use:
            // $this->addService($name, $class);
            // Which will also return a Symfony DI Definition
        }
    }

6. Register block with bldr
---------------------------

In the composer.json file, add the following:

.. code-block:: json

    {
        "extra": {
            "block-class": "Namespace\\To\\Your\\Block\\Class"
        }
    }

With this, you should be able to install it with the `bldr.json` file and add it to a .bldr.yml file:

.. code-block:: yaml

    bldr:
        name: some/name
        profile:
            test:
                jobs:
                    - randomize

        jobs:
            randomize:
                tasks:
                    -
                        type: acme_demo:output_random_number
                        min: 0
                        max: 100000

And run it!

.. code-block:: shell

    ./bldr.phar run test


There's some more advanced stuff, like being able to specify configuration:

*src/AcmeDemoBlock.php*

.. code-block:: php

    <?php

    /**
     * License Information
     */

    namespace Acme\Block\Demo;

    use Bldr\DependencyInjection\AbstractBlock;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    /**
     * @author John Doe <john@doh.com>
     */
    class AcmeDemoBlock extends AbstractBlock
    {
        // ...

        /**
         * {@inheritDoc}
         */
        protected function getConfigurationClass()
        {
            return 'Acme\Block\Demo\Configuration';
        }
    }

7. Advanced Config
------------------

Then make a Configuration.php file. This config is the config from Symfony. You can read their docs for more information.

*src/Configuration.php*

.. code-block:: php

    <?php

    /**
     * License Information
     */

    namespace Acme\Block\Demo;

    use Symfony\Component\Config\Definition\ConfigurationInterface;
    use Symfony\Component\Config\Definition\Builder\TreeBuilder;

    /**
     * @author John Doe <john@doh.com>
     */
    class Configuration implements ConfigurationInterface
    {
        /**
         * {@inheritDoc}
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode    = $treeBuilder->root('acme_demo');

            // here you will build the configuration tree

            return $treeBuilder;
        }
    }

.. _PSR-4: http://www.php-fig.org/psr/psr-4/
.. _BldrDependencyInjectionAbstractBlock: https://github.com/bldr-io/bldr/blob/master/src/DependencyInjection/AbstractBlock.php
.. _BldrBlockCoreTaskAbstractTask:https://github.com/bldr-io/bldr/blob/master/src/Block/Core/Task/AbstractTask.php
.. _BldrTaskTaskInterface:https://github.com/bldr-io/bldr/blob/master/src/Task/TaskInterface.php
.. _configure: https://github.com/bldr-io/bldr/blob/master/src/Call/CallInterface.php#L28
.. _run: https://github.com/bldr-io/bldr/blob/master/src/Call/CallInterface.php#L54
