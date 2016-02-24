<?php
namespace Zenbu\controllers;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\librairies\platform\ee\Cache;
use Zenbu\librairies\platform\ee\Convert;
use Zenbu\librairies\platform\ee\Cp;
use Zenbu\librairies\platform\ee\Lang;
use Zenbu\librairies\platform\ee\Request;
use Zenbu\librairies\platform\ee\Url;
use Zenbu\librairies\platform\ee\View;
use Zenbu\models as Model;

class Zenbu_PermissionsController extends ZenbuBaseController
{
	var $permissions;

	public function __construct()
	{
		parent::init();
	}


	/**
	 * Display Settings
	 * @return string Rendered template
	 */
    public function actionIndex()
    {
    	$this->cp->title(Lang::t('permissions'));
		$this->cp->initSidebar();
		$this->vars['permissions']    = $this->settings->getPermissions('all');
		$this->vars['permission_keys'] = array(
            'can_admin',
            'can_copy_profile',
            'can_access_settings',
            'edit_replace',
            'can_view_group_searches',
            'can_admin_group_searches'
            );
		$this->vars['module_access']  = $this->settings->getGroupsWithAddonAccess();

		//	----------------------------------------
		//	Get rid of Zenbu filter cache
		//	This avoids sectionId/entryTypeId mismatch if
		//	returning to main Zenbu page
		//	----------------------------------------
		Cache::delete('zenbu_filter_cache_'.$this->user->id);

		//	----------------------------------------
		//	Order "show" fields first
		//	----------------------------------------
		$this->vars['rows'] = $this->fields->getOrderedFields(TRUE);

		$selectOptions = $this->sections->getSectionSelectOptions();
		$this->vars['section_dropdown_options']   = $selectOptions['sections'];
		$this->vars['user_groups']   = $this->users->getUserGroups();

		//	----------------------------------------
		//	Action URLs
		//	----------------------------------------
		//$this->vars['section_select_action_url']    = Url::cpUrl(AMP."C=addons_modules".AMP."M=show_module_cp".AMP."module=zenbu&method=settings");
		$this->vars['action_url']    = Url::zenbuUrl('save_permissions');

		View::includeCss(array(
			'resources/css/zenbu_main.css',
			'resources/css/font-awesome.min.css'
			));
		return array(
			  'body'       => View::render('permissions/index.twig', $this->vars),
			  'breadcrumb' => array(Url::zenbuUrl()->compile() => Lang::t('Zenbu')),
			  'heading'  => Lang::t('permissions'),
			);

    } // END actionIndex()

    // --------------------------------------------------------------------


    public function actionSave()
    {
		$members        = Request::post('members');

		if($members === FALSE)
		{
			$this->cp->message('error', Lang::t("No permissions could be saved."));
	    	Request::redirect(Url::zenbuUrl("permissions"));
		}

		foreach($members as $group_id => $perm_array)
		{
			foreach($perm_array as $perm => $value)
			{
				$permissions_settings = new Model\ZenbuPermissionsModel();
				$permissions_settings->userGroupId = $group_id;
				$permissions_settings->setting = $perm;
				$permissions_settings->value = $group_id == 1 ? 'y' : $value;
				$permissions_settings->save();
			}
		}

		//	----------------------------------------
		//	Enable module for member groups
		//	- A shortcut to avoid going into each
		//	group settings to enable Zenbu
		//	----------------------------------------

		// Get module ID
    	$module = $this->db->find('modules', $where = 'module_name = "?"', array('Zenbu'), FALSE);

		// Remove all member_group module access settings for this module
		// Will add new settings based on submitted data later
		$this->db->delete('module_member_groups', 'module_id = ?', array($module[0]->module_id));

		if(Request::param('enable_module') !== FALSE)
		{
			foreach(Request::param('enable_module') as $group_id)
			{
				if( $group_id != 1)
				{
					// Add member group to be allowed access to Zenbu
					$enable_module_access['group_id'] = $group_id;
					$enable_module_access['module_id'] = $module[0]->module_id;
					$this->db->insert('module_member_groups', $enable_module_access);

					// Turn on ADD-ON Access
					$enable_access['can_access_addons'] = 'y';
					//$enable_access['can_access_modules'] = 'y';
					$this->db->update('member_groups', $enable_access, 'group_id = ?', array($group_id));
				}
			}
		}

		$this->cp->message('success', Lang::t("Permissions Saved"));

    	Request::redirect(Url::zenbuUrl("permissions"));
    } // END actionSave()

    // --------------------------------------------------------------------

}
