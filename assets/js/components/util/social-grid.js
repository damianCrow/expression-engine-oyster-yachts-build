'use strict';

define(['jquery', './social-mock-data'], function ($, socialItems) {

	/**
  * Social Item object
  * @param {String} platform Social media platform ("youtube", "instagram", "twitter")
  * @param {[type]} data     Social media item data
  */
	var SocialItem = function SocialItem(platform, data) {
		// social platform
		this.platform = platform;

		// social data for this item
		this.data = data;

		// is this slide on the page
		this.active = false;

		// jQuery object of the slide
		this.$el = this.buildElement(data);
	};

	/**
  * Build DOM element based on social media platform
  * @return {jQuery object} social item block
  */
	SocialItem.prototype.buildElement = function () {
		// youtube
		if (this.platform === "youtube") {
			return $('<a target="_blank" href="' + this.data.url + '" style="background-image:url(' + this.data.image + ')" class="social-slide social-slide-youtube"><i class="fa fa-youtube-play"></i></a>');

			// instagram
		} else if (this.platform === "instagram") {
				return $('<a target="_blank" href="' + this.data.url + '" style="background-image:url(' + this.data.image + ')" class="social-slide social-slide-instagram"><i class="fa fa-instagram"></i></a>');

				// twitter
			} else if (this.platform === "twitter") {
					return $('<a target="_blank" href="' + this.data.url + '" class="social-slide twitter social-slide-twitter"><i class="fa fa-twitter"></i><p>' + this.data.tweet + '</p></a>');
				}
	};

	/**
  * Social Grid object
  */
	var SocialGrid = function SocialGrid() {
		// if no social items then bail
		if (typeof socialItems === 'undefined') return;

		var _this = this;

		// select all slide blocks from the DOM
		_this.$el = $('.social-slider');

		// list of social items
		_this.items = [];

		// populate the list of social items with SocialItem objects
		$.each(socialItems, function (platform, platformItems) {
			$.each(platformItems, function (i, platformItem) {
				_this.items.push(new SocialItem(platform, platformItem));
			});
		});

		// iterate over all DOM slide blocks, adding initial slides
		$.each(_this.$el, function (i, $block) {
			// get a new SocialItem index which isn't active
			var index = _this.getNewIndex();

			// append slide to DOM
			_this.items[index].$el.appendTo($block);

			// start loop
			_this.newItem($block, index);
		});
	};

	/**
  * Get a SocialItem which is not active
  * @return {int} index of new slide
  */
	SocialGrid.prototype.getNewIndex = function () {
		// get random number from 0 to this.items.length-1
		var index = Math.floor(Math.random() * this.items.length);

		// if the slide at the new index is active then get another index
		if (this.items[index].active === true) return this.getNewIndex();

		// set SocialItem at new index to active
		this.items[index].active = true;

		return index;
	};

	/**
  * Add new slide to block
  * @param  {jQuery object} $block       DOM block we're dealing with
  * @param  {int} oldItemIndex old SocialItem index of the block
  */
	SocialGrid.prototype.newItem = function ($block, oldItemIndex) {
		var _this = this;

		// get random delay
		var delay = Math.floor(Math.random() * 16000) + 10000;

		// get new slide after random time is up
		setTimeout(function () {
			// get a new SocialItem index which isn't active
			var newItemIndex = _this.getNewIndex();

			// add class to old item => adds z-index above new slide and fades opacity to 0
			_this.items[oldItemIndex].$el.addClass('fade-block');

			// add new item to block
			_this.items[newItemIndex].$el.appendTo($block);

			// give time for old slide to animate out
			setTimeout(function () {
				// detach the old slide and remove the animation class
				_this.items[oldItemIndex].$el.removeClass('fade-block').detach();

				// set old SocialItem to inactive
				_this.items[oldItemIndex].active = false;

				// recursively get new item
				_this.newItem($block, newItemIndex);
			}, 1000);
		}, delay);
	};

	return SocialGrid;
});
//# sourceMappingURL=social-grid.js.map
