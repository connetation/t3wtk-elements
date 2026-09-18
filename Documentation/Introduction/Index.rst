.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. _what-it-does:

What does it do?
=================

Connetation Elements adds a set of Bootstrap-based content elements to a
TYPO3 v14 site, built with `b13/container
<https://packagist.org/packages/b13/container>`__ and requiring
`conn/base <https://packagist.org/packages/conn/base>`__ for shared theme
tokens, ViewHelpers, and Fluid layouts.

.. _responsive-images:

Responsive images
==================

Image-rendering elements use the ``<ce:responsiveImage>`` ViewHelper
(``Connetation\T3kitElements\Classes\ViewHelpers\ResponsiveImageViewHelper``),
which outputs a WebP ``srcset`` ladder with ``sizes``, capped at the
source file's width, honouring the backend image editor's crop. SVG and
GIF files are served unprocessed.

Server-side prerequisites (Install Tool):

- ``[GFX][processor_effects] = true`` (sharpening after downscale is
  required for crisp output)
- ``webp`` must be listed in ``[GFX][imagefile_ext]`` (this is the
  default in modern TYPO3 if the image processor supports it)
- Editors should upload hero images at 2560px width or larger, since the
  srcset ladder is capped at the source file's width.
