..  include:: /Includes.rst.txt

..  _tcaoptions:

============
Conditional TCA Options
============

This extension adds the possibility to configure TCA select options with simple conditions.

This works for any select field that is configured in your TCA of any table.

.. contents::


Usage
===========

The field must have this itemsProcFunc registered: \\TRAW\\VhsCol\\Configuration\\TcaOptionsMap::class . '->addOptions'

In the appropriate `TCA/Overrides` file place the options map configuration

.. code-block:: php
   :caption: EXT:myext/Configuration/Overrides/tt_content.php

   $GLOBALS['TCA']['tt_content]['tx_vhscol_option_map'] = [
        // your config here
   ];

.. code-block:: php
   :caption: EXT:myext/Configuration/Overrides/pages.php

   $GLOBALS['TCA']['pages]['tx_vhscol_option_map'] = [
        // your config here
   ];


For the field define one or more options/conditons arrays

If an array has no condition it is always added

.. code-block:: php
    'my_select_field' => [
        //always added, you can also configure that in the TCA
        [
            'options' => [
                ['label-a', 'dbvalue-a'],
                ['label-a2', 'dbvalue-a2'],
                ['label-a3', 'dbvalue-a3'],
            ],
        ],
        [
            'conditions' => [
                'fields' => [
                    'fieldName1' => [ 'value' ],
                    'fieldName2' => [ 'value', 'value2' ],
                ],
                'functions' => [
                    'parentPageProperties' => [
                        'parentPageFieldName' => [ 'value' ]
                    ],
                    'parentContainerProperties' => [
                        'parentContainerFieldName' => [ 'value' ]
                    ],
                ],
            ],
            'options' => [
                ['label', 'dbvalue'],
                ['label2', 'dbvalue3'],
                ['label3', 'dbvalue3'],
            ],
        ],
        [
            // other options and conditions
        ],
    ],


Conditions
----------
fields
~~~~~~~~~~
A fields condition is true if the database value matches one of the values in the configuration

Multiple field conditions are treated as AND condition, multiple values inside a condition are treated as OR

.. code-block:: php
   :caption: cType is textmedia OR image

   'fields' => [
       CType' => ['textmedia', 'image'],
   ],

.. code-block:: php
   :caption: cType is textmedia AND ( colPos is 0 OR 1)

    'fields' => [
       'CType' => ['textmedia'],
       'colPos' => [0,1],
    ],

notFields
~~~~~~~~~~
A notFields condition is true, if the database value doesn't match any of the values in the configuration

Multiple notFields condtions are treated as AND conditon, multiple values inside a conditon are also evaluated with AND

.. code-block:: php
   :caption: cType is not image

   'notFields' => [
       'CType' => ['image'],
   ],

.. code-block:: php
   :caption: cType is not image AND colPos is not 0 AND colPos is not 1

   'notFields' => [
       'CType' => ['image'],
       'colPos' => [0,1]
   ],

functions
~~~~~~~~~~

There are currently 2 types of functions conditions: `parentPageProperties` and `parentContainerProperties`

parentPageProperties function can be used to check for specific values in the content elements parent page

parentContainerProperties function can be used to check for specific values in the content elements parent container (EXT:container)

Multiple functions conditions are treated as AND condition, multiple values inside a condition are treated as OR

.. code-block:: php
   :caption: The parent page layout property has either value-1 or value-2

   'functions' => [
       'parentPageProperties' => [
           'layout' => [ 'value-1', 'value-2' ]
       ],
   ]

.. code-block:: php
   :caption: The parent container has a specific type

   'functions' => [
       'parentContainerProperties' => [
           'CType' => [ 'container-row-type']
       ],
   ]

.. code-block:: php
   :caption: The parent page layout property has either value-1 or value-2 AND the page-type 100

   'functions' => [
       'parentPageProperties' => [
           'layout' => [ 'value-1', 'value-2' ],
           'doktype' => [ 100 ]
       ],
   ]

.. code-block:: php
   :caption: The parent page layout property has either value-1 or value-2 AND the page-type 100. Additionally the parent container has a specific type

   'functions' => [
       'parentPageProperties' => [
           'layout' => [ 'value-1', 'value-2' ],
           'doktype' => [ 100 ]
       ],
       'parentContainerProperties' => [
              'CType' => [ 'container-row-type']
          ],
   ]



Example
===========

In this example we override tt_content's frame_class property

.. code-block:: php

   // the select field has a default value and maybe some other values
   $GLOBALS['TCA']['tt_content']['columns']['frame_class']['config']['items'] = [
       ['label' => 'Default', 'value' => 'default'],
       ['label' => 'Value 1', 'value' => 'value1'],
       ['label' => 'Value 3', 'value' => 'value2'],
   ];

   //Add the itemsProcFunc
   $GLOBALS['TCA']['tt_content']['columns']['frame_class']['config']['itemsProcFunc']
           = \TRAW\VhsCol\Configuration\TcaOptionsMap::class . '->addOptions';


   //Configure the conditions
    $GLOBALS['TCA'][$table]['tx_vhscol_option_map'] = [
       'frame_class' => [
            //condition 1: if ctype is contactCard or contactCardCTA, then add the options card and card-alt
           [
               'conditions' => [
                   'fields' => [
                       'CType' => ['contactCard', 'contactCardCTA'],
                   ],
               ],
               'options' => [
                   ['label' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:contactCard.cardLayout', 'value' => 'card'],
                   ['label' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:contactCard.cardLayoutAlt', 'value' => 'card-alt'],
               ],
           ],
           //condition 2: if a textmedia is inside a container column (colPos 100) and the parent page has the page-type 100, then add special-value
           [
               'conditions' => [
                   'fields' => [
                       'CType' => ['textmedia'],
                       'colPos' => [101],
                   ],
                   'functions' => [
                       'parentPageProperties' => [
                           'doktype' => [100],
                       ],
                   ],
               ],
               'options' => [
                   ['label' => 'Special label', 'value' => 'special-value'],
               ],
           ],
           //condition 3: if a textmedia is inside a container column (colPos 100 or colPos 101) and the parent page has the page-type 100, then add special-value-2
           [
                'conditions' => [
                    'fields' => [
                        'CType' => ['textmedia'],
                        'colPos' => [100,101],
                    ],
                    'functions' => [
                        'parentPageProperties' => [
                            'doktype' => [100],
                        ],
                    ],
                ],
                'options' => [
                    ['label' => 'Special label 2', 'value' => 'special-value-2'],
                ],
          ],
       ],
   ];
    // Result:
    // For a textmedia in colPos 100 on a page with doktype 100 condition 3 will be true
    // For a textmedia in colPos 101 on a page with doktype 100 condition 2 AND condition 3 will be true


