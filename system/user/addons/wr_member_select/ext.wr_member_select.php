<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wr_member_select_ext {

	var $settings = array();
	var $version = '1.0.0';
	

	function __construct($settings = '')
	{
		$this->settings = $settings;
	}
	
	
	function cp_js_end()
	{
		//$js = '$()';

		$js = "$('[name=\"field_id_283\"]').attr('readonly', 'readonly');";

		$js .= "

		function calculateBalance() {
			var balance = 0;

			// get transactions
			var rows = $('#field_id_282 tbody tr').not('.hidden');
			$(rows).each(function(i, row) {

				// get Withdrawl / Deposit and amount
				var type = $(row).find('[name*=\"[col_id_275]\"]').val();
				var amount = $(row).find('[name*=\"[col_id_276]\"]').val();

				// only add if numeric
				if (!isNaN(parseFloat(amount)) && isFinite(amount)) {
					amount = parseFloat(amount);

					if (type === 'Withdrawal') {
						balance -= amount;
					} else if (type === 'Deposit') {
						balance += amount;
					}
				}
			});

			// force 2 decimal places
			balance = balance.toFixed(2);

			$('[name=\"field_id_283\"]').val(balance);
		}

		// Amount changed
		$(document).on('keyup', '[name*=\"[col_id_276]\"]', function() {
			calculateBalance();
		});

		// Withdrawl / Deposit changed
		$(document).on('change', '[name*=\"[col_id_275]\"]', function() {
			calculateBalance();
		});

		";


		//find('[name*=\"[col_id_266]\"]'); console.log(test);";
		return $js;
	}


	function activate_extension()
	{		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'cp_js_end',
			'hook'		=> 'cp_js_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		ee()->db->insert('extensions', $data);			
	}	
	

	function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}


	function update_extension($version = '')
	{
		if(version_compare($version, $this->version) === 0)
		{
			return FALSE;
		}
		return TRUE;		
	}	
	

}