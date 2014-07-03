Bldr
=======

Simplified Build System/Task Runner

Uses Yaml, JSON, XML, PHP, or INI for configs

### Quick Usage

Plug this ```dev``` script and chmod +x it and run ./dev to start your favourite profile (local in this case):

```sh
#!/usr/bin/env bash

bldr=$(which bldr)

if [ -x "$bldr" ] ; then
    $bldr build -p local
else
    if [ ! -f ./bldr.phar ]; then
        curl -sS http://bldr.io/installer | php
    fi

    ./bldr.phar build -p local
fi
```

### Documentation

Documentation is over at [http://docs.bldr.io/en/latest/](http://docs.bldr.io/en/latest/)


### Badges

Tests are broken right now...

* Travis: [![Build Status](https://travis-ci.org/bldr-io/bldr.svg?branch=2.0.1)](https://travis-ci.org/bldr-io/bldr) - Until codeship supports PR's

* Codeship: [ ![Codeship Status for bldr-io/bldr](https://www.codeship.io/projects/30881770-9cb0-0131-2557-1a4ad598520c/status?branch=master)](https://www.codeship.io/projects/17812) 

* Scruitinizer: [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bldr-io/bldr/badges/quality-score.png?s=fc2f6d8f68605e041a0cbf9965fe42bb42484ca4)](https://scrutinizer-ci.com/g/bldr-io/bldr/)

* [The Waffle Board](https://waffle.io/bldr-io/bldr)
