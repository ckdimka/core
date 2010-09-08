/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 */

/**
 * Controller
 */

function ProductDetailsController(base)
{
  this.callSupermethod('constructor', arguments);

  if (this.base.get(0).name && this.base.get(0).name.search(/add_to_cart_([0-9]+)/) != -1) {
    var m = this.base.get(0).name.match(/add_to_cart_([0-9]+)/);
    this.productId = m[1];

    this.block = new ProductDetailsView(this.base, this.productId);

    var o = this;

    core.bind(
      'updateCart',
      function(event, data) {
        if (!o.selfAdded) {
          for (var i = 0; i < data.items.length; i++) {
            if (data.items[i].object_type == 'product' && data.items[i].object_id == o.productId) {
              o.block.load();
            }
          }
        }
      }
    );
  }
}

extend(ProductDetailsController, AController);

// Prodiuct id
ProductDetailsController.prototype.productId = null;

// Controller name
ProductDetailsController.prototype.name = 'ProductDetailsController';

// Find pattern
ProductDetailsController.prototype.findPattern = 'form.product-details';

// Controller associated main widget
ProductDetailsController.prototype.block = null;

// Controller associated buttons block widget
ProductDetailsController.prototype.buttonsBlock = null;

ProductDetailsController.prototype.selfAdded = false;

// Initialize controller
ProductDetailsController.prototype.initialize = function()
{
  var o = this;

  this.base.bind(
    'reload',
    function(event, box) {
      o.bind(box);
    }
  );
}

/**
 * Main widget
 */

function ProductDetailsView(base, productId)
{
  this.callSupermethod('constructor', arguments);

  this.productId = productId;

  this.linkClickHandler = function(event)
  {
    event.stopPropagation();

    $('.product-details .gallery li.selected a').eq(0).trigger('click');

    return false;
  }
}

extend(ProductDetailsView, ALoadable);

// Prodiuct id
ProductDetailsView.prototype.productId = null;

// Shade widget
ProductDetailsView.prototype.shadeWidget = true;

// Widget target
ProductDetailsView.prototype.widgetTarget = 'product';

// Widget class name
ProductDetailsView.prototype.widgetClass = '\\XLite\\View\\Product';

// Imgaes gallery
ProductDetailsView.prototype.gallery = null;

// Postprocess widget
ProductDetailsView.prototype.postprocess = function(isSuccess, initial)
{
  this.callSupermethod('postprocess', arguments);

  if (isSuccess) {

    // Fix box width
    $('.image .image-center', this.base)
      .css('width', $('.image .image-center img', this.base).attr('width') + 'px');

    // Save gallery list items
    this.gallery = $('.image .gallery li', this.base);

    var o = this;

    // Arrow-based image navigation
    $('.image .left-arrow', this.base).click(
      function (event) {
        o.switchImage(-1);
      }
    );

    $('.image .right-arrow', this.base).click(
      function (event) {
        o.switchImage(1);
      }
    );

    // Form AJAX-based submit
    this.base.eq(0).bind(
      'submit',
      function(event)
      {
        return o.submitForm(event, this);
      }
    )

    // Cloud zoom
    if (!initial) {
      var cloud = $('.cloud-zoom', this.base);

      if (cloud.length && !cloud.data('zoom')) {
        cloud.CloudZoom();
      }
    }

    // Gallery
    if (typeof(window.lightBoxImagesDir) != 'undefined') {
      $('.gallery a', this.base).lightBox(
        {
          fixedNavigation: true,
          imageLoading:    lightBoxImagesDir + '/lightbox-ico-loading.gif',
          imageBtnPrev:    lightBoxImagesDir + '/lightbox-btn-prev.gif',
          imageBtnNext:    lightBoxImagesDir + '/lightbox-btn-next.gif',
          imageBtnClose:   lightBoxImagesDir + '/lightbox-btn-close.gif',
          imageBlank:      lightBoxImagesDir + '/lightbox-blank.gif'
        }
      );
    }
  }
}

// Get base element for shade / unshade operation
ProductDetailsView.prototype.getShadeBase = function() {
  return this.base.parents('#content').eq(0);
}

// Image gallery switcher
ProductDetailsView.prototype.switchImage = function(diff)
{
  var selected = -1;
  var i = 0;

  // Detect current index
  this.gallery.each(
      function() {
        if (selected == -1 && $(this).hasClass('selected')) {
          selected = i;
        }
        i++;
      }
  );

  if (selected == -1) {
    selected = 0;
  }

  // Calculate new position
  var next = selected + diff;

  if (next < 0) {
    next = this.gallery.length - Math.abs(next) % this.gallery.length;

  } else if (next >= this.gallery.length) {
    next = next % this.gallery.length;
  }

  this.gallery.removeClass('selected');

  // Refresh main image and another options + cloud zoom plugin restart
  next = this.gallery.eq(next);
  next.addClass('selected');

  var cloud = $('.cloud-zoom', this.base);

  if (cloud.data('zoom')) {
    cloud.data('zoom').destroy();

  } else {
    cloud.unbind('click', this.linkClickHandler);
  }

  cloud.attr('href', $('a', next).attr('href'));

  var middle = $('img.middle', next).eq(0)

  $('img', cloud)
    .attr('src', middle.attr('src'))
    .attr('width', middle.attr('width'))
    .attr('height', middle.attr('height'));

  $('.image .image-center', this.base)
    .css('width', middle.attr('width') + 'px');

  eval('var tmp = {' + $('a', next).attr('rel') + '}');

  if (tmp.width > middle.attr('width') || tmp.height > middle.attr('height')) {
    cloud.CloudZoom();

  } else {
    cloud.click(this.linkClickHandler);
  }
}

// Get additional parameters
ProductDetailsView.prototype.getParams = function(params)
{
  params = this.callSupermethod('getParams', arguments);

  params.product_id = this.productId;
  params.added = 1;

  return params;
}

// Form submit handler
ProductDetailsView.prototype.submitForm = function(event, form)
{
  if ('undefined' == typeof(form.validate) || form.validate()) {
    this.shade();
    this.base.get(0).controller.selfAdded = true;

    form = $(form);

    var o = this;

    core.post(
      form.attr('action'),
      form.serialize(),
      function (XMLHttpRequest, textStatus, data, isValid)
      {
        return o.postprocessAdd2Cart(XMLHttpRequest, textStatus, data, isValid);
      }
    );
  }

  return false;
}

// Form POST processor
ProductDetailsView.prototype.postprocessAdd2Cart = function(XMLHttpRequest, textStatus, data, isValid)
{
  this.base.get(0).controller.selfAdded = false;

  if (isValid) {
    this.load();

  } else {
    this.unshade();
  }
}

core.autoload(ProductDetailsController);