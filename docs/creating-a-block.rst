Creating A Block
================

Creating a `Bldr Block` for `Bldr` is fairly similar to creating a Bundle for Symfony2. Here's a quick guide:

1. Create a repo
----------------

Try and stay with the naming convention used by the other blocks: <name>-block

2. Initialize composer in the repo
----------------------------------

.. code-block:: shell

    cd your-repo && composer init


3. Add Bldr as a dev dependency
-------------------------------

In your composer.json file, you will want to add `bldr-io/bldr` as a `require-dev` dependency. For right now,
until i can figure out why, you will need to add composer, and embedded composer as well.

.. code-block:: json

    {
        "require-dev": {
            "bldr-io/bldr":              "~4.1",
            "dflydev/embedded-composer": "dev-master",
            "composer/composer":         "dev-master"
        }
    }

4. Create a Block class
-----------------------

All of bldr, and the official extensions follow `PSR-4`_ (as well as all the other PSR's, and most, if not all, of the bylaws).
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

5. Create your Call
-------------------

As a demo, let's say we want to make a call that will output a random number to the user when running the call.

First, lets create the call. Directory structure doesn't really matter, but the core structure is normally `src/Call/<Name>Call.php`.
Similar to blocks, all calls must extend `Bldr\Call\AbstractCall`_.

Lets make the `src/Call` directory, and create the new Call:
.. code-block:: shell

    mkdir src/Call && vim src/Call/OutputRandomNumberCall.php

Then, let's build the call class! Extending the AbstractCall, requires that we implement two methods: `configure`_ and `run`_

*src/Call/OutputRandomNumberCall.php*

.. code-block:: php

    <?php

    /**
     * License Information
     */

    namespace Acme\Block\Demo\Call;

    use Bldr\Call\AbstractCall;

    /**
     * @author John Doe <john@doh.com>
     */
    class OutputRandomNumberCall extends AbstractCall
    {
        /**
         * {@inheritDoc}
         */
        public function configure()
        {
            $this->setName('acme_demo:output_random_number')
                ->setDescription('This call outputs a random number. If min and max are specified, it will use those as the range')
                ->addOption('min', true, 'Minimum number in range', 0)
                ->addOption('max', true, 'Maximum number in range', 100);
        }


        /**
         * {@inheritDoc}
         */
        public function run()
        {
            $random = rand($this->getOption('min'), $this->getOption('max'));
            $this->output->writeln(["", "Random Number: " . $random, ""]);

            return true;
        }
    }

Next, we need to add the call to the container, so we can use it in .bldr.yml files:

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
            $call = $this->addCall('acme_demo.output_random_number', 'Acme\Block\Demo\AcmeDemoBlock');

            // If you need dependencies, you could do the following:
            // $call->setArgument(0, new Reference('some_service'));
            // or
            // $call->addMethodCall('someMethodName', array $arguments);

            // If you want to add a service, that isn't a call, you can also use:
            // $this->addService($name, $class);
            // Which will also return a Symfony DI Definition
        }
    }


With this, you should be able to add it to a .bldr.yml file:

.. code-block:: yaml

    blocks:
        - Acme\Block\Demo\AcmeDemoBlock

    bldr:
        name: some/name
        profile:
            default:
                tasks:
                    - default

        tasks:
            default:
                calls:
                    -
                        type: acme_demo:output_random_number
                        min: 0
                        max: 100000

And run it!

.. code-block:: shell

    ./bldr.phar build -p default


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

6. Advanced Config
------------------

Then make a Configuration.php file. This config is the config from symfony. You can read their docs for more information

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

            return $treeBuilder;
        }
    }

.. _PSR-4: http://www.php-fig.org/psr/psr-4/
.. _BldrDependencyInjectionAbstractBlock: https://github.com/bldr-io/bldr/blob/master/src/DependencyInjection/AbstractBlock.php
.. _BldrCallAbstractCall:https://github.com/bldr-io/bldr/blob/master/src/Call/AbstractCall.php
.. _configure: https://github.com/bldr-io/bldr/blob/master/src/Call/CallInterface.php#L28
.. _run: https://github.com/bldr-io/bldr/blob/master/src/Call/CallInterface.php#L54