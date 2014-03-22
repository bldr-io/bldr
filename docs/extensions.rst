Extensions
^^^^^^^^^^

When starting Bldr, I wanted it to be highly extensible, and because of that, I decided to use the Symfony2
Dependency Injection component. By default, there are a couple extensions that already come with Bldr, but it is easy
enough to add your own. For a quick baseline on how to write one, check out the bldr-io/bldr-execute_ repository. When
you want to use a third party extension, its as simple as adding it to your ``.bldr.yml`` file:

.. code-block:: yaml

    extensions:
        - Acme\Extension\DependencyInjection\DemoExtension


------------------------

I'll try and keep an up to date list of extensions here. If i'm missing one, create a pull request on the docs, or email
me and I'll add it to the list.


bldr-io/bldr-execute_ (Official)
********************************
The Execute Extension (Included with Bldr)

This extension lets you run ``exec`` and ``apply`` tasks.

bldr-io/bldr-filesystem_ (Official)
***********************************
The Filesystem Extension (Included with Bldr)

This extension lets you run ``filesystem:mkdir`` and ``filesystem:remove`` commands.
Other commands will be listed in the docs of the project.


bldr-io/bldr-symfony_ (Official)
********************************
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
