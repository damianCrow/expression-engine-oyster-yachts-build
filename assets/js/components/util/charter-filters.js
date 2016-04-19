'use strict';

define(['jquery'], function ($) {

	var Filters = function Filters() {
		this.$grid = $('#yacht-grid');
		this.$form = $('#filters-form');

		if (this.$form.length > 0) {
			this.init();
		}
	};

	Filters.prototype.filter = function (model, guests, season, destination) {

		this.$grid.find('li').removeClass('hide');

		// filter model
		if (model !== "") {
			this.$grid.find("li[data-model!='" + model + "']").addClass('hide');
		}

		// filter guests
		if (guests !== "") {
			this.$grid.find("li[data-guests!='" + guests + "']").addClass('hide');
		}

		// filter season
		if (season !== "") {
			this.$grid.find("li[data-" + season + "='']").addClass('hide');
		}

		// filter destination
		if (destination !== "") {
			if (season !== "") {
				this.$grid.find("li[data-"+season+"!='" + destination + "']").addClass('hide');
			} else {
				this.$grid.find("li[data-summer!='" + destination + "'][data-winter!='" + destination + "']").addClass('hide');
			}
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
			_this.$filterGuests = $('#filter-guests');
			_this.$filterSeason = $('#filter-season');
			_this.$filterDestination = $('#filter-destination');

			_this.$form.find('.filters-submit').on('click', function (e) {
				e.preventDefault();

				_this.doFilter();
			});
		}
	};

	Filters.prototype.doFilter = function () {
		this.filter(this.$filterModel.val(), this.$filterGuests.val(), this.$filterSeason.val(), this.$filterDestination.val());
	};

	return Filters;
});
//# sourceMappingURL=charter-filters.js.map
