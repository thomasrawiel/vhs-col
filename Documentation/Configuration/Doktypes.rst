..  include:: /Includes.rst.txt

..  _doktypes:

============
Registering Page types
============

https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/PageTypes/CreateNewPageType.html

EXT:vhs_col provides an "api" function to easily register new page types (doktype), register them in the Page drag area, add columnsOverrides etc.

Just call the function `\TRAW\VhsCol\Configuration\Doktypes::registerDoktypes()` in your extension's `Configuration/Overrides/pages.php` file

The function expects an array with a configuration array for each page type you want to register and a label for your page type select item group (optional).

.. contents::

.. _minimal:

Minimal configuration
================

..  code-block:: php

     \TRAW\VhsCol\Configuration\Doktypes::registerDoktypes([
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

Configuration options
================

.. confval:: label
   :type: string
   :required: true

   The label of your page type

.. confval:: value
   :type: string|int
   :required: true

   The database value for your page type

   .. note::
       The value must be a number that can be interpreted as an integer

.. confval:: iconIdentifier
   :type: string

   The icon that should be displayed for this page type.

   An exception will be raised if the icon is not registered, e.g. in your `Configuration/Icons.php`

   .. hint::
       Check https://typo3.github.io/TYPO3.Icons/ if you want to use icons, that are already registered in TYPO3 core (e.g. `actions-accessibility <https://typo3.github.io/TYPO3.Icons/icons/actions/actions-accessibility.html>`_)

   `TYPO3 Docs - How to register an icon <https://docs.typo3.org/permalink/t3coreapi:icon-registration>`_
.. confval:: iconIdentifierHide
   :type: string

   The icon that should be displayed if the page is hidden in the navigation. This is optional in most cases


.. confval:: iconIdentifierRoot
   :type: string

   The icon that should be displayed if the page a root page. This is optional in most cases
.. confval:: iconIdentifierContentFromPid
   :type: string

   The icon that should be displayed if the page contains content from another page (contentFromPid). This is optional in most cases

.. confval:: group
   :type: string
   :Default: default

   The select item group of your page type

.. confval:: itemType
   :type: int
   :Default: \\TYPO3\\CMS\\Core\\Domain\\Repository\\PageRepository::DOKTYPE_DEFAULT

   The TCA type of the page.

   Typically you will just want the default type, but in case you really want to change it, refer to the types in PageRepository

.. confval:: columnsOverrides
   :type: array

    Changed or added ['columns'] field display definitions

.. confval:: additionalShowitem
   :type: string

    Additional Configuration of the displayed order of fields in FormEngine and their tab alignment.

   When you register a page type, the showitem value will be taken from the original type.

   Here you can specify fields, that are appended to that definition (e.g. in the Extended tab).

.. confval:: registerInDragArea
   :type: boolean

   If true, the extension will create the necessary User TSConfig to add the page type into the drag area above the page tree.page

.. confval:: allowedTables

