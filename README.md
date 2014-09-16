[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8470b809-e2e4-4a39-b96e-2001fa92f0b2/mini.png)](https://insight.sensiolabs.com/projects/8470b809-e2e4-4a39-b96e-2001fa92f0b2)
[![Build Status](https://api.travis-ci.org/Nikoms/phpunit-fail-lover.png)](https://api.travis-ci.org/Nikoms/phpunit-fail-lover)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/badges/quality-score.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/)
[![Code Coverage](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/badges/coverage.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-fail-lover/)


# Fail Lover <3

* You have tests that failed?
* You want to run them without running the tons of (selenium?) tests that take hours to execute?
* You don't want to add a special debug `@group` annotation for all of them?

No problem! This plugin allows you to **rerun only tests that failed**!

## Installation

### Composer

Simply add this to your `composer.json` file:
```js
"require": {
    "nikoms/phpunit-fail-lover": "dev-master"
}
```

Then run `php composer.phar install`

## Quick configuration

Here is a configuration for the plugin. Add the listener to your phpunit.xml(.dist) file:

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

### Main usage

To use this plugin, simply run the phpunit command `phpunit`.

What does it do?

* The first time that the command is called, it will store the tests that failed into the file specified in the configuration (`output/only-one-fail-lover-file.csv` in our previous example).
* The second time that the command is called, it will only run the tests placed in this file. Then, the file will be replaced by a new one that contains only tests that failed in the second execution. So your file may lose weight if you have corrected some tests.
* Continue to call the command until the very last test passes. At the end, your file will be deleted and you will be able to run the entire suite again.


## Customize

### Temporary disable listener

You can temporary disable the *log* or *replay* plugin by simply add argumentx to the `phpunit` command:

* To disable *log*, use `-d fail-lover=log-disabled`
* To disable *replay*, use `-d fail-lover=replay-disabled`

Of course you can disable both:
`phpunit -d fail-lover=log-disabled -d fail-lover=replay-disabled`


**Important note about "replay" mode: ** When the *replay* mode is used, filter options like `--exclude-group`, `--group` and `--filter` will be **removed**.

### File name helper

Sometimes, you may want to generate an alternative/dynamic file name. These are 3 patterns that you can use:

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


This will only re-use the last modified file in the folder `path/to/ouput/folder`. If the folder is empty, it creates the file `fail-lover.txt`.



### Separate logger and replay

You can use a different file for the *log* and the *replay* by using separated listeners. In fact, the basic `FailLoverListener` seen above is just a shortcut to these two listeners.

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


## TODO

* Create a interface for the FileNameGenerator. Maybe just give the path in the constructor and have a __toString method? It should be easier to use in phpunit.xml
* Give the possibility to use file name generator in specific listeners
