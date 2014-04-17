Extensions
^^^^^^^^^^

When starting Bldr, I wanted it to be highly extensible, and because of that, I decided to use the Symfony2
Dependency Injection component, but Bldr takes it a step further. Its mostly just a rename with a little added functionality
for ease of use.

To add your own task types to Bldr, you will have to write your own `Bldr Block`. By default, there are a couple
blocks that already come with Bldr, but it is easy enough to add your own. For a quick baseline on how to write one,
check out the `Execute Block`_ code. When you want to use a third party block, it is as simple as adding it to
your ``.bldr.yml`` file:

.. code-block:: yaml

    blocks:
        Acme\Block\TestBlock: ~
        Acme\Block\DemoBlock:
            argument: value


------------------------

I'll try and keep an up to date list of core blocks here. If i'm missing one, create a pull request on the docs, or email
me and I'll add it to the list. We are currently working on a site to find more blocks.


`Execute Block`_ (Official)
***************************
The Execute Block (Included with Bldr)

This extension lets you run ``exec`` and ``apply`` tasks.

.. code-block:: yaml

    tasks:
        sample:
            calls:
                -
                    task: exec
                    executable: php
                    arguments: [bin/phpcs]
                -
                    task: apply
                    executable: php
                    output: /dev/null
                    src:
                        - { path: [src, tests], files: *.php, recursive: true } # Checks src and tests directories for *.php files recursively
                    arguments: [-l]

`Filesystem Block`_ (Official)
******************************
The Filesystem Block (Included with Bldr)

This extension lets you run filesystem commands.

This one needs some work, as not all of the commands are there (mkdir, remove, touch, and dumpFile are).

Some examples:

.. code-block:: yaml

    tasks:
        sample:
            calls:
                -
                    task: filesystem:mkdir
                    files: [testDir]
                -
                    task: filesystem:remove
                    files: [testDir]
                -
                    task: filesystem:touch
                    files: [test.tmp]



`Notify Block`_ (Official)
**************************
The Notify Block (Included with Bldr)

This extension lets you run the ``notify`` commands. It will either print to the screen, or email a message.

To use this:

.. code-block:: yaml

    tasks:
        sample:
            calls:
                -
                    task: notify
                    message: Test Message
                    email: test@gmail.com

When adding this extension, you can specify `smtp` connections:

.. code-block:: yaml

    extensions:
        Bldr\Extension\Notify\DependencyInjection\NotifyExtension:
            smtp:
                host: smtp.google.com
                port: 465
                security: ssl
                username: google
                password: is4wesome

`Watch Block`_ (Official)
*************************
The watch Block (Included with Bldr)

This extension lets you run the ``watch`` commands. It will let you watch the filesystem for changes.

This one needs some work. Right now, you can only have one watch task.

.. code-block:: yaml

    tasks:
        sample:
            calls:
                -
                    task: watch
                    src:
                        - { path: [src, tests], files: *.php, recursive: true } # Checks src and tests directories for *.php files recursively
                        - { path: vendor/, files: [*.php, *.yml], recursive: true } # Checks vendor/ directory for *.php and *.yml files recursively
                    profile: someProfile
        sample2:
            calls:
                -
                    task: watch
                    src:
                        - { path: [src, tests], files: *.php, recursive: true } # Checks src and tests directories for *.php files recursively
                        - { files: *.yml } # Checks current directory, non-recursively
                    task: someTask


`Symfony Block`_ (Official) (Don't Use Yet)
*******************************************
The Symfony Block

This extension lets you run symfony console commands quicker. Needs work... I want to turn the following exec into
``symfony:cache:clear`` with no ``arguments``

This one needs work. It needs to be updated to the current version of Bldr.

.. code-block:: yaml

    cache-clear:
        description: 'Clears the cache'
        calls:
            -
                type: symfony:exec
                arguments:
                    - cache:clear


.. _Execute Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Execute
.. _Filesystem Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Filesystem
.. _Notify Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Notify
.. _Watch Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Watch
.. _Symfony Block: https://www.github.com/bldr-io/bldr-symfony/
