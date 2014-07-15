Installation
^^^^^^^^^^^^

This should be installed via composer, for now. You can either install it globally, or in your own project.
If it is the first time you globally install a dependency then make sure you include ``~/.composer/vendor/bin``
in ``$PATH`` as shown here_.

**Global Setup**

.. code-block:: shell

    $ composer global require bldr-io/bldr=~4.2.0 dflydev/embedded-composer=dev-master@dev composer/composer=dev-master@dev

    # Or

    $ curl -sS http://bldr.io/installer | php
    $ mv bldr.phar /usr/bin/bldr

    # You can also install specific versions (Anything greater or equal to 5.0.0)
    # Example: 5.0.0, 5.0.1, stable (latest tag in github), dev (last commit in master)
    $ curl -sS http://bldr.io/installer | php -- --version=dev # Stable by default


**Project Setup**

It is suggested that you use the phar, as you can get conflicts with dependencies by including it in your project or globally!

.. code-block:: shell

    $ curl -sS http://bldr.io/installer | php

    # Or

    $ composer require bldr-io/bldr "~4.2.0"


And that's it! From here, you should be able to run ``bldr`` if you set it up globally, or ``./bin/bldr`` or ``php ./vendor/bin/bldr`` if you set
it up in your project.


.. _here: http://getcomposer.org/doc/03-cli.md#global
