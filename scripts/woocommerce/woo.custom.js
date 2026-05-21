jQuery(document).ready(function ($) {
  function update_cart(res) {
    $(document.body).trigger("wc_fragment_refresh");

    const $cartCount = $(".cart-count");
    const $miniCart = $(".mini-cart-wrapper");
    $miniCart
      .find(".content")
      .html(res.fragments["div.widget_shopping_cart_content"]);
    $cartCount.each(function () {
      var $this = $(this);

      $this.html(res.fragments[".cart-count"]);
    });
  }

  $(document.body).on("click", ".quantity button", function (e) {
    e.preventDefault();
    const button = $(this);
    const input = button.closest(".quantity").find("input");
    let value = parseInt(input.val());
    if (button.hasClass("pd-qty-minus") && value > 1) {
      value--;
      input.val(value);
      input.trigger("change");
    } else if (button.hasClass("pd-qty-plus")) {
      value++;
      input.val(value);
      input.trigger("change");
    }
  });

  // if ($('.variations select').length) {
  //     let allSelected = true;

  //     $('.variations select').each(function() {
  //         if ($(this).val() === '' || $(this).val() === null) {
  //             // Chọn option đầu tiên có giá trị hợp lệ (bỏ qua placeholder)
  //             let firstValidOption = $(this).find('option[value!=""]').first();
  //             if (firstValidOption.length) {
  //                 $(this).val(firstValidOption.val());
  //             } else {
  //                 allSelected = false; // Không có option hợp lệ
  //             }
  //         }
  //     });

  //     // Nếu tất cả dropdown đều có giá trị, kích hoạt sự kiện
  //     if (allSelected) {
  //         $('.variations select').trigger('change');
  //         $('.variations_form').trigger('woocommerce_variation_select_change');
  //         $('.variations_form').trigger('check_variations');
  //     }
  // }

  //Single Product
  class AddToCartHandler {
    constructor() {
      this.initEventListeners();
    }

    initEventListeners() {
      $(document).on("submit", "form.cart", (e) => this.handleSubmit(e));
    }

    handleSubmit(e) {
      e.preventDefault();
      const $form = $(e.currentTarget);
      const button = $form.find(".btn-add-cart");
      const productId = $form.find('input[name="product_id"]').val();
      const variationId = $form.find('input[name="variation_id"]').val() || 0;
      const quantity = $form.find('input[name="quantity"]').val() || 1;

      const $product_id = variationId ? variationId : productId;
      let data = {
        action: "woocommerce_add_to_cart",
        product_id: $product_id,
        quantity: quantity,
      };

      // if (variationId && variationId !== '0') {
      //     data.variation_id = variationId;
      //     $form.find('select[name^="attribute_"]').each(function() {
      //         data[$(this).attr('name')] = $(this).val();
      //     });
      // }
      const $miniCart = $(".mini-cart-wrapper");

      $.ajax({
        url: wc_add_to_cart_params.ajax_url,
        type: "POST",
        data: data,
        beforeSend: function () {
          button.prop("disabled", true);
          button
            .find(".icon")
            .html('<i class="fa-solid fa-spinner-third fa-spin"></i>'); // ThĂªm spinner
        },
        success: function (res) {
          update_cart(res);
        },
        complete: function (res) {
          const data = res.responseJSON;

          if (data && data.fragments) {
            $.each(data.fragments, function (key, value) {
              $(key).replaceWith(value);
            });

            $(document.body).trigger("added_to_cart", [
              data.fragments,
              data.cart_hash,
              button,
            ]);
          }
          showStatusNotification(
            "success",
            "Thêm vào giỏ hàng thành công",
            "",
            1000,
          );

          button.prop("disabled", false);
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
          $form
            .find(".single_add_to_cart_button")
            .html('<i class="fal fa-cart-plus"></i>')
            .prop("disabled", false);
        },
      });
    }
  }
  jQuery(document.body).on("wc_fragments_refreshed", function () {
    console.log("Fragments đã refresh");
  });
  new AddToCartHandler();

  // ── Variation Radio Handler (custom swatches inside .variations_form) ──
  const $variationsForm = $(".variations_form");
  if ($variationsForm.length) {
    const allVariations = $variationsForm.data("product_variations") || [];

    $(document).on("change", ".js-variation-radio", function () {
      const $radio = $(this);
      const $list = $radio.closest(".pd-attr-list");
      const attrName = $radio.data("attribute_name");

      // Toggle active class
      $list.find(".pd-attr-item").removeClass("is-active");
      $radio.closest(".pd-attr-item").addClass("is-active");

      // Sync radio → hidden select
      const $select = $variationsForm.find(
        'select[data-attribute_name="' + attrName + '"]',
      );
      if ($select.length && $select.val() !== $radio.val()) {
        $select.val($radio.val()).trigger("change");
      }

      // Trigger WooCommerce variation check
      $variationsForm.trigger("woocommerce_variation_select_change");
      $variationsForm.trigger("check_variations");

      // Collect selected attributes for price update
      const selected = {};
      $(".pd-attr-list").each(function () {
        const attr = $(this).data("attribute");
        const val = $(this).find(".js-variation-radio:checked").val();
        if (val) selected[attr] = val;
      });

      // Find matching variation & update price
      const match = allVariations.find((v) => {
        return Object.keys(selected).every((key) => {
          return (
            v.attributes[key] === "" || v.attributes[key] === selected[key]
          );
        });
      });

      if (match && match.price_html) {
        $(".pd-price .price").html(match.price_html);
      }
    });

    // Auto-select first variation on load
    $(".pd-attr-list").each(function () {
      const $first = $(this).find(".js-variation-radio").first();
      if (
        $first.length &&
        !$(this).find(".js-variation-radio:checked").length
      ) {
        $first.prop("checked", true).trigger("change");
      }
    });
  }

  $(document.body).on("change", "td.product-quantity input.qty", function (e) {
    updateCart();
    console.log("change");
  });

  function updateCart() {
    let button = $('button[name="update_cart"]');
    button.attr("disabled", false);
    button.trigger("click");
  }
  $(document).on("click", ".pd-btn-installment", function (e) {
    const $price = $(document).find(".pd-price").html();
    const $thumb = $(document).find(".pd-main-swiper .swiper-slide").first();
    const $thumbImg = $thumb.find("img").attr("src");
    $("#popup-form-mua-hang").find(".price-product").html($price);
    $("#popup-form-mua-hang")
      .find("#main-image-product")
      .attr("src", $thumbImg);
  });
  $("form.variations_form").on("hide_variation", function () {
    $(".pd-badge-discount").hide();
  });
  $("form.variations_form").on("show_variation", function (event, variation) {
    updateSalePercent(variation);
  });

  function updateSalePercent(variation) {
    const salePrice = parseFloat(
      variation.display_price || variation.sale_price,
    );
    const regularPrice = parseFloat(
      variation.display_regular_price || variation.regular_price,
    );
    console.log(salePrice);
    console.log(regularPrice);

    if (!regularPrice || !salePrice || salePrice >= regularPrice) {
      $(".pd-badge-discount").hide();
      return;
    }

    const salePercent = Math.round(
      ((regularPrice - salePrice) / regularPrice) * 100,
    );

    $(".pd-badge-discount")
      .text("-" + salePercent + "%")
      .show();
  }
  function syncVariationUI() {
    $("form.variations_form select").each(function () {
      const attrName = $(this).data("attribute_name");
      const value = $(this).val();

      if (!attrName) return;

      const $list = $('.pd-attr-list[data-attribute="' + attrName + '"]');

      $list.find(".pd-attr-item").removeClass("is-active");

      if (value) {
        $list
          .find('input[value="' + value + '"]')
          .closest(".pd-attr-item")
          .addClass("is-active");
      }
    });
  }

  // init
  $(document).ready(function () {
    syncVariationUI();
  });

  // khi Woo update variation
  $("form.variations_form").on(
    "woocommerce_update_variation_values",
    function () {
      syncVariationUI();
    },
  );

  // ══════════════════════════════════════════════════════════════════
  // ── AJAX PRODUCT FILTER ──────────────────────────────────────────
  // ══════════════════════════════════════════════════════════════════

  var ProductFilter = {
    // Store active filters
    activeFilters: {}, // { 'brand': 'yamaha', 'pa_mau-sac': 'den', ... }
    activePrice: null, // { min: 0, max: 10000000 }
    activeOrderby: "",
    currentPage: 1,
    isLoading: false,

    // Keys reserved (not treated as taxonomy filters in URL)
    reservedKeys: ["min_price", "max_price", "orderby", "paged", "price_label"],

    init: function () {
      var self = this;

      // Only run on shop/archive pages
      if (!$(".js-products-wrapper").length) return;

      // ── Read URL on load → restore filters ──
      self.readURL();

      // ── Filter link click ──
      $(document).on("click", ".js-filter-link", function (e) {
        e.preventDefault();
        var $link = $(this);
        var filterType = $link.data("filter-type");

        if (filterType === "price") {
          self.handlePriceFilter($link);
        } else {
          self.handleTaxFilter($link);
        }

        // Close dropdown
        $link
          .closest(".js-filter-dropdown")
          .find(".product-filter__panel")
          .removeClass("is-open");
        $link
          .closest(".js-filter-dropdown")
          .find(".product-filter__trigger")
          .attr("aria-expanded", "false");

        // Reset to page 1 & fetch
        self.currentPage = 1;
        self.fetchProducts(false);
      });

      // ── Orderby click (from orderby.php template) ──
      $(document).on(
        "click",
        ".woocommerce-ordering .product-filter__panel a",
        function (e) {
          e.preventDefault();
          var $link = $(this);
          var orderby = $link.data("orderby");
          if (orderby) {
            self.activeOrderby = orderby;
            self.currentPage = 1;

            // Update trigger text & active state
            var $ordering = $link.closest(".woocommerce-ordering");
            $ordering.find(".product-filter__trigger span").text($link.text());
            $ordering
              .find(".product-filter__panel li")
              .removeClass("is-active");
            $link.closest("li").addClass("is-active");

            // Close dropdown
            $ordering.find(".product-filter__panel").removeClass("is-open");
            $ordering
              .find(".product-filter__trigger")
              .attr("aria-expanded", "false");

            self.fetchProducts(false);
          }
        },
      );

      // ── Load More ──
      $(document).on("click", ".js-load-more", function (e) {
        e.preventDefault();
        if (self.isLoading) return;
        self.currentPage++;
        self.fetchProducts(true); // append = true
      });

      // ── Remove filter tag ──
      $(document).on("click", ".js-filter-tag-remove", function (e) {
        e.preventDefault();
        var key = $(this).data("filter-key");

        if (key === "price") {
          self.activePrice = null;
          $(".js-filter-link[data-filter-type='price']")
            .closest("li")
            .removeClass("is-active");
        } else {
          delete self.activeFilters[key];
          $(".js-filter-link[data-attribute='" + key + "']")
            .closest("li")
            .removeClass("is-active");
        }

        self.renderFilterTags();
        self.currentPage = 1;
        self.fetchProducts(false);
      });

      // ── Clear all filters ──
      $(document).on("click", ".js-filter-clear-all", function (e) {
        e.preventDefault();
        self.activeFilters = {};
        self.activePrice = null;
        self.activeOrderby = "";
        self.currentPage = 1;

        $(".js-filter-link").closest("li").removeClass("is-active");
        self.renderFilterTags();
        self.fetchProducts(false);
      });

      // ── Browser back/forward ──
      $(window).on("popstate", function () {
        self.readURL();
        self.fetchProducts(false, true); // skipPushState = true
      });
    },

    // ── URL → State ────────────────────────────────────────────────
    readURL: function () {
      var params = new URLSearchParams(window.location.search);

      // Reset state
      this.activeFilters = {};
      this.activePrice = null;
      this.activeOrderby = "";
      this.currentPage = 1;

      // Read price
      var minPrice = params.get("min_price");
      var maxPrice = params.get("max_price");
      var priceLabel = params.get("price_label");
      if (minPrice || maxPrice) {
        this.activePrice = {
          min: minPrice ? parseFloat(minPrice) : "",
          max: maxPrice ? parseFloat(maxPrice) : "",
          label: priceLabel || minPrice + " - " + maxPrice,
        };
      }

      // Read orderby
      var orderby = params.get("orderby");
      if (orderby) {
        this.activeOrderby = orderby;
        // Update orderby trigger text
        var $orderbyLink = $(
          ".woocommerce-ordering .product-filter__panel a[data-orderby='" +
            orderby +
            "']",
        );
        if ($orderbyLink.length) {
          $(".woocommerce-ordering .product-filter__trigger span").text(
            $orderbyLink.text(),
          );
        }
      }

      // Read paged
      var paged = params.get("paged");
      if (paged) {
        this.currentPage = parseInt(paged) || 1;
      }

      // Read taxonomy filters (everything else)
      var self = this;
      params.forEach(function (value, key) {
        if (self.reservedKeys.indexOf(key) === -1 && value) {
          self.activeFilters[key] = value;
        }
      });

      // Sync UI: activate matching links
      this.syncUIFromState();
      this.renderFilterTags();
    },

    // ── State → URL ────────────────────────────────────────────────
    updateURL: function () {
      var params = new URLSearchParams();

      // Taxonomy filters
      $.each(this.activeFilters, function (key, value) {
        params.set(key, value);
      });

      // Price
      if (this.activePrice) {
        if (this.activePrice.min !== "" && this.activePrice.min !== undefined) {
          params.set("min_price", this.activePrice.min);
        }
        if (this.activePrice.max !== "" && this.activePrice.max !== undefined) {
          params.set("max_price", this.activePrice.max);
        }
        if (this.activePrice.label) {
          params.set("price_label", this.activePrice.label);
        }
      }

      // Orderby
      if (this.activeOrderby) {
        params.set("orderby", this.activeOrderby);
      }

      // Page (only if > 1)
      if (this.currentPage > 1) {
        params.set("paged", this.currentPage);
      }

      var qs = params.toString();
      var newUrl = window.location.pathname + (qs ? "?" + qs : "");

      history.pushState({ filters: true }, "", newUrl);
    },

    // ── Sync UI active states from internal state ──────────────────
    syncUIFromState: function () {
      // Clear all active states first
      $(".js-filter-link").closest("li").removeClass("is-active");

      // Activate taxonomy filter links
      var self = this;
      $.each(this.activeFilters, function (key, value) {
        $(
          ".js-filter-link[data-attribute='" +
            key +
            "'][data-value='" +
            value +
            "']",
        )
          .closest("li")
          .addClass("is-active");
      });

      // Activate price link
      if (this.activePrice) {
        $(".js-filter-link[data-filter-type='price']").each(function () {
          var $link = $(this);
          if (
            $link.data("min-price") == self.activePrice.min &&
            $link.data("max-price") == self.activePrice.max
          ) {
            $link.closest("li").addClass("is-active");
          }
        });
      }
    },

    handlePriceFilter: function ($link) {
      var minPrice = $link.data("min-price");
      var maxPrice = $link.data("max-price");

      // Toggle – if same price is clicked again, remove it
      if (
        this.activePrice &&
        this.activePrice.min == minPrice &&
        this.activePrice.max == maxPrice
      ) {
        this.activePrice = null;
        $link.closest("li").removeClass("is-active");
      } else {
        // Deactivate all price links, activate this one
        $(".js-filter-link[data-filter-type='price']")
          .closest("li")
          .removeClass("is-active");
        $link.closest("li").addClass("is-active");
        this.activePrice = {
          min: minPrice,
          max: maxPrice,
          label: $link.text(),
        };
      }

      this.renderFilterTags();
    },

    handleTaxFilter: function ($link) {
      var attribute = $link.data("attribute");
      var value = $link.data("value");

      if (!attribute || !value) return;

      // Toggle – click same filter again removes it
      if (this.activeFilters[attribute] === value) {
        delete this.activeFilters[attribute];
        $link.closest("li").removeClass("is-active");
      } else {
        // Deactivate sibling links within same panel
        $link
          .closest(".product-filter__panel")
          .find("li")
          .removeClass("is-active");
        $link.closest("li").addClass("is-active");
        this.activeFilters[attribute] = value;
      }

      this.renderFilterTags();
    },

    renderFilterTags: function () {
      var $container = $(".js-filter-tags");
      if (!$container.length) return;

      var html = "";
      var hasFilters = false;

      // Taxonomy filters
      $.each(this.activeFilters, function (key, value) {
        hasFilters = true;
        var $activeLink = $(
          ".js-filter-link[data-attribute='" +
            key +
            "'][data-value='" +
            value +
            "']",
        );
        var label = $activeLink.length ? $activeLink.text() : value;

        html += '<span class="product-filter__tag">';
        html += '<span class="product-filter__tag-label">' + label + "</span>";
        html +=
          '<button class="product-filter__tag-close js-filter-tag-remove" data-filter-key="' +
          key +
          '" type="button">';
        html += '<i class="fa-regular fa-xmark"></i>';
        html += "</button>";
        html += "</span>";
      });

      // Price filter
      if (this.activePrice) {
        hasFilters = true;
        html += '<span class="product-filter__tag">';
        html +=
          '<span class="product-filter__tag-label">' +
          this.activePrice.label +
          "</span>";
        html +=
          '<button class="product-filter__tag-close js-filter-tag-remove" data-filter-key="price" type="button">';
        html += '<i class="fa-regular fa-xmark"></i>';
        html += "</button>";
        html += "</span>";
      }

      // Clear all button
      if (hasFilters) {
        html +=
          '<button class="product-filter__clear js-filter-clear-all" type="button">Xóa tất cả</button>';
      }

      $container.html(html);
    },

    fetchProducts: function (append, skipPushState) {
      var self = this;
      if (self.isLoading) return;

      var $wrapper = $(".js-products-wrapper");
      var $grid = $wrapper.find("#list-product");
      var $loadMore = $(".js-load-more");
      var categoryId =
        $wrapper.data("category-id") ||
        $("input[name='currentcategory']").val() ||
        0;

      var data = {
        action: "filter_products",
        nonce: vanphucmusic_ajax.nonce,
        paged: self.currentPage,
        category_id: categoryId,
        orderby: self.activeOrderby,
        filters: self.activeFilters,
      };

      if (self.activePrice) {
        data.min_price = self.activePrice.min;
        data.max_price = self.activePrice.max;
      }

      // Push state to URL (unless triggered by popstate)
      if (!skipPushState) {
        self.updateURL();
      }

      self.isLoading = true;

      // UI loading state
      if (!append) {
        $wrapper.addClass("is-loading");
      }
      $loadMore
        .prop("disabled", true)
        .html('<i class="fa-solid fa-spinner-third fa-spin"></i> Đang tải...');

      $.ajax({
        url: vanphucmusic_ajax.url,
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success && response.data) {
            if (append) {
              $grid.append(response.data.products);
            } else {
              $grid.html(response.data.products);
            }

            // Update load-more button state
            $loadMore
              .data("current-page", response.data.current_page)
              .data("max-pages", response.data.max_pages);

            if (response.data.current_page >= response.data.max_pages) {
              $loadMore.hide();
            } else {
              $loadMore.show();
            }

            // Re-init lozad for lazy images
            if (window.refreshLazyload) {
              window.refreshLazyload();
            }

            // Scroll to top of grid on filter change (not load-more)
            if (!append && $wrapper.length) {
              $("html, body").animate(
                {
                  scrollTop: $wrapper.offset().top - 120,
                },
                400,
              );
            }
          }
        },
        error: function (xhr, status, error) {
          console.error("Filter AJAX Error:", status, error);
        },
        complete: function () {
          self.isLoading = false;
          $wrapper.removeClass("is-loading");
          $loadMore.prop("disabled", false).html("Xem thêm");
        },
      });
    },
  };

  // Initialize filter on page load
  ProductFilter.init();

  // If URL has filter params on load, trigger initial AJAX fetch
  if (window.location.search && $(".js-products-wrapper").length) {
    var params = new URLSearchParams(window.location.search);
    var hasFilterParams = false;
    params.forEach(function (value, key) {
      if (key !== "paged") hasFilterParams = true;
    });
    if (hasFilterParams) {
      ProductFilter.fetchProducts(false, true); // skipPushState = true
    }
  }
});
