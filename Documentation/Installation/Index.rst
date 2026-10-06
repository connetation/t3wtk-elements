.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

Install via Composer::

   composer require connetation/t3wtk-elements

This requires ``connetation/t3wtk-base`` and ``b13/container`` as dependencies; Composer
resolves both automatically.

.. _installation-site-set:

Activating the Set
====================

Add ``connetation/t3wtk-elements`` as a Set dependency in your site configuration
(:file:`config/sites/<site>/config.yaml`), or depend on it from a project
sitepackage::

   dependencies:
     - connetation/t3wtk-base
     - connetation/t3wtk-elements

.. _installation-build:

Build assets
============

From :file:`vendor/connetation/t3wtk-elements`::

   npm install
   npm run build        # production build (SCSS + JS)
   npm run dev          # watch mode (SCSS + JS in parallel)
   npm run build:scss   # SCSS only
   npm run build:js     # JS only

Under DDEV::

   ddev exec "cd /var/www/html/vendor/connetation/t3wtk-elements && npm run build"
