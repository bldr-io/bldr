Usage
^^^^^


To start, you are going to want to generate a ``.bldr.yml`` file for your project. This (for now) has to be done manually, but
it's pretty simple.

Create a ``.bldr.yml(.dist)`` file:

.. code-block:: yaml

    bldr:
        name: some/name
        description:  A description about the project # (Not Required)
        profiles: # A list of profiles that can be ran with `./bldr.phar run`
            someJob:
                description: Gets ran when `./bldr.phar run someJob` is called
                jobs:
                    - foo
            someOtherJob:
                jobs:
                    - bar
            inheritanceExample:
                description: Will run the tasks from `someJob` and then `someOtherJob`.
                uses:
                    before: [someJob]
                    after: [someOtherJob]
        jobs:
            foo:
                description: Foo job
                tasks:
                    -
                        type: exec
                        executable: echo
                        arguments: [Hello World]
            bar:
                description: Bar job
                tasks:
                    -
                        type: exec
                        executable: sleep
                        arguments: [1]

To view a list of available task types, run:

.. code-block:: shell

    ./bldr.phar task:list

And to get more information on a particular task, run:

.. code-block:: shell

    ./bldr.phar task:info <task name>

To run your profiles: (This has changed since version 7)

.. code-block:: shell

    ./bldr.phar run <profile name>