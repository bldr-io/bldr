Usage
^^^^^


To start, you are going to want to generate a ``.bldr.yml`` file for your project. This can be kick-started with a
simple console command:

*All commands assume you installed Bldr globally*

.. code-block:: shell

    bldr init

This will interactively guide you through creating a ``.bldr.yml`` file, but here's the gist of it:

1. Basic Info
*************
You will be asked to enter a name for the project, and a short description of the project.

2. Profiles
***********
After filling out the basic info, you will be asked if you want to define your profiles. In Bldr, profiles are the main
containers that get ran, ``bldr build --profile travis`` for example

It will ask you for a profile name and description. e.g. ``travis`` and ``Profile for running on Travis-CI``.
After those, it will ask you for a list of tasks to run. Right now, you are just defining the names of the tasks.
You will manually define whats in these tasks later.

3. Manual Configuration
***********************
When you are done, just enter through the defaults, and it will build the yaml, and show you what it looks like.


------------------------

From here, you will have to manually define what your tasks do!
