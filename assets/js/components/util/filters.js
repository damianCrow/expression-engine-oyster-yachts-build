'use strict';

define(['jquery'], function ($) {

	var Filters = function Filters() {
		this.$grid = $('#yacht-grid');
		this.$form = $('#filters-form');

		if (this.$form.length > 0) {
			this.init();
		}
	};

	Filters.prototype.filter = function (model, price, location, status) {
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

		// filter model
		if (model !== "") {
			this.$grid.find("li[data-model!='" + model + "']").addClass('hide');
		}

		// filter location
		if (location !== "") {
			this.$grid.find("li[data-location!='" + location + "']").addClass('hide');
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
			_this.$filterPrice = $('#filter-price');
			_this.$filterLocation = $('#filter-location');
			_this.$filterStatus = $('#filter-status');

			_this.$form.find('.filters-submit').on('click', function (e) {
				e.preventDefault();

				_this.doFilter();
			});
		}
	};

	Filters.prototype.doFilter = function () {
		this.filter(this.$filterModel.val(), this.$filterPrice.val(), this.$filterLocation.val(), this.$filterStatus.val());
	};

	return Filters;
});
//# sourceMappingURL=filters.js.map
