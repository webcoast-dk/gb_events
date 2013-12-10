============================
Administrator Manual
============================

Target group: **Administrators**

Installation
=============

Install the extension as normal in the Extension Manager. Depending on your TYPO3 version you will need to confirm the creation of the necessary database tables.

Configuration
=======================

This extension is built using Extbase and Fluid. Thus the normal options of configuring storage folders or extracting the templates apply.

Adding the extension to a page gives you the choice of multiple output modes, namely „Upcoming Events“, „List of Events“ and „Calendar output“ and a „Details View“. The difference between the views is the way the events are being presented on the frontend.

Please note that the included default templates for the list mode and the upcoming mode are identical. You will not see a visual difference between these two modes unless you have more than 3 (or the configured amount) of events in your database.

All output options can be configured in the backend using flexforms. For the „Upcoming Events“ you can set the number of events to show (defaults to 3), for the „List of Events“ you can configure the number of years in the future you want to display. This defaults to one year which means that all events from today up to the same date one year from now will be displayed.

Output Modes
============


.. figure:: Images/UserManual/Frontend/Upcoming.jpg
    :width: 500px
    :alt: Frontend view of upcoming events

    Frontend view of upcoming events

    Example implementation of the frontend output for the upcoming events.

.. figure:: Images/UserManual/Frontend/List.jpg
    :width: 500px
    :alt: Frontend list view of events in the database

    Frontend list view of events in the database

    Example implementation of the list view frontend output.

.. figure:: Images/UserManual/Frontend/Show.jpg
    :width: 500px
    :alt: Frontend details view of an event

    Frontend details view of an event

    Example implementation of the details view for an event.

Customization
=============

This extension is built using Extbase and Fluid. Thus the normal options of configuring storage folders or extracting the templates apply.
Unless you configured the storage folder using TypoScript don't forget to set the Record Storage option to the folder where you have added the event records.