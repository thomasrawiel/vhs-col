..  include:: /Includes.rst.txt
..  highlight:: php

..  _pipe2br:

================
Pipe2Br
================

This viewhelper can be used to replace a string in a text.PhoneNumberUtil
A simple `str_replace` is used, so no regex is supported

..  tip::
    In Fluid templates you typically want to wrap the output in <f:format.raw>

..  code-block:: html
     <f:format.raw><vcol:text.pipeToBr text="{data.header}" /></f:format.raw>

..  code-block:: html
    <f:format.raw><vcol:text.pipeToBr text="{data.subheader}" search="***" replace="<br/>"/></f:format.raw>

Inline usage

..  code-block:: html
    {data.header->vcol:text.pipeToBr()->f:format.raw()}

    {data.subheader->vcol:text.pipeToBr(search:'***',replace:'<br/>')->f:format.raw()}

