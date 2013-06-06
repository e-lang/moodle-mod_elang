moodle-mod_elang
================

[![Build Status](https://travis-ci.org/chdemko/moodle-mod_elang.png?branch=master)](https://travis-ci.org/chdemko/moodle-mod_elang)

Description
-----------
A moodle module for learning language published under the [CeCILL-B licence](http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license)

* Launch `git submodule init`, then `git submodule update`

* Launch `mkdir src/view/lib`

* Download [jquery](code.jquery.com/jquery.js) in `src/view/lib` folder

* Download and unzip [bootstrap](http://twitter.github.io/bootstrap/assets/bootstrap.zip) in `src/view/lib` folder

* Launch `cd src/view; tools/deploy.sh; cd ../..`

* Place the `src` folder into the `mod/elang` folder of the moodle directory.

* Visit Settings > Site Administration > Notifications, you should find the module's tables successfully created

* Go to Site Administration > Plugins > Activity modules > Manage activities
  and you should find that this elang has been added to the list of
  installed modules.

Licenses
--------

* Icon designed by Benjamin D. Esham (Public domain), via [Wikimedia Commons] (http://commons.wikimedia.org/wiki/File:Chat_bubbles.svg)
