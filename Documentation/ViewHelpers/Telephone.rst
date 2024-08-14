..  include:: /Includes.rst.txt

..  _telephone:

================
Telephone URI
================

This viewhelper converts a telephone number into a telephone URI

..  code-block:: html
    <vcol:uri.telephone phoneNumber="{data.header}" />

If you know which region is most likely to be used, or saved in another record field, you can specify it

..  code-block:: html
    <vcol:uri.telephone phoneNumber="{data.header}" defaultRegion="de" />

Specify the format

..  code-block:: html
    <vcol:uri.telephone phoneNumber="{data.header}" format="3" />

Available Formats
    - E164 (1)
    - INTERNATIONAL (2)
    - NATIONAL (3)
    - RFC3966 (3) - Default

Uses PhoneNumberUtil of giggsey/libphonenumber-for-php

Read more: https://github.com/giggsey/libphonenumber-for-php/blob/master/docs/PhoneNumberUtil.md#format


Inline usage

..  code-block:: html
    {data.header->vcol:uri.telephone()}
    {data.header_link->vcol:uri.telephone(defaultRegion:'de',format:3)}

