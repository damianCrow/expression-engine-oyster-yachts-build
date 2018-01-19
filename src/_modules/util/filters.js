'use strict';

define(['jquery'], function ($) {

  var Filters = function Filters() {
    this.$grid = $('#yacht-grid');
    this.$form = $('#filters-form');

    if (this.$form.length > 0) {
      this.init();
    }
  };

  Filters.prototype.filter = function (model, oysterType, price, location, status) {
    this.$grid.find('li').removeClass('hide');

    // filter price
    if (price !== "") {
      var priceRange = price.split('-');

      priceRange[0] = parseInt(priceRange[0]);
      priceRange[1] = parseInt(priceRange[1]);

      if (priceRange[0] > 0) {
        this.$grid.find('li').filter(function () {
          return parseInt($(this).data('price')) < priceRange[0];
        }).addClass('hide');
      }

      if (priceRange[1] > 0) {
        this.$grid.find('li').filter(function () {
          return parseInt($(this).data('price')) > priceRange[1];
        }).addClass('hide');
      }
    }

    if (oysterType !== "" || oysterType === true) {
      this.$grid.find("li[data-non-oyster!='" + oysterType + "']").addClass('hide');
    }

    // filter model
    if (model !== "") {
      this.$grid.find("li[data-model!='" + model + "']").addClass('hide');
    }

    // filter location
    if (location !== "") {
      var _this = this;
      var locationList = location.split('|');

      $.each(locationList, function(i, loc) {
        _this.$grid.find("li").not("[data-location*='" + loc + "']").addClass('hide');
      });

    }

    // filter status
    if (status !== "") {
      this.$grid.find("li[data-status!='" + status + "']").addClass('hide');
    }
  };

  Filters.prototype.init = function () {
    var _this = this;

    if (_this.$form.attr('data-post') === "true") {
      $('.filters-submit').on('click', function (e) {
        e.preventDefault();

        _this.$form.submit();
      });
    } else {
      _this.$filterModel = $('#filter-model');
      _this.$filterOysterType = $('#filter-oyster-type-non'),
      _this.$filterPrice = $('#filter-price');
      _this.$filterLocation = $('#filter-location');
      _this.$filterStatus = $('#filter-status');

      _this.$form.find('.filters-submit').on('click', function (e) {
        e.preventDefault();

        _this.doFilter();
      });

      this.queryString(
        _this.$filterModel.val(),
        _this.$filterOysterType.prop('checked'),
        _this.$filterPrice.val(),
        _this.$filterLocation.val(),
        _this.$filterStatus.val()
      );

      window.onpopstate = function(event) {
        _this.filter(
          event.state.model,
          event.state.price,
          event.state.location,
          event.state.status
        );

        _this.$filterModel.val(event.state.model).trigger('change');
        _this.$filterOysterType.prop('checked', event.state.oysterType).trigger('change');
        _this.$filterPrice.val(event.state.price).trigger('change');
        _this.$filterLocation.val(event.state.location).trigger('change');
        _this.$filterStatus.val(event.state.status).trigger('change');
      };
    }
  };

  Filters.prototype.doFilter = function () {
    this.filter(
      this.$filterModel.val(),
      this.$filterOysterType.prop('checked'),
      this.$filterPrice.val(),
      this.$filterLocation.val(),
      this.$filterStatus.val()
    );

    this.queryString(
      this.$filterModel.val(),
      this.$filterOysterType.prop('checked'),
      this.$filterPrice.val(),
      this.$filterLocation.val(),
      this.$filterStatus.val()
    );
  };

  Filters.prototype.queryString = function (model, oysterType, price, location, status) {
    if (history.pushState) {
      var query = [];
      if (model !== "") {
        query.push('model='+encodeURIComponent(model));
      }
      if (oysterType !== "") {
        query.push('oysterType='+encodeURIComponent(oysterType));
      }
      if (price !== "") {
        query.push('price='+encodeURIComponent(price));
      }
      if (location !== "") {
        query.push('location='+encodeURIComponent(location));
      }
      if (status !== "") {
        query.push('status='+encodeURIComponent(status));
      }

      if (query.length > 0) {
        var qs = "?"+query.join('&').replace(/%20/g,'+');

        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + qs;
          window.history.pushState({
            model: model,
            oysterType: oysterType,
            price: price,
            location: location,
            status: status
          },'',newurl);
      }
    }
  };

  return Filters;
});
//# sourceMappingURL=brokerage-filters.js.map
