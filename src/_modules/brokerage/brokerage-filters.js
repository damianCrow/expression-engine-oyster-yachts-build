import $ from 'jquery'

export default class BrokerageFilters {
  constructor() {
    this.grid = $('#yacht-grid')
    this.form = $('#filters-form')

    if (this.form.length > 0) {
      this.init()
    }
  }

  init() {
    if (this.form.attr('data-post') === 'true') {
      $('.filters-submit').on('click', (e) => {
        e.preventDefault()
        this.form.submit()
      })
    } else {
      this.$filterModel = $('#filter-model')
      this.$filterModelOther = $('#filter-model-other')
      this.$filterotherType = $('#filter-oyster-type-non')
      this.$filterPrice = $('#filter-price')
      this.$filterLocation = $('#filter-location')
      this.$filterStatus = $('#filter-status')

      this.form.find('.filters-submit').on('click', (e) => {
        e.preventDefault()

        this.doFilter()
      })

      this.doFilter()

      $('input[name=oyster-yacht-or-not]').change(function () {
        if (this.value === 'true') {
          $('.filter-model').show()
          $('.filter-model-other').hide()
        } else {
          $('.filter-model').hide()
          $('.filter-model-other').show()
        }
      })

      window.onpopstate = (event) => {
        this.filter(
          event.state.model,
          event.state.otherType,
          event.state.price,
          event.state.location,
          event.state.status
        )

        event.state.otherType ? this.$filterModelOther.val(event.state.model).trigger('change') : this.$filterModel.val(event.state.model).trigger('change')
        this.$filterotherType.prop('checked', event.state.otherType).trigger('change')
        this.$filterPrice.val(event.state.price).trigger('change')
        this.$filterLocation.val(event.state.location).trigger('change')
        this.$filterStatus.val(event.state.status).trigger('change')

        if (event.state.otherType) {
          $('filter-model').hide()
          $('filter-model-other').show()
        } else {
          $('filter-model').show()
          $('filter-model-other').hide()
        }
      }
    }
  }

  filter(model, otherType, price, location, status) {
    this.grid.find('li').removeClass('hide')

    if (otherType === true) {
      this.grid.find('li').addClass('hide')
      this.grid.find("li[data-non-oyster='true']").removeClass('hide')
    } else {
      this.grid.find("li[data-non-oyster='true']").addClass('hide')
    }

    // filter price
    if (price !== '') {
      const priceRange = price.split('-')

      priceRange[0] = parseInt(priceRange[0])
      priceRange[1] = parseInt(priceRange[1])

      if (priceRange[0] > 0) {
        this.grid.find('li').filter(() => parseInt($(this).data('price')) < priceRange[0]).addClass('hide')
      }

      if (priceRange[1] > 0) {
        this.grid.find('li').filter(() => parseInt($(this).data('price')) > priceRange[1]).addClass('hide')
      }
    }

    // filter model
    if (model !== '') {
      this.grid.find("li[data-model!='" + model + "']").addClass('hide')
    }

    // filter location
    if (location !== '') {
      const locationList = location.split('|')

      $.each(locationList, (i, loc) => {
        this.grid.find('li').not("[data-location*='" + loc + "']").addClass('hide')
      })
    }

    // filter status
    if (status !== '') {
      this.grid.find("li[data-status!='" + status + "']").addClass('hide')
    }

    if (this.grid.find('li').length === this.grid.find('li.hide').length) {
      this.grid.find('.no-results').addClass('no-results--show')
    } else {
      this.grid.find('.no-results').removeClass('no-results--show')
    }
  }

  doFilter() {
    this.filter(
      this.$filterotherType.prop('checked') ? this.$filterModelOther.val() : this.$filterModel.val(),
      this.$filterotherType.prop('checked'),
      this.$filterPrice.val(),
      this.$filterLocation.val(),
      this.$filterStatus.val()
    )

    this.queryString(
      this.$filterotherType.prop('checked') ? this.$filterModelOther.val() : this.$filterModel.val(),
      this.$filterotherType.prop('checked'),
      this.$filterPrice.val(),
      this.$filterLocation.val(),
      this.$filterStatus.val()
    )
  }

  queryString(model, otherType, price, location, status) {
    if (history.pushState) {
      const query = []
      if (model !== '') {
        query.push('model=' + encodeURIComponent(model))
      }
      if (otherType !== '') {
        query.push('otherType=' + encodeURIComponent(otherType))
      }
      if (price !== '') {
        query.push('price=' + encodeURIComponent(price))
      }
      if (location !== '') {
        query.push('location=' + encodeURIComponent(location))
      }
      if (status !== '') {
        query.push('status=' + encodeURIComponent(status))
      }

      if (query.length > 0) {
        const qs = "?" + query.join('&').replace(/%20/g, '+')

        const newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + qs
        window.history.pushState({
          model, otherType, price, location, status,
        },'',newurl)
      }
    }
  }
}

