jQuery(document).ready(function ($) {
  $("#area-shop-parent").change(function () {
    var areaParent = $(this).val();
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "get_shop_areas",
        parent: areaParent,
      },
      beforeSend: function () {
        $("#area-shop-child").addClass("loading");
      },
      success: function (response) {
        $("#area-shop-child").html(response.data.html);
        $("#area-shop-child").removeClass("loading");
      },
      error: function (error) {
        $("#area-shop-child").removeClass("loading");
        console.log(error);
      },
    });
  });

  $("form.system-select").submit(function (e) {
    e.preventDefault();
    var category = $("#category-shop").val();
    var areaParent = $("#area-shop-parent").val();
    var areaChild = $("#area-shop-child").val();
    var lang = $("input[name='lang']").val();
    var data = {
      action: "get_shop_data",
      category: category,
      areaParent: areaParent,
      areaChild: areaChild,
      lang: lang,
    };

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: data,
      beforeSend: function () {
        $("#shop-list").addClass("loading");
      },
      success: function (response) {
        if (response.data.no_result) {
          $("#shop-list").html(response.data.html);
          $("#map-iframe").html("");

          $("#shop-list").removeClass("loading");
        } else {
          $("#shop-list").html(response.data.html);
          $("#map-iframe").html(response.data.map_iframe_first);
          $("#shop-list").removeClass("loading");
        }
      },
      error: function (error) {
        $("#shop-list").removeClass("loading");
        console.log(error);
      },
    });
  });
  $(document).on("click", "#shop-list .item", function () {
    var iframe = $(this).data("iframe");
    $("#map-iframe").html(iframe);
    $("#map-iframe").addClass("loading");
    setTimeout(function () {
      $("#map-iframe").removeClass("loading");
    }, 1000);
  });

  $(".attribute-options li").on("click", function () {
    var $this = $(this);
    var value = $this.data("value");
    var attributeName = $this.closest(".filters").data("attribute");
    var $select = $('select[name="attribute_' + attributeName + '"]');

    // Cập nhật select tương ứng
    $select.val(value).trigger("change");

    // Active state UI
    $this.addClass("active").siblings().removeClass("active");
  });

  // Khi load trang: tự đồng bộ li theo select đang chọn (auto select first)
  $(".filters").each(function () {
    var $this = $(this);
    var attributeName = $this.data("attribute");
    var $select = $('select[name="attribute_' + attributeName + '"]');
    var selectedValue = $select.val();

    if (selectedValue) {
      $this.find("li").removeClass("active");
      $this.find('li[data-value="' + selectedValue + '"]').addClass("active");
    } else {
      // Nếu chưa có chọn -> tự chọn option đầu tiên
      var first = $this.find("li").first();
      first.addClass("active");
      $select.val(first.data("value")).trigger("change");
    }
  });

  // ── Gallery update on variation change ──────────────────────────
  var _origMainSlides = null;
  var _origThumbSlides = null;

  function _getGallerySwipers() {
    var $colLeft = $(".product-detail-1 .col-left");
    var mainEl = $colLeft.find(".main .swiper")[0];
    var thumbEl = $colLeft.find(".thumb .swiper")[0];
    return {
      main: mainEl && mainEl.swiper ? mainEl.swiper : null,
      thumb: thumbEl && thumbEl.swiper ? thumbEl.swiper : null,
    };
  }

  function _refreshLazy($scope) {
    $scope.find(".lozad[data-src]").each(function () {
      $(this).attr("src", $(this).data("src"));
    });
    if (window.refreshLazyload) window.refreshLazyload();
  }

  function _initOriginalGallery() {
    var sw = _getGallerySwipers();
    if (sw.main && !_origMainSlides) {
      _origMainSlides = $(sw.main.wrapperEl).html();
    }
    if (sw.thumb && !_origThumbSlides) {
      _origThumbSlides = $(sw.thumb.wrapperEl).html();
    }
  }

  function restoreOriginalGallery() {
    var sw = _getGallerySwipers();
    if (sw.main && _origMainSlides) {
      $(sw.main.wrapperEl).html(_origMainSlides);
      sw.main.update();
      sw.main.slideTo(0, 0, false);
    }
    if (sw.thumb && _origThumbSlides) {
      $(sw.thumb.wrapperEl).html(_origThumbSlides);
      sw.thumb.update();
      sw.thumb.slideTo(0, 0, false);
    }
    _refreshLazy($(".product-detail-1 .col-left"));
  }

  function updateVariationGallery(variation) {
    if (!variation || !variation.image || !variation.image.src) return;

    _initOriginalGallery();

    var sw = _getGallerySwipers();
    if (!sw.main) return;

    var images = [];
    var mainSrc = variation.image.full_src || variation.image.src;
    if (mainSrc) images.push(mainSrc);

    // Data từ plugin Variation Images Gallery for WooCommerce
    if (Array.isArray(variation.variation_gallery_images)) {
      variation.variation_gallery_images.forEach(function (img) {
        var src = img.full_src || img.url || img.src;
        if (src && src !== mainSrc) images.push(src);
      });
    }

    if (!images.length) return;

    var mainHTML = images
      .map(function (src) {
        return (
          '<div class="swiper-slide"><div class="img"><a class="img-ratio rounded-4" data-fancybox="product-image" data-src="' +
          src +
          '"><img class="lozad" data-src="' +
          src +
          '" alt="" /></a></div></div>'
        );
      })
      .join("");

    var thumbHTML = images
      .map(function (src) {
        return (
          '<div class="swiper-slide"><div class="img"><a class="img-ratio"><img class="lozad" data-src="' +
          src +
          '" alt="" /></a></div></div>'
        );
      })
      .join("");

    $(sw.main.wrapperEl).html(mainHTML);
    sw.main.update();
    sw.main.slideTo(0, 0, false);

    if (sw.thumb) {
      $(sw.thumb.wrapperEl).html(thumbHTML);
      sw.thumb.update();
      sw.thumb.slideTo(0, 0, false);
    }

    _refreshLazy($(".product-detail-1 .col-left"));
  }
  // ─────────────────────────────────────────────────────────────────

  $("form.variations_form").on("show_variation", function (event, variation) {
    console.log(variation);
    updateProductVariation(variation);
    updateVariationGallery(variation);
  });

  $("form.variations_form").on("hide_variation", function () {
    restoreOriginalGallery();
  });

  function updateProductVariation(variation) {
    var titleProduct = variation.title_product;
    var sku = variation.sku;
    var parentSku = variation.parent_sku;
    var is_in_stock = variation.is_in_stock;
    var inStockText = $(".stock-status").data("in-stock-text");
    var outOfStockText = $(".stock-status").data("out-of-stock-text");
    var price = variation.price_html;
    var display_price = variation.display_price;
    var $atribute_title = "";
    $(".attribute-options li.active span").each(function () {
      $atribute_title += $(this).text() + " ";
    });

    const title = titleProduct + " " + $atribute_title;
    if (titleProduct) {
      $(".title-product").html(title);
      $('[name="product-name"]').val(title);
    }
    if (sku) {
      $(".sku-product").text(sku);
    } else if (parentSku) {
      $(".sku-product").text(parentSku);
    }
    if (is_in_stock) {
      $(".stock-status").text(inStockText);
    } else {
      $(".stock-status").text(outOfStockText);
    }
    if (price) {
      $(".price-product").html(price);
      if (display_price > 0) {
        $(".price-product").show();
      } else {
        $(".price-product").hide();
      }
    }
  }
});
