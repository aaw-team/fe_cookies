.. include:: ../../Includes.txt


.. _section-configuration-typoscript:

TypoScript Configuration Reference
==================================

.. contents:: Table of Contents
    :local:

The TypoScript options configure the behaviour of the frontend plugin.
They are registered in ``plugin.tx_fecookies``.

.. tip::

    Most of the options are set up with constants, you'd rather want to
    change the values of the constants than those in setup.

.. _section-configuration-typoscript-conditions:

Conditions
----------

The extension provides a set of TypoScript conditions in symfony expression
language. They let you define the TypoScript rendering based on the
presence or the value of the cookie without getting in trouble with the
TYPO3 page cache. Learn more about conditions in the
:ref:`official TypoScript Reference <t3tsref:conditions>`.

Condition functions
^^^^^^^^^^^^^^^^^^^

cookieIsSet
"""""""""""

:aspect:`Function`
    cookieIsSet

:aspect:`Parameter`
    none

:aspect:`Type`
    bool

:aspect:`Description`
    Returns true, when the cookie is set.

:aspect:`Example`
    True if cookie is set:

    .. code-block:: typoscript

        [cookieIsSet()]

    True if cookie is not set:

    .. code-block:: typoscript

        [not cookieIsSet()]


cookieValue
"""""""""""

:aspect:`Function`
    cookieValue

:aspect:`Parameter`
    none

:aspect:`Type`
    int / string

:aspect:`Description`
    Returns the cookie value.

:aspect:`Example`
    True if the cookie value is 1:

    .. code-block:: typoscript

        [cookieValue() == 1]
  
.. important::

    Cookies have always the value "1" at the moment (when they are set by the
    API). In a future version of fe_cookies, it is planned to configure
    meaningful values.


showFrontendPlugin
""""""""""""""""""

:aspect:`Function`
    showFrontendPlugin

:aspect:`Parameter`
    int (:code:`0`, :code:`1` or :code:`-1`)

:aspect:`Type`
    bool

:aspect:`Description`
    Returns true, when the frontend plugin should be shown. The parameter should
    be the value of the predefined constant
    typoscript:`{$plugin.tx_fecookies.settings.enableFrontendPlugin}` which
    happens to be either :code:`0`, :code:`1` or :code:`-1`.

:aspect:`Example`
    True if the frontend plugin should be shown:

    .. code-block:: typoscript

        [showFrontendPlugin({$plugin.tx_fecookies.settings.enableFrontendPlugin})]

    True if the frontend plugin should not be shown:

    .. code-block:: typoscript

        [not showFrontendPlugin({$plugin.tx_fecookies.settings.enableFrontendPlugin})]


.. _section-configuration-typoscript-constants:

Constants
---------

Edit the constants in the TYPO3 Constants Editor or directly in a
``sys_template`` record. 

.. _section-configuration-typoscript-constants-templating:

Templating
^^^^^^^^^^

These options define the paths of the template files to be used for the
rendering. This is the well known setup for fluid-based rendering, the
options are defined in ``plugin.tx_fecookies.view``.

.. container:: table-row

   Option
       templateRootPath

   Data type
       string

   Description
       The template root path.

       **Default value:** ``empty``

.. container:: table-row

   Option
       partialRootPath

   Data type
       string

   Description
       The partial root path.

       **Default value:** ``empty``

.. container:: table-row

   Option
       layoutRootPath

   Data type
       string

   Description
       The layout root path.

       **Default value:** ``empty``

.. _section-configuration-typoscript-constants-settings:

Settings
^^^^^^^^

These options s are defined in ``plugin.tx_fecookies.settings``.

.. container:: table-row

   Option
       includeCssApi

   Data type
       bool

   Description
       If this option evaluates to true, the "CSS API" will be included
       in the page rendering.

       **Default value:** ``0``

.. container:: table-row

   Option
       enableCloseButton

   Data type
       bool

   Description
       If this option evaluates to true, the "close-button" will be shown
       by the plugin.

       **Default value:** ``0``

.. container:: table-row

   Option
       bannerPosition

   Data type
       string

   Description
       This option defines the position of the cookie-banner message in
       the default frontend plugin template by setting a CSS class. Valid
       values are "bottom" or "top". If the option is set to any other
       value, no (positioning) CSS class is set.

       **Default value:** ``bottom``

.. _section-configuration-typoscript-setup:

Setup
-----

As stated above, most of the options are defined using constants. The
option names are the same in constants as in setup, refer to the
constants documentation above. The following options have no constants configuration.

.. container:: table-row

   Option
       settings.beforeBlocks

   Data type
       cObject

   Description
       In the dfefault template, this cObject will be rendered right
       **before** the message block records. It is invoked by:
       
       .. code-block:: html

           <f:cObject typoscriptObjectPath="plugin.tx_fecookies.settings.beforeBlocks" />

       **Default value:** ``TEXT (empty)``

.. container:: table-row

   Option
       settings.afterBlocks

   Data type
       cObject

   Description
       In the dfefault template, this cObject will be rendered right
       **after** the message block records. It is invoked by:
       
       .. code-block:: html

           <f:cObject typoscriptObjectPath="plugin.tx_fecookies.settings.beforeBlocks" />

       **Default value:** ``TEXT (empty)``

.. _section-configuration-typoscript-defaultstyle:

Default style
-------------

The default style is included in the static template "Frontend Cookies:
Default style", which only defines
``plugin.tx_fecookies._CSS_DEFAULT_STYLE``.

.. _section-configuration-typoscript-other:

Other configurations
--------------------

In the static template "Frontend Cookies: Base", there are some further
definitions. We assume, that the default ``PAGE`` object is configured as
``page = PAGE``. If you use some other naming (eg. ``mypage = PAGE``),
be sure to register the needed scripts for it as well to get the frontend
plugin to work correctly.

Example:

.. code-block:: typoscript

    mypage = PAGE
    // ...
    mypage.includeJSFooterlibs.fe_cookies < page.includeJSFooterlibs.fe_cookies
    mypage.includeCSSLibs.fe_cookies < page.includeCSSLibs.fe_cookies
