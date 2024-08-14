..  include:: /Includes.rst.txt
..  highlight:: php

..  _pipe2br:

================
ExtensionLoaded
================

Checks if a specific extension is loaded. This is a ConditionViewhelper, so there's a then and else

..  code-block:: html
     <vcol:extension.extensionLoaded extensionKey="my_ext">
        <f:then>my_ext is loaded.</f:then>
        <f:else>my_ext is not loaded.</f:else>
     </vcol:extension.extensionLoaded>

Inline usage

..  code-block:: html
    my_ext {vcol:extension.extensionLoaded(extensionKey:'my_ext',then:'is', else:'is not')} loaded


