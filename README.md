moodle-mod_elang
================

[![Build Status](https://travis-ci.org/chdemko/moodle-mod_elang.png?branch=master)](https://travis-ci.org/chdemko/moodle-mod_elang)

See (http://chdemko.github.io/moodle-mod_elang/) [http://chdemko.github.io/moodle-mod_elang/] for description, license, authors and contributors.

Installation
-----------

* Launch `git submodule init`, then `git submodule update`

* Launch `mkdir src/view/lib`

* Download [jquery](http://code.jquery.com/jquery.js) in the `src/view/lib` folder

* Download and unzip [bootstrap](http://twitter.github.io/bootstrap/assets/bootstrap.zip) in the `src/view/lib` folder

* Launch `cd src/view; enyo/tools/deploy.js; cd ../..`

* Place the files of the `src` folder into the `mod/elang` folder of the moodle directory.

* Visit Settings > Site Administration > Notifications, you should find the module's tables successfully created

* Go to Site Administration > Plugins > Activity modules > Manage activities
  and you should find that this elang has been added to the list of
  installed modules.

