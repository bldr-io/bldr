Installation
^^^^^^^^^^^^

This should be installed via composer, for now. You can either install it globally, or in your own project.
If it is the first time you globally install a dependency then make sure you include ``~/.composer/vendor/bin``
in ``$PATH`` as shown here_.

**Global Setup**

.. code-block:: shell

    composer global require bldr-io/bldr dev-master

**Project Setup**

.. code-block:: shell

    composer require bldr-io/bldr dev-master


And that's it! From here, you should be able to run ``bldr`` if you set it up globally, or ``./bin/bldr`` if you set
it up in your project.


.. _here: http://getcomposer.org/doc/03-cli.md#global
