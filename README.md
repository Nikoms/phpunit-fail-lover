[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8470b809-e2e4-4a39-b96e-2001fa92f0b2/mini.png)](https://insight.sensiolabs.com/projects/8470b809-e2e4-4a39-b96e-2001fa92f0b2)
[![Build Status](https://api.travis-ci.org/Nikoms/phpunit-fail-lover.png)](https://api.travis-ci.org/Nikoms/phpunit-fail-lover)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/badges/quality-score.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/)
[![Code Coverage](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/badges/coverage.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/)


# Fail Lover <3

This PHPUnit plugin allows you to re-run only tests that failed :) More info to come. Stay tuned because this lib is under construction.

## Installation

### Composer

Simply add this to your `composer.json` file:
```js
"require": {
    "nikoms/phpunit-fail-lover": "dev-master"
}
```

Then run `php composer.phar install`

## Basic configuration

Here is the basic configuration for the plugin. Add the listener to your phpunit.xml(.dist) file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\FailLoverListener" file="src/Listener/FailLoverListener.php">
            <arguments>
                <string>output/only-one-fail-lover-file.csv</string>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

If the specified file doesn't exist, it will be created. A good practice is to give a path where you already create files, like "code coverage" for example. Be careful that it **won't create any folder** for you.

## Usage

### Full usage

To use this plugin, simply run this command:

`phpunit -d fail-lover=replay -d fail-lover=log`

What does it do?

* The first time that the command is called, it will store the tests that failed into the file specified in the configuration (`output/only-one-fail-lover-file.csv` in our previous example).
* The second time that the command is called, it will only run the tests placed in this file. Then, the file will be replaced by a new one that contains only tests that failed in the second execution. So your file may lose weight if you have corrected some tests.
* Continue to call the command until the very last test passes. At the end, your file will be deleted and you will be able to run the entire suite again.


### Partial usage

To record failing tests:

`phpunit -d fail-lover=log`


To replay failing tests:

`phpunit -d fail-lover=replay`


### Important note about "replay" mode


When you use the *replay* mode, filter options like `--exclude-group`, `--group` and `--filter` will be **erased**. The new filter replace all of them.

## Customize

### Separate logger and replay

You may want to only activate one of the two plugin or use another file for each of them. You can do that by using a specific listener.

#### Log only

To activate the *log* plugin. Add the listener to your phpunit.xml(.dist) file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\LoggerListener" file="vendor/nikoms/phpunit-fail-lover/src/Listener/LoggerListener.php">
            <arguments>
                <object class="Nikoms\FailLover\Storage\FileSystem\Csv\CsvRecorder">
                    <arguments>
                        <string>tests-that-failed-again.csv</string>
                    </arguments>
                </object>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

#### Replay only

To activate the *replay* plugin. Add the listener to your phpunit.xml(.dist) file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\ReplayListener" file="src/Listener/ReplayListener.php">
            <arguments>
                <object class="Nikoms\FailLover\Storage\FileSystem\Csv\CsvReader">
                    <arguments>
                        <string>tests-that-failed-before.csv</string>
                    </arguments>
                </object>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

### File name helper

Sometimes, you may want to generate an alternative file name for each of your test. These are 3 patterns that you can use (only with the *FailLoverListener* for the moment):

#### datetime

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\FailLoverListener" file="src/Listener/FailLoverListener.php">
            <arguments>
                <string>path/to/ouput/folder:datetime</string>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

This will generate the file `path/to/ouput/folder/2014-09-16-225536`.

#### uniqId

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\FailLoverListener" file="src/Listener/FailLoverListener.php">
            <arguments>
                <string>path/to/ouput/folder:uniqId</string>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```

This will generate the file `path/to/ouput/folder/54177f8845685`.

#### last

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\FailLover\Listener\FailLoverListener" file="src/Listener/FailLoverListener.php">
            <arguments>
                <string>path/to/ouput/folder:last</string>
            </arguments>
        </listener>
    </listeners>
</phpunit>
```


This will only re-use the last modified file in the folder `path/to/ouput/folder`. If no file are present, it creates the file `fail-lover.txt`.


## TODO

* Create a interface for the FileNameGenerator
* Give the FileNameGenerator as the second argument of listener
