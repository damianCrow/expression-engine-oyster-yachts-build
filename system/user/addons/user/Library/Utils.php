<?php

namespace Solspace\Addons\User\Library;
use Solspace\Addons\User\Library\AddonBuilder;

class Utils extends AddonBuilder
{
	// --------------------------------------------------------------------

	/**
	 *	Characters Decoding
	 *
	 *	Converted entities back into characters
	 *
	 *	@access		public
	 *	@param		string
	 *	@return		string
	 */

	public function chars_decode( $str = '' )
	{
		if ( $str == '' ) return;

		if ( function_exists( 'htmlspecialchars_decode' ) )
		{
			$str	= htmlspecialchars_decode( $str );
		}

		if ( function_exists( 'html_entity_decode' ) )
		{
			$str	= html_entity_decode( $str );
		}

		$str	= str_replace( array( '&amp;', '&#47;', '&#39;', '\'' ), array( '&', '/', '', '' ), $str );

		$str	= stripslashes( $str );

		return $str;
	}
	//END chars_decode


	// --------------------------------------------------------------------

	/**
	 *	Update User's Last Activity in Database
	 *
	 *	@access		public
	 *	@return		bool
	 */

	public function update_last_activity()
	{
		if (ee()->session->userdata('member_id') == 0)
		{
			return FALSE;
		}
		else
		{
			$member_id	= ee()->session->userdata('member_id');
		}

		ee()->db->update(
			'members',
			array('last_activity'	=> ee()->localize->now),
			array('member_id'		=> $member_id)
		);

		return TRUE;
	}
	//END update_last_activity


	// --------------------------------------------------------------------

	/**
	 * full stop
	 *
	 * stop on ajax or user error
	 *
	 * @access	public
	 * @param 	mixed 	string error message
	 * @param 	string 	show_user_error type
	 * @return	null
	 */

	public function full_stop ($errors = '', $error_type = 'submission')
	{
		if ( ! is_array($errors))
		{
			$errors = array($errors);
		}

		if ($this->is_ajax_request())
		{
			$this->send_ajax_response(array(
				'success' => FALSE,
				'errors' => $errors
			));
		}
		else
		{
			//the error array might have sub arrays
			//so we need to flatten
			$error_return = array();

			foreach ($errors as $error_set => $error_data)
			{
				if (is_array($error_data))
				{
					foreach ($error_data as $sub_key => $sub_error)
					{
						$error_return[] = $sub_error;
					}
				}
				else
				{
					$error_return[] = $error_data;
				}
			}

			$this->show_error($error_return);
		}

		if ($this->test_mode)
		{
			return;
		}
		else
		{
			exit();
		}
	}
	//END full_stop


	// --------------------------------------------------------------------

	/**
	 * Session Useable
	 *
	 * @access	public
	 * @param	boolean $require_member_id [description]
	 * @return	bool	session is fully available and member logged in
	 */

	public function session_useable($require_member_id = true)
	{
		return (
			isset(ee()->session) &&
			is_object(ee()->session) &&
			isset(ee()->session->userdata) &&
			(
				$require_member_id == false OR
				ee()->session->userdata('member_id') > 0
			)
		);
	}
	//END session_useable


	// --------------------------------------------------------------------

	/**
	 * Image Resize
	 *
	 * @access	public
	 * @param	string	$filename	filename to resize
	 * @param	string	$type		type of resize item (avatar, photo, sig)
	 * @param	string	$axis		axis to resize on
	 * @return	boolean				success
	 */

	public function image_resize($filename, $type = 'avatar', $axis = 'width')
	{
		if ($type == 'avatar')
		{
			$max_width	= (	ee()->config->slash_item('avatar_max_width') == '' OR
							ee()->config->item('avatar_max_width') == 0) ?
								100 : ee()->config->item('avatar_max_width');
			$max_height	= (	ee()->config->item('avatar_max_height') == '' OR
							ee()->config->item('avatar_max_height') == 0) ?
								100 : ee()->config->item('avatar_max_height');
			$image_path = rtrim( ee()->config->item('avatar_path'), '/' ) . '/';
		}
		elseif ($type == 'photo')
		{
			$max_width	= (	ee()->config->slash_item('photo_max_width') == '' OR
							ee()->config->item('photo_max_width') == 0) ?
								100 : ee()->config->item('photo_max_width');
			$max_height	= (	ee()->config->item('photo_max_height') == '' OR
							ee()->config->item('photo_max_height') == 0) ?
								100 : ee()->config->item('photo_max_height');
			$image_path = ee()->config->item('photo_path');
		}
		else
		{
			$max_width	= (	ee()->config->slash_item('sig_img_max_width') == '' OR
							ee()->config->item('sig_img_max_width') == 0) ?
								100 : ee()->config->item('sig_img_max_width');
			$max_height	= (	ee()->config->item('sig_img_max_height') == '' OR
							ee()->config->item('sig_img_max_height') == 0) ?
								100 : ee()->config->item('sig_img_max_height');
			$image_path = ee()->config->item('sig_img_path');
		}

		ee()->load->helper('text');

		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		$imageType = 0;
		switch (strtolower($ext)) {
			case "gif": $imageType = 1; break;
			case "png": $imageType = 3; break;
			case "jpeg":
			case "jpg":
				$imageType = 2;
				break;
		}

		$config = array(
			'image_library'		=> ee()->config->item('image_resize_protocol'),
			'library_path'		=> ee()->config->item('image_library_path'),
			'maintain_ratio'	=> TRUE,
			'master_dim'		=> $axis,
			'source_image'		=> reduce_double_slashes($image_path . '/' . $filename),
			'full_src_path'		=> reduce_double_slashes($image_path . '/' . $filename),
			'image_type'        => $imageType,
			'quality'			=> '75%',
			'width'				=> $max_width,
			'height'			=> $max_height
		);

		ee()->load->library('image_lib');

		ee()->image_lib->initialize($config);

		if ( ! ee()->image_lib->resize())
		{
			return FALSE;
		}

		return TRUE;
	}
	//END image_resize


	// --------------------------------------------------------------------

	/**
	 * Split a string by pipes with no empty items
	 * Because I got really tired of typing this.
	 *
	 * @access public
	 * @param  string $str pipe delimited string to split
	 * @return array      array of results
	 */

	public function pipe_split($str)
	{
		return preg_split('/\|/', $str,	-1,	PREG_SPLIT_NO_EMPTY);
	}
	//END pipe_split


	// --------------------------------------------------------------------

	/**
	 * Template Parser Class Usable
	 *
	 * @access	public
	 * @return	boolean		present and usable ee()->TMPL
	 */

	public function tmpl_usable()
	{
		return isset(ee()->TMPL) &&
				is_object(ee()->TMPL) &&
				//this in case someone sets a varialble to it on
				//accident and auto-instanciates it as stdClass.
				is_callable(array(ee()->TMPL, 'fetch_param'));
	}
	//END tmpl_usable
}
// End class Utils
