..  include:: /Includes.rst.txt

..  _ctypes:

============
Conditional TCA Options
============

EXT:vhs_col provides an "api" function to easily register new content element types (cTypes), register them in the New Content Element wizard, add a flexform, add columnsOverrides etc.

Just call the function `\TRAW\VhsCol\Configuration\CTypes::registerCTypes()` in your extension's `Configuration/Overrides/tt_content.php` file

The function expects an array with a configuration array for each cType you want to register and a label for your cTypes select item group (optional).


..  _minimal:

Minimal configuration
================

..  code-block:: php
     \TRAW\VhsCol\Configuration\CTypes::registerCTypes([
        [
            'label' => 'My content element type',
            'value' => 'my_content_element'
        ],
     ], 'My cTypes group');

At least a label and a value are required for a content element type.

However, it is recommended to add more options, for example an icon or `showItem` to control the backend forms.

.. _advanced:

Advanced example
================

