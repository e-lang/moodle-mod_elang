moodle-mod_elang
================

[![Build Status](https://travis-ci.org/e-lang/moodle-mod_elang.png?branch=master)](https://travis-ci.org/e-lang/moodle-mod_elang)

See [http://e-lang.github.io/moodle-mod_elang](http://e-lang.github.io/moodle-mod_elang) for description, license, authors and contributors.

Build
-----

Make sure you have install *composer* and *bower* on your system.

* Run

    ~~~sh
    composer --stability="dev" create-project e-lang/moodle-mod_elang
    cd moodle-mod_elang
    ~~~
  for getting the project
* Run

    ~~~sh
    composer package
    ~~~
  for creating creating zip files ready to be unzipped in the `mod` folder of the moodle directory

