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

The extension provides some API methods, that can be used in TypoScript
conditions. They let you define the TypoScript rendering based on the
presence or the value of the cookie without getting in trouble with the
TYPO3 page cache. Learn more about userFunc conditions in the
:ref:`TypoScript Reference <t3tsref:condition-userfunc>`.

The available methods are grouped in the class
``AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies``.

**Usage:**

.. code-block:: typoscript

    [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::<method>(<arguments>)]

Condition methods
^^^^^^^^^^^^^^^^^

.. container:: table-row

   Method
       ``cookieIsSet``

   Arguments
       none

   Description
       Returns true, when the cookie is set.

       Example:

       .. code-block:: typoscript

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieIsSet()]
               // cookie is set
           [else]
               // cookie is not set
           [global]

.. container:: table-row

   Method
       ``cookieIsNotSet``

   Arguments
       none

   Description
       Returns true, when the cookie is not set.

       Example:

       .. code-block:: typoscript

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieIsNotSet()]
               // cookie is not set
           [else]
               // cookie is set
           [global]

.. container:: table-row

   Method
       ``cookieValue``

   Arguments
       string

   Description
       Returns true when the cookie value matches at least one of the
       arguments. Returns false when no cookie is set.

       Multiple arguments are accepted, they must be comma separated.

       The arguments are divided into an operator and a value part. One
       argument must be composed by ``<operator>[SPACE]<value>``.

       Several **operators** can be used:
       
       =============   ==================================================
       Operator        Function
       =============   ==================================================
       =               The cookie value must exactly match the given
                       value.

       !=              Inverse of "="

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

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieValue(= 1)]
               // cookie is set and its value equals 1
           [global]

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieValue(= 0|1|2)]
               // cookie is set and its value equals 0, 1 or 2
           [global]

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieValue(>= 2)]
               // cookie is set and its value is greater or equal 2
           [global]

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieValue(= 0, > 2)]
               // cookie is set and its value is either 0 or greater than 2
           [global]

       .. important::

           Cookies have always the value "1" at the moment (when they are
           set by the API). In a future version of fe_cookies, it is
           planned to configure meaningful values.

.. container:: table-row

   Method
       ``showFrontendPlugin``

   Arguments
       int (``0``, ``1`` or ``-1``)

   Description
       Returns true, when the frontend plugin should be shown. The
       argument should be the value of the predefined constant

       ``{$plugin.tx_fecookies.settings.enableFrontendPlugin}``
       
       which happens to be either ``0``, ``1`` or ``-1``.

       Example:

       .. code-block:: typoscript

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::showFrontendPlugin({$plugin.tx_fecookies.settings.enableFrontendPlugin})]
               // frontend plugin should be shown
           [else]
               // frontend plugin should be hidden
           [global]

.. container:: table-row

   Method
       ``hideFrontendPlugin``

   Arguments
       int (``0``, ``1`` or ``-1``)

   Description
       Returns true, when the frontend plugin should be hidden. The
       argument should be the value of the predefined constant

       ``{$plugin.tx_fecookies.settings.enableFrontendPlugin}``
       
       which happens to be either ``0``, ``1`` or ``-1``.

       This condition is used internally to give logged in backend users
       the possibility to show the cookie-banner always during setup.

       Example:

       .. code-block:: typoscript

           [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::hideFrontendPlugin({$plugin.tx_fecookies.settings.enableFrontendPlugin})]
               // frontend plugin should be hidden
           [else]
               // frontend plugin should be shown
           [global]

       .. tip::

           In the defaultContentRendering TypoScript, it is used like this:

           .. code-block:: typoscript

               [userFunc = AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::hideFrontendPlugin({$plugin.tx_fecookies.settings.enableFrontendPlugin})]
                   tt_content.list.20.fecookies_fecookies >
               [global]

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
