..  include:: /Includes.rst.txt

..  _doktypes:

============
Register page types
============


This extension provides an "api" function to easily register new page types (doktype), register them in the Page drag area, add columnsOverrides etc.

Just call the function `\TRAW\VhsCol\Configuration\Doktypes::registerDoktypes()` in your extension's `Configuration/Overrides/pages.php` file

The function expects an array with a configuration array for each page type you want to register and a label for your page type select item group (optional).

Also see `TYPO3 Docs - Create new Page Type <https://docs.typo3.org/permalink/t3coreapi:page-types-example>`_

.. contents::

.. _minimal:

Minimal configuration
================

..  code-block:: php

     \TRAW\VhsCol\Configuration\Doktypes::registerDoktypes([
        [
            'label' => 'My page type',
            'value' => '123'
        ],
     ], 'My cTypes group');

At least a label and a value are required for a content element type.

However, it is recommended to add more options, for example an icon or `showItem` to control the backend forms.

.. _advanced:

Advanced example
================

..  code-block:: php

     \TRAW\VhsCol\Configuration\Doktypes::registerDoktypes([
        [
            'label' => 'My event page type',
            'value' => '123'
            'icon' => 'actions-calendar',
            'group' => 'my-ext-doktype-group',
            'columnsOverrides' => [
                'media' => [
                    'config' => [
                        'maxitems' => 1,
                        'allowed' => 'jpg,png',
                    ]
                ]
            ],
            'additionalShowitem' => '--palette--;;customPalette',
            'registerInDragArea' => true,
            'allowedTables' => '*',
        ],
     ], 'My cTypes group');

Configuration options
================

.. confval:: label
   :type: string
   :required: true
   :name: doktype-value

   The label of your page type

.. confval:: value
   :type: string|int
   :required: true
   :name: doktype-value

   The database value for your page type

   .. note::
       The value must be a number that can be interpreted as an integer

.. confval:: icon
   :type: string
   :name: doktype-iconIdentifier

   The icon that should be displayed for this page type.

   An exception will be raised if the icon is not registered, e.g. in your `Configuration/Icons.php`

   .. hint::
       Check https://typo3.github.io/TYPO3.Icons/ if you want to use icons, that are already registered in TYPO3 core (e.g. `actions-accessibility <https://typo3.github.io/TYPO3.Icons/icons/actions/actions-accessibility.html>`_)

   `TYPO3 Docs - How to register an icon <https://docs.typo3.org/permalink/t3coreapi:icon-registration>`_
.. confval:: icon-hide
   :type: string
   :name: doktype-iconIdentifierHide

   The icon that should be displayed if the page is hidden in the navigation. This is optional in most cases


.. confval:: icon-root
   :type: string
   :name: doktype-iconIdentifierRoot

   The icon that should be displayed if the page a root page. This is optional in most cases
.. confval:: icon-contentFromPid
   :type: string
   :name: doktype-iconIdentifierContentFromPid

   The icon that should be displayed if the page contains content from another page (contentFromPid). This is optional in most cases

.. confval:: group
   :type: string
   :default: default
   :name: doktype-group

   The select item group of your page type

.. confval:: itemType
   :type: int
   :default: \\TYPO3\\CMS\\Core\\Domain\\Repository\\PageRepository::DOKTYPE_DEFAULT
   :name: doktype-itemtype

   The TCA type of the page.

   Typically you will just want the default type, but in case you really want to change it, refer to the types in PageRepository

.. confval:: columnsOverrides
   :type: array
   :name: doktype-columnsOverrides

    Changed or added ['columns'] field display definitions

.. confval:: additionalShowitem
   :type: string
   :name: doktype-showitem

    Additional Configuration of the displayed order of fields in FormEngine and their tab alignment.

   When you register a page type, the showitem value will be taken from the original type.

   Here you can specify fields, that are appended to that definition (e.g. in the Extended tab).

.. confval:: registerInDragArea
   :type: boolean
   :name: doktype-register
   :default: true

   If true, the extension will create the necessary User TSConfig to add the page type into the drag area above the page tree.page

.. confval:: allowedTables
   :type: string, comma-separated list
   :default: *
   :name: doktype-allowed

   List of database table that are allowed in pages of this type

   .. hint::
       Omit this setting to allow all tables

   Example::
       '*'

       'tt_content,tx_news_domain_model_news,tt_address'

