..  include:: /Includes.rst.txt

..  _ctypes:

============
Registering Content Element Types (cTypes)
============

EXT:vhs_col provides an "api" function to easily register new content element types (cTypes), register them in the new element wizard, add a flexform, add columnsOverrides etc.

Just call the function `\TRAW\VhsCol\Configuration\CTypes::registerCTypes()` in your extension's `Configuration/Overrides/tt_content.php` file

The function expects an array with a configuration array for each cType you want to register and a label for your cTypes select item group (optional).

..  contents::

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

.. code-block:: php

    $LLL = 'LLL:EXT:my_ext/Resources/Private/Language/locallang_tca.xlf';

    \TRAW\VhsCol\Configuration\CTypes::registerCTypes([
        [
            'label' => $LLL . ':tca.CType.quote',
            'description' => $LLL . ':tca.CType.quote.description',
            'value' => 'quote',
            'icon' => 'content-quote', //use the TYPO3 icon "content-quote"
            'group' => 'my_group',
            'registerInNewContentElementWizard' => true,
            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;general,
                    --palette--;;headers,
                    tx_myext_author_field,
                    bodytext,
                    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                    --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,categories,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
            'flexform' => 'FILE:EXT:my_ext/Configration/Flexforms/QuoteFlexform.xml',
            'previewRenderer' => \MyVendor\MyExtension\Preview\PreviewRenderer::class,
            'columnsOverrides' => [
                'bodytext' => [
                    'config' => [
                        'enableRichtext' => true,
                        'richtextConfiguration' => 'lw_dist_quote',
                    ],
                ],
            ],
            'saveAndClose' => true,
            'defaultValues' => [
                'header_position' => 'center',
                'space_before_class' => 'medium',
                'header_layout' => 3,
                'header_style' => 1,
            ],
        ],
    ], 'My cTypes group');

Configuration options
================

..  confval:: label
    :type: string
    :required: true
    :name: ctype-label

    Label of the content element type (cType)

    .. hint::
        If `registerInNewContentElementWizard` is true, this will also be the label of the content element in the new element wizard

.. confval:: value
   :type: string
   :required: true
   :name: ctype-value

    Database value of the cType column

.. confval:: description
   :type: string
   :name: ctype-description

   Description of the content element.

   .. hint::
       If `registerInNewContentElementWizard` is true, this will also be the description of the content element in the new element wizard

.. confval:: iconIdentifier
   :type: string
   :name: ctype-icon

    The icon that should be displayed for this content element.

    An exception will be raised if the icon is not registered, e.g. in your `Configuration/Icons.php`

    .. hint::
        Check https://typo3.github.io/TYPO3.Icons/ if you want to use icons, that are already registered in TYPO3 core (e.g. `actions-accessibility <https://typo3.github.io/TYPO3.Icons/icons/actions/actions-accessibility.html>`_)

    `TYPO3 Docs - How to register an icon <https://docs.typo3.org/permalink/t3coreapi:icon-registration>`_
.. confval:: group
   :type: string
   :Default: default
   :name: ctype-group

    The group identifier where the content element is displayed in.
    This is relevant for the cType dropdown and the new element wizard.
.. confval:: showitem
   :type: string
   :name: ctype-showitem

    Configuration of the displayed order of fields in FormEngine and their tab alignment.

    :ref:`TYPO3 Docs - showitem <t3tca:confval-types-showitem>`
.. confval:: flexform
   :type: string
   :name: ctype-flexform

    File path to a flexform xml that you want to include.

    .. hint::
        If you use this, don't forget to include the `pi_flexform` field in your showitem configuration.

    Example::

        'flexform' => 'FILE:EXT:my_ext/Configuration/Flexforms/myCTypeFlexform.xml',

.. confval:: columnsOverrides
   :type: array
   :name: ctype-columns

    Changed or added ['columns'] field display definitions

    Example::

        //Enable the RTE for the bodytext field
        'columnsOverrides' => [
            'bodytext' => [
                'config' => [
                    'enableRichtext' => true,
                ],
            ],
        ]


    :ref:`TYPO3 docs - columnsOverrides <t3tca:confval-types-columnsoverrides>`

.. confval:: previewRenderer
   :type: classname
   :name: ctype-preview

    Configures a backend preview for a content element.

    :ref:`TYPO3 Docs - previewRenderer <t3tca:confval-types-previewrenderer>`

.. confval:: registerInNewContentElementWizard
   :type: boolean
   :name: ctype-wizard
   :default: false

    If true, the extension will automatically create the necessary TSConfig to register your content element in the new element wizard

    .. versionchanged:: 13

        All new content elements are automatically registered into the new element wizard.
        Since the default value is false, you still need to explicitly set this to true

    .. hint::
        relevant configuration options:

        - label
        - description
        - group
        - iconIdentifier
        - defaultValues

.. confval:: defaultValues
   :type: array

    Values, that should be set, when the content element is added via the new element wizard

    Expects an array

    Example::

        'defaultValues' => [
            'header_position' => 'center',
            'space_before_class' => 'medium',
            'header_layout' => 3,
        ],
.. confval:: saveAndClose
   :type: boolean
   :default: false

    If true, clicking on the new element wizard will take the user directly to the page module, rather than showing the edit form from the form engine.

    ..  versionadded:: 13


