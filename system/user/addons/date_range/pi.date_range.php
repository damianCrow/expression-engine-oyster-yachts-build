<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_range {

	public $return_data = "";

    public function __construct() {

    	$startDate = ee()->TMPL->fetch_param('start_date');
    	$endDate = ee()->TMPL->fetch_param('end_date');

    	$this->return_data = $this->formatDateRange($startDate, $endDate);
    }

    private function formatDateRange($startDate, $endDate) {
    	$startDateSegments = explode(' ', $startDate);
    	$endDateSegments = explode(' ', $endDate);

		if ($startDateSegments[2] !== $endDateSegments[2]) {
			return $startDate.' - '.$endDate;
		}

		if ($startDateSegments[1] !== $endDateSegments[1]) {
			return $startDateSegments[0].' '.$startDateSegments[1].' - '.$endDate;
		}

		if ($startDateSegments[0] !== $endDateSegments[0]) {
			return $startDateSegments[0].' - '.$endDate;
		}

		return $startDate;
    }
}