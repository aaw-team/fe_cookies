.. include:: ../Includes.txt


.. _section-installation:

Installation
============

System Requirements
-------------------

+------------------+---------+----------+--------------------+
| Frontend Cookies | State   | PHP      | TYPO3              | 
+==================+=========+==========+====================+
| 0.4              | active  | 5.6      | 6.2, 7.6, 8.7, 9.2 |
+------------------+---------+----------+--------------------+
| 1.0              | planned | 7.2      | 9.5, 10.4          |
+------------------+---------+----------+--------------------+


Extension Installation
----------------------

Install the extension :ref:`the regular way in TYPO3
<t3coreapi:extension-install>`.

For `composer <https://getcomposer.org/>`_ users:

.. code-block:: bash

    composer require aaw-team/fe_cookies

Or if you want to load the code from git:

.. code-block:: bash

    git clone https://github.com/aaw-team/fe_cookies.git


.. _section-installation-includestatictemplates:

Include static templates
------------------------

Include the static templates "Frontend Cookies: Base", and "Frontend Cookies:
Default style". The latter is optional but recommended.


.. _section-installation-includefrontendplugin:

Include frontend plugin
-----------------------

Include the frontend plugin via TypoScript:

.. code-block:: typoscript

    page.200 = < tt_content.list.20.fecookies_fecookies
