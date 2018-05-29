.. include:: ../Includes.txt


.. _section-administration:

Administration
==============

.. contents:: Table of Contents
    :local:


.. _section-administration-installation:

Installation
------------

Install the extension via Extension Manager. There is also a composer
package available:

  `composer require aaw-team/fe_cookies`

.. _section-administration-includestatictemplates:

Include static templates
------------------------

Include the static templates "Frontend Cookies: Base", and
"Frontend Cookies: Default style". The latter is optional but
recommended.

.. _section-administration-includefrontendplugin:

Include frontend plugin
-----------------------

Include the frontend plugin via TypoScript:

.. code-block:: typoscript

    page.200 = < tt_content.list.20.fecookies_fecookies
