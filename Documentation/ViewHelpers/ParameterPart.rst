..  include:: /Includes.rst.txt
..  highlight:: php

..  _parameterpart:

================
ParameterPart
================

Extract a specific part of a typolink. This could be useful for example for automatically creating buttons with the link title as button text.

If no fallback is set, it the viewhelper can be used in conditions.

..  contents::


ViewHelper attributes
================

.. confval:: part
   :type: string
   :required: true
   :name: param-part

   One of the following parts from a typolink:

   - title
   - css class
   - target
   - url
   - additionalParams

.. confval:: link
   :type: string
   :name: param-link

   The typolink as a string

   .. code-block:: txt
       t3://page?uid=16#10 _blank my-css-class "Link title" &test=123

.. confval:: fallback
   :type: string
   :name: param-fallback

   Fallback string if the part is either not found or not set

Usage
================

.. code-block:: html
    <vcol:link.parameterPart link="{data.header_link}" part="title" fallback="Fallback button text" />

.. code-block:: html
    :caption: inline usage

    {data.header_link->vcol:link.parameterPart(part:'title', fallback:'fallback button text')}


Examples
================

Button link where the button text is with the typolink
~~~~~~~~~~~~~~

.. code-block:: html

    <f:link.typolink parameter="{data.header_link}" class="button">
        <vcol:link.parameterPart link="{data.header_link}" part="title" fallback="Fallback button text" />
    </f:link.typolink>

If no fallback is set, the ViewHelper can be used in conditions
~~~~~~~~~~

.. code-block:: html
    <f:if condition="{data.header_link->vcol:link.parameterPart(part:'title')}">
        <f:then>Title is set</f:then>
        <f:else>Title is not set</f:else>
    </f:if>

Add a class based on the link target
~~~~~~~~~~

.. code-block:: html
    <f:link.typolink parameter="{data.header_link}" class="{f:if(condition: '{data.header_link->vcol:link.parameterPart(part:\'target\')} == \'_blank\'', then:'link-external')}">
        {data.header}
    </f:link.typolink>

.. code-block:: txt
   t3://page?uid=16#10 _blank my-css-class "Link title" &test=123

will result in `class="my-css-class link-external"`
