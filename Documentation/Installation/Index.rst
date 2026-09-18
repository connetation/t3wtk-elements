.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

Install via Composer::

   composer require conn/t3kit-elements

This requires ``conn/base`` and ``b13/container`` as dependencies; Composer
resolves both automatically.

.. _installation-site-set:

Activating the Set
====================

Add ``conn/t3kit-elements`` as a Set dependency in your site configuration
(:file:`config/sites/<site>/config.yaml`), or depend on it from a project
sitepackage::

   dependencies:
     - conn/base
     - conn/t3kit-elements

.. _installation-build:

Build assets
============

From :file:`vendor/conn/t3kit-elements`::

   npm install
   npm run build        # production build (SCSS + JS)
   npm run dev          # watch mode (SCSS + JS in parallel)
   npm run build:scss   # SCSS only
   npm run build:js     # JS only

Under DDEV::

   ddev exec "cd /var/www/html/vendor/conn/t3kit-elements && npm run build"
