..  Editor configuration
	...................................................
	* utf-8 with BOM as encoding
	* tab indent with 4 characters for code snippet.
	* optional: soft carriage return preferred.

.. Includes roles, substitutions, ...
.. include:: _IncludedDirectives.rst

=================
Extension Name
=================

:Extension name: Event Calendar
:Extension key: gb_events
:Version: 1.3.4
:Description: Manuals covering TYPO3 extension "Event Calendar"
:Language: en
:Author: Morton Jonuschat
:Creation: 2013-12-04
:Generation: 09:53
:Licence: Open Content License available from `www.opencontent.org/opl.shtml <http://www.opencontent.org/opl.shtml>`_

The content of this document is related to TYPO3, a GNU/GPL CMS/Framework available from `www.typo3.org
<http://www.typo3.org/>`_

**Table of Contents**

.. toctree::
	:maxdepth: 2

	ProjectInformation
	UserManual
	AdministratorManual
	TyposcriptReference
	DeveloperCorner
	RestructuredtextHelp

.. STILL TO ADD IN THIS DOCUMENT
	@todo: add section about how screenshots can be automated. Pointer to PhantomJS could be added.
	@todo: explain how documentation can be rendered locally and remotely.
	@todo: explain what files should be versionned and what not (_build, Makefile, conf.py, ...)

.. include:: ../Readme.rst

What does it do?
=================

Provides a very simple calendar for upcoming events. Provides views for the next „n“ upcoming events, a list view and a monthly calendar view with pagination. Events can be set as recurring in certain intervals.

.. figure:: Images/UserManual/Frontend/List.jpg
		:width: 504px
		:alt: List of events for a musician

		Event list with date, time and a short description

		How the Frontend of the extension could look