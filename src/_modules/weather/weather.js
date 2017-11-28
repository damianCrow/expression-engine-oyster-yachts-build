import $ from 'jquery'
import 'simpleweather'

import { weatherIconIds } from './weather-icons'
import { climateMonths, averageClimate } from './average-climate-data'

export default class Weather {
  constructor() {
    this.averageClimate = averageClimate
    this.init()
  }

  init() {
    $('.destination-todays-temp').each((index, element) => {
      const location = $(element).data('weather-location')
      $.simpleWeather({
        zipcode: '',
        woeid: '', //2357536
        location,
        unit: 'c',
        success: function success(weather) {
          $('.destination-temp span', element).html(weather.temp)
          $('.destination-date-time time', element).html(weather.forecast[0].date)
          $('.destination-date-time time', element).data('cel', weather.temp)
          $('.destination-date-time time', element).data('fanren', weather.alt.temp)
          $('.weather-icon', element).html(weatherIconIds[weather.code]).promise().done(() => {
            $('.destination-temp').addClass('destination-temp-loaded')
          })
        },
      })
    })

    $('.temperature-setting-label').on('click', () => {
      const weatherBox = $(this).parents('.destination-todays-temp')

      if ($('input', this).prop('checked')) {
        const faran = $(weatherBox).find('.destination-date-time time').data('fanren')
        $('.destination-temp span', weatherBox).html(faran)
      } else {
        const cel = $(weatherBox).find('.destination-date-time time').data('cel')
        $('.destination-temp span', weatherBox).html(cel)
      }
    })

    // ---- *end* simpleWeather.js CONFIG *end* ----

    //  ---- AVERAGE CLIMATE SLIDER -----  //
    if ($('[data-average-climate-hardcode]').length > 0) {
      $('.destination-average-climate').each((index, element) => {
        const destination = $(element).data('average-climate-hardcode')

        const monthText = $('.destination-selected-month', element)

        // Set the first values (aka January)
        monthText.html(this.avgClimate(destination)[0].month)
        $('.destination-avg-rainfall span', element).html(`${this.avgClimate(destination)[0].value.Rainfall}mm`)
        $('.destination-avg-temperature span', element).html(`${this.avgClimate(destination)[0].value.AvgTemp}°C`)

        $('.destination-avg-temperature-next', element).on('click', () => {
          const currentMonth = monthText.data('average-climate-month')
          let nextMonth = currentMonth + 1

          if (this.avgClimate(destination)[nextMonth] === undefined) {
            nextMonth = 0
          }

          monthText.html(this.avgClimate(destination)[nextMonth].month)
          $('.destination-avg-rainfall span', element).html(`${this.avgClimate(destination)[nextMonth].value.Rainfall}mm`)
          $('.destination-avg-temperature span', element).html(`${this.avgClimate(destination)[nextMonth].value.AvgTemp}°C`)

          monthText.data('average-climate-month', nextMonth)
        })

        $('.destination-avg-temperature-prev', element).on('click', () => {
          const currentMonth = monthText.data('average-climate-month')
          let nextMonth = currentMonth - 1

          if (this.avgClimate(destination)[nextMonth] === undefined) {
            nextMonth = 11
          }

          monthText.html(this.avgClimate(destination)[nextMonth].month)
          $('.destination-avg-rainfall span', element).html(`${this.avgClimate(destination)[nextMonth].value.Rainfall}mm`)
          $('.destination-avg-temperature span', element).html(`${this.avgClimate(destination)[nextMonth].value.AvgTemp}°C`)

          monthText.data('average-climate-month', nextMonth)
        })
      })
    }
  }

  avgClimate(destination) {
    const output = []

    function getDescendantProp(obj, desc) {
      const arr = desc.split('.')
      while (arr.length && (obj = obj[arr.shift()])) {
        return obj
      }
    }

    for (const k in climateMonths) {
      const climateMonth = climateMonths[k]
      output.push({ month: climateMonth, value: getDescendantProp(this.averageClimate, destination.toString())[climateMonth] })
    }
    return output
  }
}
