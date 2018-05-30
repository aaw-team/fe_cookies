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

The extension provides an extra TypoScript condition, which let's you
define the TypoScript rendering based on the presence or the value of the
cookie.

.. code-block:: typoscript

    [AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies <arguments>]

Multiple arguments are accepted, they must be comma separated.

Condition arguments
^^^^^^^^^^^^^^^^^^^

.. container:: table-row

   Argument
       ``cookieSet``

   Operators
       =

   Description
       Test, whether the cookie is set or not.

       Example:

       .. code-block:: typoscript

           [AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies cookieSet = 1]
               // cookie is set
           [global]

           [AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies cookieSet = 0]
               // cookie is not set
           [global]

.. container:: table-row

   Argument
       ``cookieValue``

   Operators
       =,!=,>,>=,<,<=

   Description
       Test the cookie value. Does never match, when no cookie is set.

       Several **operators** can be used:
       
       =============   ==================================================
       Operator        Function
       =============   ==================================================
       =               The cookie value must exactly match the given
                       value.

       !=              Inverse of ``=``

       >               The cookie value must be greater than the given
                       value.

       >=              The cookie value must be greater or equal the
                       given value.

       <               The cookie value must be less than the given
                       value.

       <=              The cookie value must be less or equal the given
                       value.
       =============   ==================================================

       **Values** can be a single value, or a set of values, separated
       by "|". The latter will test all values and return true as soon as
       a match occured ("OR-chain").

       Examples:

       .. code-block:: typoscript

           [AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies cookieValue = 1]
               // cookie is set and its value equals 1
           [global]

           [AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies cookieValue = 0|1|2]
               // cookie is set and its value equals 0, 1 or 2
           [global]
           
           [AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies cookieValue >= 2]
               // cookie is set and its value is greater or equal 2
           [global]

       .. important::

           Cookies have always the value "1" at the moment (when they are
           set by the API). In a future version of fe_cookies, it is
           planned to configure meaningful values.

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
