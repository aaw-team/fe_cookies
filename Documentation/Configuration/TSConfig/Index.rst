.. include:: ../../Includes.txt


.. _section-configuration-tsconfig:

TSConfig Reference
==================

Use the TSConfig options to configure the behaviour of the backend
module. You can set it for backend users, backend groups or pages.

The options are defined in ``mod.fe_cookies``.

.. contents:: Table of Contents
    :local:

.. _section-configuration-tsconfig-settingsmanagement:

Settings management
-------------------

The following options are defined in
``mod.fe_cookies.settingsManagement``.

.. container:: table-row

   Option
       enable

   Data type
       bool

   Description
          If this option evaluates to true for a backend user, the
          settings-dialog in the backend module will become available.
          
          .. tip::
              
              Admin users can always access the settings in the backend
              module.

          This option is likely to be used in backend user (group)
          TSConfig.

          **Default value:** ``0``

.. container:: table-row

   Option
       templateUid

   Data type
       int

   Description
          Defines the uid of one concrete record from ``sys_template``
          that will be used in the settings-dialog. Use in conjunction
          with ``templatePid``, if needed.

          If set to ``0`` the first template found on the current page,
          (or ``templatePid``) will be used.

          This option is likely to be used in page TSConfig.

          **Default value:** ``0``

.. container:: table-row

   Option
       templatePid

   Data type
       int

   Description
          Defines the pid to look for ``sys_template`` records.

          If empty, the uid of the current selected page will be used.

          This option is likely to be used in page TSConfig.

          **Default value:** ``empty``

.. container:: table-row

   Option
       allowedConstantNames

   Data type
       array

   Description
          With this option it is possible to configure, which options
          will be show in the settings dialog of the backend module.

          Every value in the array defines one rule. The rules describe
          the TypoScript constant names (in
          ``plugin.tx_fecookies.settings.``) that are visible (and
          writable). A rule can contain a wildcard (``*``) that matches
          any string. See the default values to get an idea.

          This option is likely to be used in backend user (group)
          TSConfig.

          **Default value:**

          .. code-block:: typoscript

              mod.fe_cookies.settingsManagement.allowedConstantNames {
                  0 = enableCloseButton
                  1 = styles.*
              }

.. _section-configuration-tsconfig-languagemanagement:

Language management
-------------------

The language management allows backend users to edit the language labels
that are used in the frontend plugin. To achieve this, the module stores
the userdefined labels in dynamically generated xml files (xliff format)
which are located in
``typo3conf/tx_fecookies/UserdefinedLanguageLabels``.

Currently available labels:

    * ``plugin.label.button.accept``
    * ``plugin.label.button.close``
    * ``plugin.label.aria.banner``

The following options are defined in
``mod.fe_cookies.languageManagement``.

.. container:: table-row

    Option
        enable

    Data type
        bool

    Description
        If this option evaluates to true for a backend user, the
        language management in the backend module will become available.
        Admin users are always allowed.

        .. important::

            If you enable language management in the backend module, be
            sure to correctly set the options `defaultLanguageIsocode``
            and/or ``storageMode`` **before** using the module.

        This option is likely to be used in backend user (group)
        TSConfig.

        **Default value:** ``0``

.. container:: table-row

    Option
        storageMode

    Data type
        string

    Description
        This option defines, how the xml files are stored in the
        filesystem. By default (``global``), all files stay in the
        predefined location (
        ``typo3conf/tx_fecookies/UserdefinedLanguageLabels``).

        If you have a TYPO3 installation that manages multiple websites
        and you must be able to set different labels in different
        pages/rootlines, you can change this option to ``page:<UID>``.
        Where ``<UID>`` is the actual uid of the page under which the
        labels should be accessible. In this case, the xml files are
        stored in
        ``typo3conf/tx_fecookies/UserdefinedLanguageLabels/page_<UID>``.

        Like this, the frontend plugin will be able to look up the
        rootline, until it finds a folder with language files. If
        nothing is found, the global location will be tried. If still
        no result is available, the default files shipped with fe_cookies
        itself (
        ``EXT:fe_cookies/Resources/Private/Language/userdefinedLabels.xlf``
        )
        will be used.

        .. tip::

            To override the extension-defaults, you can use "Custom
            translations", see the
            :ref:`TYPO3 Core API <t3coreapi:xliff-translating-custom>`
            documentation for reference.

        This option is likely to be used in page TSConfig.

        **Default value:** ``global``

.. container:: table-row

    Option
        defaultLanguageIsocode

    Data type
        string

    Description
        Use this option, when you have a language other than english as
        your default website language.

        .. tip::

            You might want to configure your default language like this
            (for german):

            .. code-block:: typoscript

                mod.SHARED {
                    defaultLanguageFlag = de
                    defaultLanguageLabel = Deutsch
                }
                mod.fe_cookies.languageManagement.defaultLanguageIsocode = de

        This option is likely to be used in page TSConfig.

        **Default value:** ``default``

.. container:: table-row

    Option
        allowedLanguageLabels

    Data type
        string/list

    Description
        With this option, you can define, which language labels can be
        edited in the backend module. It is a string with comma-separated
        labels. See above for the currently available labels.

        Examples:

        .. code-block:: typoscript

            // Allow everything (from default), except the aria-label 
            mod.fe_cookies.languageManagement {
                allowedLanguageLabels := removeFromList(plugin.label.aria.banner)
            }

            // Allow only the accept-button label
            mod.fe_cookies.languageManagement {
                allowedLanguageLabels = plugin.label.button.accept
            }

        This option is likely to be used in page TSConfig.

        **Default value:** ``plugin.label.button.accept, plugin.label.button.close, plugin.label.aria.banner``
