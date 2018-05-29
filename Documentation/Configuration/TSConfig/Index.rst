.. include:: ../../Includes.txt


.. _section-configuration-tsconfig:

TSConfig Reference
==================

Use the TSConfig options to configure the behaviour of the backend
module. You can set it for backend users, backend groups or pages.

The options are defined in ``mod.fe_cookies``.

.. _section-configuration-tsconfig- settingsmanagement:

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
