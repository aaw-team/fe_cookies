.. include:: ../Includes.txt


.. _section-developer:

Developer
=========

In this section of the documentation, the development-relevant parts of
the extension fe_cookies are described.

The extension ships with a small API for PHP, JavaScript and a special
"CSS API". The scripting APIs are designed for simplicity and are
ready-to-use. The namings are consistent, say, they are the same for both
APIs.

.. contents:: Table of Contents
    :local:

.. _section-developer-php:

PHP API
-------

.. code-block:: php

    class \AawTeam\FeCookies\Utility\FeCookiesUtility {
        /**
         * @return string
         */
        public static function getCookieName();

        /**
         * @return string|false
         */
        public static function getCookieValue();

        /**
         * @return bool
         */
        public static function setCookie();

        /**
         * @return bool
         */
        public static function hasCookie();

        /**
         * @return bool
         */
        public static function removeCookie();
    }

Following public API is designed to be used in TypoScript Conditions. For
detailed information read the
:ref:`Chapter about TypoScript conditions <section-configuration-typoscript-conditions>`.

.. code-block:: php

    class \AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies {
        /**
         * @return bool
         */
        public static function cookieIsSet();

        /**
         * @return bool
         */
        public static function cookieIsNotSet();

        /**
         * @param string ...$conditionParameters
         * @return bool
         */
        public static function cookieValue(...$conditionParameters);

        /**
         * @param string $enableFrontendPlugin
         * @return bool
         */
        public static function showFrontendPlugin($enableFrontendPlugin);

        /**
         * @param string $enableFrontendPlugin
         * @return bool
         */
        public static function hideFrontendPlugin($enableFrontendPlugin);
    }


.. _section-developer-javascript:

JavaScript API
--------------

.. code-block:: js

    window.AawTeam.feCookies {
        /**
         * @return string
         */
        function getCookieName();

        /**
         * @return string|false
         */
        function getCookieValue();

        /**
         * @return bool
         */
        function setCookie();

        /**
         * @return bool
         */
        function hasCookie();

        /**
         * @return bool
         */
        function removeCookie();
    }

.. _section-developer-css:

CSS API
-------

This is a special one. fe_cookies provides a CSS file, which you can
include optionally (by setting the appropriate constant). It contains
classes, that can be used to show/hide elements in your HTML structure.

To use it, set TypoScript constant
``plugin.tx_fecookies.settings.includeCssApi`` to ``1``. Then use the
following (or equivalent) TypoScript:

.. code-block:: typoscript

    config.htmlTag_setParams = class="tx_fe_cookies-noCookie"
    [userFunc=AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies::cookieIsSet()]
        config.htmlTag_setParams = class="tx_fe_cookies-hasCookie"
    [global]

Now, you can use following classes:

.. code-block:: css

    /* Classes for elements when cookie has NOT been set */
    html.tx_fe_cookies-noCookie {
        .tx_fe_cookies-noCookie-display-none {
            display: none;
        }
        .tx_fe_cookies-noCookie-display-block {
            display: block;
        }
        .tx_fe_cookies-noCookie-display-inline {
            display: inline;
        }
        .tx_fe_cookies-noCookie-display-inline-block {
            display: inline-block;
        }
    }

    /* Classes for elements when cookie HAS been set */
    html.tx_fe_cookies-hasCookie {
        .tx_fe_cookies-hasCookie-display-none {
            display: none;
        }
        .tx_fe_cookies-hasCookie-display-block {
            display: block;
        }
        .tx_fe_cookies-hasCookie-display-inline {
            display: inline;
        }
        .tx_fe_cookies-hasCookie-display-inline-block {
            display: inline-block;
        }
    }

Example:

.. code-block:: html

    <div class="
        tx_fe_cookies-noCookie-display-block
        tx_fe_cookies-hasCookie-display-none">
        <!-- This element will be show when no cookie has been set and hidden otherwise. -->
    </div>

    <div class="
        tx_fe_cookies-noCookie-display-none
        tx_fe_cookies-hasCookie-display-block">
        <!-- This element will only be show, when a cookie has been set and hidden otherwise. -->
    </div>
