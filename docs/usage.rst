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
            default:
                description: Gets ran when `./bldr.phar build` has no `-p` defined # (Not Required)
                tasks:
                    - default
        tasks:
            default:
                description: Default task # (Not Required)
                calls:
                    -
                        type: exec
                        executable: echo
                        arguments: [Hello World]

To view a list of avaible call types, run:

.. code-block:: shell

    ./bldr.phar task:list

And to get more information on a particular type, run:

.. code-block:: shell

    ./bldr.phar task:info <task name>
