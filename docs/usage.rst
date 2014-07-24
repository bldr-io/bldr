Usage
^^^^^


To start, you are going to want to generate a ``.bldr.yml`` file for your project. This (for now) has to be done manually, but
it's pretty simple.

Create a ``.bldr.yml(.dist)`` file:

.. code-block:: yaml

    bldr:
        name: some/name
        description:  A description about the project # (Not Required)
        profiles: # A list of profiles that can be ran with `./bldr.phar build`
            someTask:
                description: Gets ran when `./bldr.phar build someTask` is called
                tasks:
                    - foo
            someOtherTask:
                tasks:
                    - bar
            inheritanceExample:
                description: Will run the tasks from `someTask` and then `someOtherTask`.
                uses:
                    before: [someTask]
                    after: [someOtherTask]
        tasks:
            foo:
                description: Foo task
                calls:
                    -
                        type: exec
                        executable: echo
                        arguments: [Hello World]
            bar:
                description: Bar task
                calls:
                    -
                        type: exec
                        executable: sleep
                        arguments: [1]

To view a list of available call types, run:

.. code-block:: shell

    ./bldr.phar task:list

And to get more information on a particular type, run:

.. code-block:: shell

    ./bldr.phar task:info <task name>

To run your profiles: (This has changed since version 4)

.. code-block:: shell

    ./bldr.phar build <profile name>