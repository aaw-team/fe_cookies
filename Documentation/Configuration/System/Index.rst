.. include:: ../../Includes.txt


.. _section-configuration-system:

System Configuration Reference
==============================

.. contents:: Table of Contents
    :local:

.. _section-configuration-system-basics:

Basics
------

In the system configuration, the settings for the cookies can be set up.
Everything takes place in one single array. You can deploy your
configurations in ``typo3conf/AdditionalConfiguration.php``.

The whole configurtation consists of named arrays, that define the
concrete cookie settings for one single domain each. The default
settings are defined in the key of
``\AawTeam\FeCookies\Configuration\Configuration::GLOBAL_KEY`` (which is
the string ``_global_``).

.. important::

    Use the constant
    ``\AawTeam\FeCookies\Configuration\Configuration::GLOBAL_KEY``
    instead of ``_global_``, to have no problems in future versions.


The default configuration is:

.. code-block:: php

    use \AawTeam\FeCookies\Configuration\Configuration;

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fe_cookies'] = [
        Configuration::GLOBAL_KEY => [
            Configuration::OPTION_NAME     => Configuration::DEFAULT_COOKIE_NAME,
            Configuration::OPTION_LIFETIME => null,
            Configuration::OPTION_DOMAIN   => null,
            Configuration::OPTION_PATH     => null,
            Configuration::OPTION_SECURE   => null,
            Configuration::OPTION_HTTPONLY => null,
        ],
    ];


.. _section-configuration-system-behaviour:

Behaviour
---------

For the first time when the configuration is used, it looks, whether a
configuration-set is defined for the currently requested domain. If yes,
the set is merged with the default set, otherwise the default set is
used. Like this it becomes possible, to just configure the relevant
options.

Example:

.. code-block:: php

    use \AawTeam\FeCookies\Configuration\Configuration;

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fe_cookies']['example.org'] = [
        Configuration::OPTION_LIFETIME => 3600,
        Configuration::OPTION_SECURE   => true,
    ];

    // Will become:
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fe_cookies']['example.org'] = [
        Configuration::OPTION_NAME     => Configuration::DEFAULT_COOKIE_NAME,
        Configuration::OPTION_LIFETIME => 3600,
        Configuration::OPTION_DOMAIN   => null,
        Configuration::OPTION_PATH     => null,
        Configuration::OPTION_SECURE   => true,
        Configuration::OPTION_HTTPONLY => null,
    ];

.. _section-configuration-system-options:

Options
-------

Every option in a configuration set is represented by a string. To make
code easier to read and future-proof, you are encouraged to use the
defined constants instead of the actual strings.

.. tip::
      The options are designed to be used with the PHP function
      ``setcookie()``, see
      https://secure.php.net/manual/en/function.setcookie.php for
      detailed information.

.. container:: table-row

   Option
       ``Configuration::OPTION_NAME``

   Data type
       string

   Description
          .. code-block:: php

              \AawTeam\FeCookies\Configuration\Configuration::OPTION_NAME = 'name';

          The name of the cookie. This string must contain only
          US-ASCII characters, except control characters (0-31;127),
          space, tab or one of the following characters:
          ``()<>@,;:"/[]?={}``.

          For more information see section "2.2 Basic Rules" in RFC 2616
          (https://www.ietf.org/rfc/rfc2616.txt).

          **Default value:** ``Configuration::DEFAULT_COOKIE_NAME`` (``"tx_fecookies"``)

.. container:: table-row

   Option
       ``Configuration::OPTION_LIFETIME``

   Data type
       int/null

   Description
          .. code-block:: php

              \AawTeam\FeCookies\Configuration\Configuration::OPTION_LIFETIME = 'lifetime';

          Defines the lifetime of the cookie. The expiration datetime of
          the cookie will be calculated by adding
          ``Configuration::OPTION_LIFETIME`` to
          ``$GLOBALS['ACCESS_TIME']``.

          If this value is ``null``, the cookie will expire, when the
          browser session ends.

          **Default value:** ``null``

.. container:: table-row

   Option
       ``Configuration::OPTION_DOMAIN``

   Data type
       string/null

   Description
          .. code-block:: php

              \AawTeam\FeCookies\Configuration\Configuration::OPTION_DOMAIN = 'domain';

          Defines the (sub)domain of the cookie.

          If this value is ``null``, the cookie will be set for the
          currently requested domain.

          **Default value:** ``null``

.. container:: table-row

   Option
       ``Configuration::OPTION_PATH``

   Data type
       string/null

   Description
          .. code-block:: php

              \AawTeam\FeCookies\Configuration\Configuration::OPTION_PATH = 'path';

          Defines the path of the cookie.

          If this value is ``null``, the cookie will be set to the
          currently requested path
          (``GeneralUtility::getIndpEnv('TYPO3_SITE_PATH')``), or, when
          ``Configuration::OPTION_DOMAIN`` is set, to ``"/"``.

          **Default value:** ``null``

.. container:: table-row

   Option
       ``Configuration::OPTION_SECURE``

   Data type
       string/null

   Description
          .. code-block:: php

              \AawTeam\FeCookies\Configuration\Configuration::OPTION_SECURE = 'secure';

          Defines the 'secure' option of the cookie. It will be
          transferred over secure (TLS/SSL) connection only.

          If this value is ``null``, the cookie-secure option will be
          set to `true`, when the current request is made over a secure
          connection
          (``GeneralUtility::getIndpEnv('TYPO3_SSL') == true``).
          Otherwise it will become ``false``.

          .. important::

              Setting ``Configuration::OPTION_SECURE`` to ``true`` in a
              non-TLS/SSL environment will throw an exception!

          **Default value:** ``null``

.. container:: table-row

   Option
       ``Configuration::OPTION_HTTPONLY``

   Data type
       string/null

   Description
          .. code-block:: php

              \AawTeam\FeCookies\Configuration\Configuration::OPTION_HTTPONLY = 'httpOnly';

          Defines the 'httpOnly' option of the cookie. If set to
          ``true``, the cookie will not be accessible for JavaScript in
          the frontend.

          If this value is ``null``, the 'httpOnly' option will be
          ignored.

          .. important::

              Setting ``Configuration::OPTION_HTTPONLY`` to ``true`` will
              make the cookie inaccessible to JavaScript in the frontend.
              **The JavaScript API won't work in that case!**
              Only set if you know what you do.

          **Default value:** ``null``

