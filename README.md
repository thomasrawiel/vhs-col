# vhscol
A collection of more or less useful ViewHelpers

# Usage
Namespace is registered globally as `vcol`

### URI Phone number
example with Address phone number
```
<f:link.typolink parameter="{vcol:uri.telephone(phoneNumber: address.phone)}">{address.phone}</f:link.typolink>
```

## SVG
### Render SVG Content
render a svg filereference as inline svg html
```
 <f:format.raw><vcol:renderSvgContent svgReference="{file}"/>/f:format.raw>
```

### Svg VH
render SVG from given name, has typoscript config

## Text
### Pipe to BR
If you don't allow <br> e.g. in the header, you can use `|` symbols. This vh replaces the | with <br> tag

## Misc
### Exension loaded
Condition if any extension key is loaded
```
<vcol:extension.extensionLoaded extensionKey="my_ext">
<f:then></f:then>
<f:else></f:else>
</vcol:extension.extensionLoaded>

<vcol:extension.extensionLoaded extensionKey="my_ext">
    do something
</vcol:extension.extensionLoaded>
```

## TCA Options Map ItemsProcFunction

The field must have this itemsProcFunc registered:
`\TRAW\VhsCol\Configuration\TcaOptionsMap::class . '->addOptions'`

You can register multiple condtions per field, e.g. "add option A if condition A is met and option B if condition B is met"

### Usage
For the table define the option map
```
$GLOBALS['TCA']['tt_content]['tx_vhscol_option_map'] = [...];
$GLOBALS['TCA']['pages]['tx_vhscol_option_map'] = [...];
```


For the field define one or more conditon/option array:

```
'frame_class' => [
    [
        'conditions' => [
            'fields' => [
                'fieldName1' => [ values ],
                'fieldName2' => [ values ],
            ],
            'functions' => [
                'parentPageProperties' => [
                    'parentPageFieldName' => [ values]
                ],
                'parentContainerProperties' => [
                    'parentContainerFieldName' => [ values ]
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
    ]
],

```
If you omit the condition array, the options will always be added

### Condition Types
fields: the field matches one of the values
notFields: the field matches none of the values
functions: the function returns true
functions can either be `parentPageProperties` or `parentContainerProperties`

### Usage in tt_content 
(e.g. Configuration/TCA/Overrides/tt_content.php)

Add the option if all of the following conditions are met
- Ctype=textmedia,
- colPos=0 or 1,
- parent page has doktype 100
- parent container (b13/container) has the CType "row-100" or "row-tabs"
 
```
$GLOBALS['TCA']['tt_content']['columns']['frame_class']['config']['itemsProcFunc']
 = \TRAW\VhsCol\Configuration\TcaOptionsMap::class . '->addOptions';
$GLOBALS['TCA']['tt_content']['tx_vhscol_option_map'] = [
    'frame_class' => [
        [
            'conditions' => [
                'fields' => [
                    'CType' => ['textmedia'],
                    'colPos' => [0,1],
                ],
                'functions' => [
                    'parentPageProperties' => [
                        'doktype' => [100]
                    ],
                    'parentContainerProperties' => [
                        'CType' => ['row-100', 'row-tabs']
                    ],
                ],
            ],
            'options' => [
                ['label', 'dbvalue'],
            ],
        ]
    ],
];

```
    

### Usage in pages 
(e.g. Configuration/TCA/Overrides/pages.php)

Add the option 1 and 2 if the following conditions are met
- doktype is 1 (Standard page)
- the parent site is a root page

Add the option 3 and 4 if the following conditions are met:
- doktype is 1, 10 or 100
- the parent page is not a root page


    //Add itemsProcFunc to pages field
    $GLOBALS['TCA']['pages']['columns']['layout']['config']['itemsProcFunc']
        = \TRAW\VhsCol\Configuration\TcaOptionsMap::class . '->addOptions';
    $GLOBALS['TCA']['pages']['tx_vhscol_option_map'] = [
        'layout' => [
            [
                'conditions' => [
                    'fields' => [
                        'doktype' => [1],
                    ],
                    'functions' => [
                        'parentPageProperties' => [
                            'is_siteroot' => [1]
                        ],
                    ],
                ],
                'options' => [
                    ['label1', 'dbvalue1'],
                    ['label2', 'dbvalue2'],
                ],
            ],
            // Add the options if it's doktype 1 but the parent is not a root page
            [
                'conditions' => [
                    'fields' => [
                        'doktype' => [1,10,100],
                    ],
                    'functions' => [
                        'parentPageProperties' => [
                            'is_siteroot' => [0]
                        ],
                    ],
                ],
                'options' => [
                    ['label3', 'dbvalue3'],
                    ['label4', 'dbvalue3'],
                ],
            ]
        ],
    ];
``

Also works with other tables (news, address, etc.)

# TODO:
- Documentation