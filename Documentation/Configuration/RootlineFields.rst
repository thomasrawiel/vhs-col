..  include:: /Includes.rst.txt

..  _ctypes:

============
Add rootline fields
============

:ref:`addRootLineFields documentation <t3coreapi:confval-typo3-conf-vars-fe-addrootlinefields>`

EXT:vhs_col provides a simple "api" function to avoid the hassle of checking and overriding the configuration option for `addRootlineFields`

..  code-block:: php
    \TRAW\VhsCol\Configuration\RootlineFields::addRootlineField('tx_myfield', 'tx_myfield_2', 'tx_myfield_3');

or as an array

..  code-block:: php
    \TRAW\VhsCol\Configuration\RootlineFields::addRootlineFields(['tx_myfield', 'tx_myfield_2', 'tx_myfield_3']);

.. note::
    The configuration option for rootline fields has been removed in TYPO3 13.2. This makes this function call obsolete. (see `Deprecation: #103752 - Obsolete $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] <https://docs.typo3.org/permalink/changelog:deprecation-103752-1714304437>`_)

    Using the function in TYPO3 13.2 or above will ignore the setting and trigger a deprecation message.


