import $ from 'jquery'

export default class CharterFilters {
  constructor() {
    this.grid = $('#yacht-grid')
    this.form = $('#filters-form')

    if (this.form.length > 0) {
      this.init()
    }
  }

  init() {
    if (this.form.attr('data-post') === "true") {
      $('.filters-submit').on('click', function (e) {
        e.preventDefault()

        this.form.submit()
      })
    } else {
      this.filterModel = $('#filter-model')
      this.filterGuests = $('#filter-guests')
      this.filterSeason = $('#filter-season')
      this.filterDestination = $('#filter-destination')

      this.form.find('.filters-submit').on('click', (e) => {
        e.preventDefault()
        this.doFilter()
      })
    }
  }

  filter(model, guests, season, destination) {
    this.grid.find('li').removeClass('hide')

    // filter model
    if (model !== '') {
      this.grid.find("li[data-model!='" + model + "']").addClass('hide')
    }

    // filter guests
    if (guests !== '') {
      //this.grid.find("li[data-guests!='" + guests + "']").addClass('hide')
      this.grid.find('li').filter(yacht => parseInt($(yacht).data('guests')) < parseInt(guests)).addClass('hide')
    }

    // filter season
    if (season !== '') {
      this.grid.find("li[data-" + season + "='']").addClass('hide')
    }

    // filter destination
    if (destination !== '') {
      if (season !== '') {
        this.grid.find("li[data-" + season + "!='" + destination + "']").addClass('hide')
      } else {
        this.grid.find("li[data-summer!='" + destination + "'][data-winter!='" + destination + "']").addClass('hide')
      }
    }

    if (this.grid.find('li').length === this.grid.find('li.hide').length) {
      this.grid.find('.no-results').addClass('no-results--show')
    } else {
      this.grid.find('.no-results').removeClass('no-results--show')
    }
  }

  doFilter() {
    this.filter(this.filterModel.val(), this.filterGuests.val(), this.filterSeason.val(), this.filterDestination.val())
  }
}
