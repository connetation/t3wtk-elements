.. include:: /Includes.rst.txt

.. _developer-guide:

================
Developer Guide
================

.. _adding-content-element:

Adding a new content element
=============================

Use the scaffold CLI — it generates every required file and patches all
registration files automatically.

Local (Node.js installed on host), from :file:`vendor/conn/t3kit-elements`::

   npm run make-element

This works out of the box wherever the package is installed, since the
script (:file:`bin/make-element.mjs`) ships inside ``conn/t3kit-elements``
itself.

Optional: a DDEV host command
-------------------------------

.. important::

   Unlike ``npm run make-element``, the ``ddev make-element`` shortcut is
   **not** part of the ``conn/t3kit-elements`` package — it is a project-level
   DDEV host command that must exist in *your own* project's
   :file:`.ddev/commands/host/make-element`. Installing ``conn/t3kit-elements``
   via Composer into a fresh project does not give you this command; you
   need to add the script yourself before ``ddev make-element`` will
   work.

To add it:

#. Create the file :file:`.ddev/commands/host/make-element` at the root
   of your TYPO3 project (create the ``host`` directory too if it
   doesn't exist yet — note this is a project-level DDEV directory, not
   part of ``conn/t3kit-elements``).
#. Paste in the following script:

   .. code-block:: bash

      #!/usr/bin/env bash
      ## Description: Scaffold a new content element in conn/t3kit-elements
      ## Usage: make-element ["Element Name"]
      ## Example: ddev make-element
      ## Example: ddev make-element "Hero Banner"

      set -euo pipefail

      ARGS="$*"
      if [ -n "$ARGS" ]; then
          ddev exec bash -c "cd /var/www/html/vendor/conn/t3kit-elements && node bin/make-element.mjs '$ARGS'"
      else
          ddev exec bash -c "cd /var/www/html/vendor/conn/t3kit-elements && node bin/make-element.mjs"
      fi

#. Make it executable — DDEV silently ignores host commands that aren't::

      chmod +x .ddev/commands/host/make-element

#. Restart DDEV so it picks up the new command::

      ddev restart

   (``ddev restart`` is only needed the first time you add a new host
   command; editing an existing one afterwards does not require a
   restart.)

``/var/www/html`` is DDEV's default in-container path for the project
root, so this script works unmodified on any standard DDEV setup. If your
project customises the web container's document/project root, adjust that
path accordingly.

Once that host command is in place, you can run::

   ddev make-element

Or pass the element name directly to skip the first prompt::

   ddev make-element "Hero Banner"

The host command itself is a thin wrapper that opens an interactive
terminal inside the web container (``ddev exec -it``) and runs
``node bin/make-element.mjs`` from :file:`vendor/conn/t3kit-elements`, so
readline prompts work correctly; running plain ``ddev exec`` without
``-it`` would hang waiting for input.

Either way you invoke it, the scaffold CLI asks three questions:

1. **Element name** — e.g. ``Hero Banner``, ``Testimonial``, ``Pricing Table``
2. **Type** — Simple (FlexForm settings only) or With inline child records
   (like Accordion / Cards)
3. **CE group** — ``conn_bootstrap``, ``conn``, or ``conn_swiper``

What gets generated
--------------------

.. list-table::
   :header-rows: 1
   :widths: 60 40

   * - File
     - Purpose
   * - ``Configuration/FlexForms/<Name>.xml``
     - Display settings form
   * - ``Configuration/TypoScript/ContentElements/<Name>.typoscript``
     - Frontend rendering setup
   * - ``Configuration/TCA/tx_conn_t3kit_elements_domain_model_<name>_item.php``
     - Child record TCA (with-items only)
   * - ``Classes/DataProcessing/<Name>ItemsProcessor.php``
     - DB query + FlexForm resolver (with-items only)
   * - ``Resources/Private/Templates/ContentElements/<Name>.html``
     - Main Fluid template
   * - ``Resources/Private/Partials/<Name>/Item.html``
     - Item partial (with-items only)
   * - ``Resources/Private/Components/<Name>/index.scss``
     - SCSS entry point
   * - ``Resources/Private/Components/<Name>/_<name>.scss``
     - SCSS component
   * - ``Resources/Private/Components/<Name>/<name>.ts``
     - TypeScript component
   * - ``Resources/Public/Icons/<name>-ce.svg``
     - Icon placeholder (replace with real SVG)

What gets patched automatically
--------------------------------

:file:`Configuration/Icons.php`, :file:`build.mjs`, :file:`ext_tables.sql`,
:file:`ext_localconf.php`, :file:`Configuration/TCA/Overrides/tt_content.php`,
:file:`Resources/Private/Language/locallang_be.xlf`,
:file:`Configuration/TypoScript/setup.typoscript`.

All patches are idempotent — re-running the script on an existing element
name is safe.

.. _after-scaffold:

After running the scaffold
----------------------------

These steps must be done **in order** — skipping or reordering them
causes an "Unknown column" SQL error when saving the element in the
backend.

#. **Build assets** — ``npm run build`` locally, or via DDEV as above.
#. **Flush TYPO3 caches** — Admin Tools > Maintenance > Flush all caches,
   so TYPO3 picks up the new TCA and SQL definitions.
#. **Apply database schema** *(with-items elements only)* — Admin Tools >
   Maintenance > Analyze Database Structure > apply changes. Skipping
   this step leaves the new column missing from the database, causing a
   ``SQL error: Unknown column '...' in 'INSERT INTO'`` when saving.
#. **Verify** — the element should now appear in the "New Content
   Element" wizard under the chosen group.

Customising the generated element
-----------------------------------

- **FlexForm** — edit ``Configuration/FlexForms/<Name>.xml`` to add or
  change display settings.
- **Child record fields** — extend
  ``Configuration/TCA/tx_conn_t3kit_elements_domain_model_<name>_item.php`` and
  ``ext_tables.sql``, then re-run database analysis.
- **Data Processor** — extend ``resolveSettings()`` and the ``$rows``
  enrichment in the generated processor.
- **Template** — edit the Fluid template and partial freely.
- **SCSS** — edit ``_<name>.scss``; uses ``var(--conn-*)`` design tokens
  for consistent theming.
- **TypeScript** — edit ``<name>.ts``; the ``registry.register()`` call
  at the bottom wires it to the DOM.
- **Icon** — replace ``Resources/Public/Icons/<name>-ce.svg`` with your
  SVG (16×16 viewBox recommended).
