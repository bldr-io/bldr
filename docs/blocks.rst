Extensions
^^^^^^^^^^

When starting Bldr, I wanted it to be highly extensible, and because of that, I decided to use the Symfony2
Dependency Injection component, but Bldr takes it a step further. Its mostly just a rename with a little added functionality
for ease of use.

With that, we've come up with the idea for `Bldr Blocks`. `Bldr Blocks` are basically Symfony Extensions (with a little difference),
that add some functionality to bldr. There are a bunch of `Bldr Blocks` already in the core of `bldr`, and they are listed below.
To find other blocks, we are working on a site where people will be able to upload links to their repositories. But in the mean time,
just use the list below. If I missed anything, you can find it here: https://www.versioneye.com/php/bldr-io:bldr/references

* `Frontend Block`_ - Used for tasks like CSS/JS Minification and Less/Sass/SCSS/Coffeescript compilation
* `Gush Block`_ - Used for integrating with `Gush`_
* [Out Of Date] `Symfony Block`_ - Used for integrating with the Symfony2 Framework
* [Out Of Date] `Git Block`_ - Used for integrating with git


To add your own task types to Bldr, you will have to write your own `Bldr Block`. By default, there are a couple
blocks that already come with Bldr, but it is easy enough to add your own. For a quick baseline on how to write one,
check out the `documentation <creating-a-block.html>`_.

Adding third party blocks is a three step process. First, you need to create a `bldr.json` file (if you don't have one):

.. code-block:: json

    {
        "require": {
            "acme/demo-block": "@stable"
        }
    }

Then, install/update your bldr dependencies:

.. code-block:: shell

    ./bldr.phar install
    # OR
    ./bldr.phar update

Then, add it to your ``.bldr.yml`` file:

.. code-block:: yaml

    bldr: ~

    blocks:
        - Acme\Block\Demo\AcmeDemoBlock

    # If you have configs
    acme_demo:
        some_setting: some_value


------------------------

Below is some minor documentation on the core blocks.

`Execute Block`_ (Official)
^^^^^^^^^^^^^^^^^^^^^^^^^^^
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
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
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
^^^^^^^^^^^^^^^^^^^^^^^^^^
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

    blocks:
        - Bldr\Block\Notify\NotifyBlock

    notify:
        smtp:
            host: smtp.google.com
            port: 465
            security: ssl
            username: google
            password: is4wesome

`Watch Block`_ (Official)
^^^^^^^^^^^^^^^^^^^^^^^^^
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


.. _Frontend Block: https://github.com/bldr-io/frontend-block
.. _Gush Block: https://github.com/bldr-io/gush-block
.. _Symfony Block: https://www.github.com/bldr-io/bldr-symfony/
.. _Git Block: https://github.com/bldr-io/bldr-git

.. _Execute Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Execute
.. _Filesystem Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Filesystem
.. _Notify Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Notify
.. _Watch Block: https://github.com/bldr-io/bldr/tree/master/src/Block/Watch

.. _Gush: http://github.com/gushphp/gush


Content
=======

.. toctree::
    :maxdepth: 4

    creating-a-block