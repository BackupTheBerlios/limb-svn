<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class stats_report_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/stats_report/reports_list.html',
        ),
        'pages_report' => array(
            'template_path' => '/stats_report/pages_list.html',
            'action_name' => strings :: get('show_pages_report', 'stats'),
            'action_path' => '/stats_report/stats_pages_report_action',
            'display_in_breadcrumbs' => true
        ),
        'referers_report' => array(
            'template_path' => '/stats_report/referers_list.html',
            'action_name' => strings :: get('show_referers_report', 'stats'),
            'action_path' => '/stats_report/stats_referers_report_action',
            'display_in_breadcrumbs' => true
        ),
        'hits_hosts_report' => array(
            'template_path' => '/stats_report/hits_hosts_list.html',
            'action_name' => strings :: get('show_hits_hosts_report', 'stats'),
            'action_path' => '/stats_report/stats_hits_hosts_report_action',
            'display_in_breadcrumbs' => true
        ),
        'ips_report' => array(
            'template_path' => '/stats_report/ips_list.html',
            'action_name' => strings :: get('show_ips_report', 'stats'),
            'action_path' => '/stats_report/stats_ips_report_action',
            'display_in_breadcrumbs' => true
        ),
        'keywords_report' => array(
            'template_path' => '/stats_report/keywords_list.html',
            'action_name' => strings :: get('show_keywords_report', 'stats'),
            'action_path' => '/stats_report/stats_keywords_report_action',
            'display_in_breadcrumbs' => true
        ),
        'search_engines_report' => array(
            'template_path' => '/stats_report/search_engines_list.html',
            'action_name' => strings :: get('show_search_engines_report', 'stats'),
            'action_path' => '/stats_report/stats_search_engines_report_action',
            'display_in_breadcrumbs' => true
        ),
        'routes_report' => array(
            'template_path' => '/stats_report/routes_list.html',
            'action_name' => strings :: get('show_routes_report', 'stats'),
            'action_path' => '/stats_report/stats_routes_report_action',
            'display_in_breadcrumbs' => true
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/actions/edit.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/actions/delete.gif'
        ),
    );
  }
}

?>