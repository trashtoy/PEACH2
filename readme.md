PEACH2
======

PHP Extension leading your ACHIEVEMENT.


Features
--------

### Util
Object-oriented array manipulation modules like
[Java Collections Framework](http://docs.oracle.com/javase/8/docs/technotes/guides/collections/).

- Map interface like java.util.HashMap. You can use objects as key.
- Sorting arrays which contain objects.
- Some other utility classes.

### DT
Object-oriented datetime management API.

- Datetime objects consisting of various scopes. (DATE, DATETIME and TIMESTAMP)
- Easy to sort and compare.
- Library which is designed by immutable classes.
- Loosely-coupled API between datetime manipulation and format/parse.

### Markup
This module helps you to markup HTML or XML dynamically.

- DOM-like usability.
- Various output customization.
- Helper class enables more simple coding.

### DF
Data format encoding / decoding API.
All the classes of this module implement interface Codec.

- Utf8Codec: dealing with the interconversion of unicode codepoints and UTF-8 string
    - example: `'süß'` (byte sequence: 73 C3 BC C3 9F) => decode => `array(0x73, 0xFC, 0xDF)` => encode => `'süß'`
- JsonCodec: alternative of [json_encode](http://php.net/manual/function.json-encode.php) and [json_decode](http://php.net/manual/function.json-decode.php)
- Base64Codec: wrapping [base64_encode](http://php.net/manual/function.base64-encode.php) and [base64_decode](http://php.net/manual/function.base64-decode.php)
- SerializationCodec: wrapping [serialize](http://php.net/manual/function.serialize.php) and [unserialize](http://php.net/manual/function.unserialize.php)
- CodecChain: concatenating multiple Codec instances

### Http
HTTP message handling module with minimal side effects.
By implementing the interface Endpoint as a mock, you can check the behavior of the web application with a simple unit test.

Requirements
------------

- PHP 5.3.0 or later

That's all.


How to use
----------

### Composer
You can install using composer by the following command:  
`composer require trashtoy/peach2`

### Autoloading
Require autoload.php  
`require_once("/path/to/PEACH2/autoload.php");`  
or set up autoload manually.

Documentation
-------------

[Online documentation](http://trashtoy.github.io/PEACH2/) is available.

Roadmap
-------

Package | Description
--------|------------
RB      | Object-oriented i18n module. (RB represents ResourceBundle.)
DB      | A reinvented O/R mapper.
App     | Various components about web application development. (Forms, validations, etc.)
