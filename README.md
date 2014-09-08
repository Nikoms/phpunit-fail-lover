[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8470b809-e2e4-4a39-b96e-2001fa92f0b2/mini.png)](https://insight.sensiolabs.com/projects/8470b809-e2e4-4a39-b96e-2001fa92f0b2)
[![Build Status](https://api.travis-ci.org/Nikoms/phpunit-fail-lover.png)](https://api.travis-ci.org/Nikoms/phpunit-fail-lover)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/badges/quality-score.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/)
[![Code Coverage](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/badges/coverage.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/)


Fail Lover <3
==============

This PHPUnit plugin allows you to re-run only tests that failed :) More info to come. Stay tuned because this lib is under construction.

Installation
--------------

### Composer ###
Simply add this to your `composer.json` file:
```js
"require": {
    "nikoms/phpunit-fail-lover": "dev-master"
}
```

Then run `php composer.phar install`

PhpUnit configuration
---------------------
To activate the *log* plugin. Add the listener to your phpunit.xml(.dist) file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\LoggerListener" file="vendor/nikoms/phpunit-fail-lover/src/Listener/LoggerListener.php">
            <arguments>
                <object class="Nikoms\FailLover\FileSystem\Csv\CsvRecorder">
                    <arguments>
                        <string>tests-that-failed-again.csv</string>
                    </arguments>
                </object>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

* If the file `failed-tests.csv` doesn't exist, it will be created. A good practice is to give a path where you already create files, like code coverage. Be careful that it won't create any folder for you.
* If the file already exists, it is emptied before all tests start.


To activate the *replay* plugin. Add the listener to your phpunit.xml(.dist) file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\ReplayListener" file="src/Listener/ReplayListener.php">
            <arguments>
                <object class="Nikoms\FailLover\FileSystem\Csv\CsvReader">
                    <arguments>
                        <string>tests-that-failed-before.csv</string>
                    </arguments>
                </object>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

If the specified file doesn't exist, then none of the tests will be executed.


Usage
-----

To record failing tests:

`phpunit -d fail-lover=log`


To replay failing tests:

`phpunit -d fail-lover=replay`

/!\ The `--exclude-group` option of phpunit won't be executed if you use replay.

Of course, you can combine the two:

`phpunit -d fail-lover=replay -d fail-lover=log`

This will replay only supposed tests that failed AND log new tests that failed.

Customize
---------

Yes, you will be able to do that!


TODO
----

* Replay: Find why the --exclude-group doesn't work with "replay"
* Both: Simplify the use the listener by giving only a file in the phpunit.xml file. Or by managing some new parameters in the command line?
* Both: Give the possibility to give vars in the name of the file (like the date, now, LAST_ERRORS_IN:path, etc...)
