..  include:: /Includes.rst.txt
..  highlight:: php

..  _rendersvgcontent:

================
RenderSvgContent
================

Render the contents of an svg as inline html

.. confval:: svgReference
    :type: TYPO3\\CMS\\Core\\Resource\\FileReference
    :required: true
    :name: rendersvg-file


.. code-block:: html
    <vcol:image.renderSvgContent svgReference="{file}"/>