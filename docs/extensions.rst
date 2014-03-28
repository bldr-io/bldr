Extensions
^^^^^^^^^^

When starting Bldr, I wanted it to be highly extensible, and because of that, I decided to use the Symfony2
Dependency Injection component. By default, there are a couple extensions that already come with Bldr, but it is easy
enough to add your own. For a quick baseline on how to write one, check out the `Execute Extension`_ code. When
you want to use a third party extension, its as simple as adding it to your ``.bldr.yml`` file:

.. code-block:: yaml

    extensions:
        Bldr\Extension\Symfony\DependencyInjection\SymfonyExtension: ~
        Acme\Extension\DependencyInjection\DemoExtension:
            argument: value


------------------------

I'll try and keep an up to date list of extensions here. If i'm missing one, create a pull request on the docs, or email
me and I'll add it to the list.


`Execute Extension`_ (Official)
*******************************
The Execute Extension (Included with Bldr)

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
                    executeable: php
                    fileset: [src/*.php, src/*/*.php]
                    arguments: [-l]

`Filesystem Extension`_ (Official)
**********************************
The Filesystem Extension (Included with Bldr)

This extension lets you run filesystem commands.

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



`Notify Extension`_ (Official)
******************************
The Notify Extension (Included with Bldr)

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

`Watch Extension`_ (Official)
*****************************
The watch Extension (Included with Bldr)

This extension lets you run the ``watch`` commands. It will let you watch the filesystem for changes.

.. code-block:: yaml

    tasks:
        sample:
            calls:
                -
                    task: watch
                    files: [src/*.php, src/**/*.php]
                    profile: someProfile
        sample2:
            calls:
                -
                    task: watch
                    files: [src/*.php, src/**/*.php]
                    task: someTask


`Symfony Extension`_ (Official)
*******************************
The Symfony Extension

This extension lets you run symfony console commands quicker. Needs work... I want to turn the following exec into
``symfony:cache:clear`` with no ``arguments``

.. code-block:: yaml

    cache-clear:
        description: 'Clears the cache'
        calls:
            -
                type: symfony:exec
                arguments:
                    - cache:clear


.. _Execute Extension: https://github.com/bldr-io/bldr/tree/master/src/Extension
.. _Filesystem Extension: https://github.com/bldr-io/bldr/tree/master/src/Filesystem
.. _Notify Extension: https://github.com/bldr-io/bldr/tree/master/src/Notify
.. _Watch Extension: https://github.com/bldr-io/bldr/tree/master/src/Watch
.. _Symfony Extension: https://www.github.com/bldr-io/bldr-symfony/
