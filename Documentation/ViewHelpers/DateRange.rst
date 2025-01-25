..  include:: /Includes.rst.txt
..  highlight:: php

..  _daterange:

================
DateRange
================

Outputs the range between two dates.

..  contents::


Usage
================

.. code-block:: html
     <vcol:date.dateRange start="{data.dateRangeStart}" end="{data.dateRangeEnd}"/>

.. code-block:: html
    :caption: inline usage

    {vcol:date.dateRange(start: data.dateRangeStart, end: data.dateRangeEnd)}


ViewHelper attributes
================

.. confval:: start
   :type: integer
   :required: true
   :name: daterange-start
   :default: current timestamp, if explicitly set to 0

   The start date as a unix timestamp.

.. confval:: end
   :type: integer
   :name: daterange-end
   :default: current timestamp

   The end date as a unix timestamp.

.. confval:: dateFormat
   :type: integer
   :name: daterange-date
   :default: m/d/Y (default) d.m.Y (German)

   Date format for full dates

.. confval:: dateFormatDay
   :type: integer
   :name: daterange-day
   :default: m/d (default) d.m. (German)

   Date format for dates that represent a day, e.g. when the date range is within the same month

        **05** - 08/08/2024

        Date begin: August 5, 2024

        Date end: August 8, 2024

.. confval:: dateFormatMonth
   :type: integer
   :name: daterange-month
   :default: m/d (default) d.m. (German)

   Date format for dates that represent a month, e.g. when the date range overlaps months

        **08/04** - 10/30/2024

        Date begin: August 4, 2024

        Date end: Octrober 30, 2024


.. confval:: sep
   :type: string
   :name: daterange-sep
   :default: -

   Seperator between the two dates, will always be wrapped with spaces

   .. code-block:: php
       $dateBegin . " " . $seperator . " " . $dateEnd


Example outputs
================

Some examples that use the default format settings.

- date range ends on the same day: **08/14/2024**
- date range ends in the same week: **08/05 - 08/08/2024**
- date range ends in the same month: **08/04 - 08/28/2024**
- date range ends in the same year: **08/04 - 10/30/2024**
- date range ends in the next year: **12/30/2024 - 01/20/2025**
- date range ends some years later: **08/01/2020 - 09/30/2028**

