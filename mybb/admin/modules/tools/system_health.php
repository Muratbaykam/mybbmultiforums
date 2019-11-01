<?php
/**
 * MyBB 1.6
 * Copyright 2010 MyBB Group, All Rights Reserved
 *
 * Website: http://mybb.com
 * License: http://mybb.com/about/license
 *
 * $Id: system_health.php 5545 2011-08-08 15:44:56Z PirataNervo $
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item($lang->system_health, "index.php?module=tools-system_health");

$sub_tabs['system_health'] = array(
	'title' => $lang->system_health,
	'link' => "index.php?module=tools-system_health",
	'description' => $lang->system_health_desc
);

$sub_tabs['template_check'] = array(
	'title' => $lang->check_templates,
	'link' => "index.php?module=tools-system_health&amp;action=check_templates",
	'description' => $lang->check_templates_desc
);

$plugins->run_hooks("admin_tools_system_health_begin");

if($mybb->input['action'] == "do_check_templates" && $mybb->request_method == "post")
{
	$plugins->run_hooks("admin_tools_system_health_template_do_check_start");

	$query = $db->simple_select("templates", "*", "", array("order_by" => "sid, title", "order_dir" => "ASC"));

	if(!$db->num_rows($query))
	{
		flash_message($lang->error_invalid_input, 'error');
		admin_redirect("index.php?module=tools-system_health");
	}

	$t_cache = array();
	while($template = $db->fetch_array($query))
	{
		if(check_template($template['template']) == true)
		{
			$t_cache[$template['sid']][] = $template;
		}
	}

	if(empty($t_cache))
	{
		flash_message($lang->success_templates_checked, 'success');
		admin_redirect("index.php?module=tools-system_health");
	}

	$plugins->run_hooks("admin_tools_system_health_template_do_check");

	$page->add_breadcrumb_item($lang->check_templates);
	$page->output_header($lang->check_templates);

	$page->output_nav_tabs($sub_tabs, 'template_check');
	$page->output_inline_error(array($lang->check_templates_info_desc));

	$templatesets = array(
		-2 => array(
			"title" => "MyBB Master Templates"
		)
	);
	$query = $db->simple_select("templatesets", "*");
	while($set = $db->fetch_array($query))
	{
		$templatesets[$set['sid']] = $set;
	}

	$count = 0;
	foreach($t_cache as $sid => $templates)
	{
		if(!$done_set[$sid])
		{
			$table = new Table();
			$table->construct_header($templatesets[$sid]['title'], array("colspan" => 2));

			$done_set[$sid] = 1;
			++$count;
		}

		if($sid == -2)
		{
			// Some cheeky clown has altered the master templates!
			$table->construct_cell($lang->error_master_templates_altered, array("colspan" => 2));
			$table->construct_row();
		}

		foreach($templates as $template)
		{
			if($sid == -2)
			{
				$table->construct_cell($template['title'], array('colspan' => 2));
			}
			else
			{
				$popup = new PopupMenu("template_{$template['tid']}", $lang->options);
				$popup->add_item($lang->full_edit, "index.php?module=style-templates&amp;action=edit_template&amp;title=".urlencode($template['title'])."&amp;sid={$sid}");

				$table->construct_cell("<a href=\"index.php?module=style-templates&amp;action=edit_template&amp;title=".urlencode($template['title'])."&amp;sid={$sid}&amp;from=diff_report\">{$template['title']}</a>", array('width' => '80%'));
				$table->construct_cell($popup->fetch(), array("class" => "align_center"));
			}

			$table->construct_row();
		}

		if($done_set[$sid] && !$done_output[$sid])
		{		
			$done_output[$sid] = 1;
			if($count == 1)
			{
				$table->output($lang->check_templates);
			}
			else
			{
				$table->output();
			}
		}
	}

	$page->output_footer();
}

if($mybb->input['action'] == "check_templates")
{
	$plugins->run_hooks("admin_tools_system_health_template_check");

	$page->add_breadcrumb_item($lang->check_templates);
	$page->output_header($lang->check_templates);

	$page->output_nav_tabs($sub_tabs, 'template_check');

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form = new Form("index.php?module=tools-system_health", "post", "check_set");
	echo $form->generate_hidden_field("action", "do_check_templates");

	$form_container = new FormContainer($lang->check_templates);
	$form_container->output_row($lang->check_templates_title, "", $lang->check_templates_info);
	$form_container->end();

	$buttons = array();
	$buttons[] = $form->generate_submit_button($lang->proceed);

	$form->output_submit_wrapper($buttons);
	
	$form->end();

	$page->output_footer();
}
	
if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_tools_system_health_start");
	
	$page->output_header($lang->system_health);
	
	$page->output_nav_tabs($sub_tabs, 'system_health');
	
	$table = new Table;
	$table->construct_header($lang->totals, array("colspan" => 2));
	$table->construct_header($lang->attachments, array("colspan" => 2));
	
	$query = $db->simple_select("attachments", "COUNT(*) AS numattachs, SUM(filesize) as spaceused, SUM(downloads*filesize) as bandwidthused", "visible='1' AND pid > '0'");
	$attachs = $db->fetch_array($query);
	
	$table->construct_cell("<strong>{$lang->total_database_size}</strong>", array('width' => '25%'));
	$table->construct_cell(get_friendly_size($db->fetch_size()), array('width' => '25%'));
	$table->construct_cell("<strong>{$lang->attachment_space_used}</strong>", array('width' => '200'));
	$table->construct_cell(get_friendly_size(intval($attachs['spaceused'])), array('width' => '200'));
	$table->construct_row();
	
	if($attachs['spaceused'] > 0)
	{
		$attach_average_size = round($attachs['spaceused']/$attachs['numattachs']);
		$bandwidth_average_usage = round($attachs['bandwidthused']);
	}
	else
	{
		$attach_average_size = 0;
		$bandwidth_average_usage = 0;
	}
	
	$table->construct_cell("<strong>{$lang->total_cache_size}</strong>", array('width' => '25%'));
	$table->construct_cell(get_friendly_size($cache->size_of()), array('width' => '25%'));
	$table->construct_cell("<strong>{$lang->estimated_attachment_bandwidth_usage}</strong>", array('width' => '25%'));
	$table->construct_cell(get_friendly_size($bandwidth_average_usage), array('width' => '25%'));
	$table->construct_row();
	
	
	$table->construct_cell("<strong>{$lang->max_upload_post_size}</strong>", array('width' => '200'));
	$table->construct_cell(@ini_get('upload_max_filesize').' / '.@ini_get('post_max_size'), array('width' => '200'));
	$table->construct_cell("<strong>{$lang->average_attachment_size}</strong>", array('width' => '25%'));
	$table->construct_cell(get_friendly_size($attach_average_size), array('width' => '25%'));
	$table->construct_row();
	
	$table->output($lang->stats);
	
	$table->construct_header($lang->task);
	$table->construct_header($lang->run_time, array("width" => 200, "class" => "align_center"));
	
	$task_cache = $cache->read("tasks");
	$nextrun = $task_cache['nextrun'];
	
	$query = $db->simple_select("tasks", "*", "nextrun >= '{$nextrun}' AND enabled='1'", array("order_by" => "nextrun", "order_dir" => "asc", 'limit' => 3));
	while($task = $db->fetch_array($query))
	{
		$task['title'] = htmlspecialchars_uni($task['title']);
		$next_run = date($mybb->settings['dateformat'], $task['nextrun']).", ".date($mybb->settings['timeformat'], $task['nextrun']);
		$table->construct_cell("<strong>{$task['title']}</strong>");
		$table->construct_cell($next_run, array("class" => "align_center"));
	
		$table->construct_row();
	}
	
	$table->output($lang->next_3_tasks);
	
	$backups = array();
	$dir = MYBB_ADMIN_DIR.'backups/';
	$handle = opendir($dir);
	while(($file = readdir($handle)) !== false)
	{
		if(filetype(MYBB_ADMIN_DIR.'backups/'.$file) == 'file')
		{
			$ext = get_extension($file);
			if($ext == 'gz' || $ext == 'sql')
			{
				$backups[@filemtime(MYBB_ADMIN_DIR.'backups/'.$file)] = array(
					"file" => $file,
					"time" => @filemtime(MYBB_ADMIN_DIR.'backups/'.$file),
					"type" => $ext
				);
			}
		}
	}
	
	$count = count($backups);
	krsort($backups);
	
	$table = new Table;
	$table->construct_header($lang->name);
	$table->construct_header($lang->backup_time, array("width" => 200, "class" => "align_center"));
	
	$backupscnt = 0;
	foreach($backups as $backup)
	{
		++$backupscnt;
		
		if($backupscnt == 4)
		{
			break;
		}
		
		if($backup['time'])
		{
			$time = my_date($mybb->settings['dateformat'].", ".$mybb->settings['timeformat'], $backup['time']);
		}
		else
		{
			$time = "-";
		}
		
		$table->construct_cell("<a href=\"index.php?module=tools-backupdb&amp;action=dlbackup&amp;file={$backup['file']}\">{$backup['file']}</a>");
		$table->construct_cell($time, array("class" => "align_center"));
		$table->construct_row();
	}
	
	if($count == 0)
	{
		$table->construct_cell($lang->no_backups, array('colspan' => 2));
		$table->construct_row();
	}
	
	
	$table->output($lang->existing_db_backups);
	
	if(is_writable(MYBB_ROOT.'inc/settings.php'))
	{
		$message_settings = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_settings = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable(MYBB_ROOT.'inc/config.php'))
	{
		$message_config = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_config = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable('.'.$mybb->settings['uploadspath']))
	{
		$message_upload = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_upload = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable('../'.$mybb->settings['avataruploadpath']))
	{
		$message_avatar = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_avatar = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable(MYBB_ROOT.'inc/languages/'))
	{
		$message_language = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_language = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable(MYBB_ROOT.$config['admin_dir'].'/backups/'))
	{
		$message_backup = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_backup = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable(MYBB_ROOT.'/cache/'))
	{
		$message_cache = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_cache = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	if(is_writable(MYBB_ROOT.'/cache/themes/'))
	{
		$message_themes = "<span style=\"color: green;\">{$lang->writable}</span>";
	}
	else
	{
		$message_themes = "<strong><span style=\"color: #C00\">{$lang->not_writable}</span></strong><br />{$lang->please_chmod_777}";
		++$errors;
	}
	
	
	if($errors)
	{
		$page->output_error("<p><em>{$errors} {$lang->error_chmod}</span></strong> {$lang->chmod_info} <a href=\"http://wiki.mybb.com/index.php/HowTo_Chmod\" target=\"_blank\">MyBB Wiki</a>.</em></p>");
	}
	else
	{
		$page->output_success("<p><em>{$lang->success_chmod}</em></p>");
	}
	
	$table = new Table;
	$table->construct_header($lang->file);
	$table->construct_header($lang->location, array("colspan" => 2, 'width' => 250));
	
	$table->construct_cell("<strong>{$lang->config_file}</strong>");
	$table->construct_cell("./inc/config.php");
	$table->construct_cell($message_config);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->settings_file}</strong>");
	$table->construct_cell("./inc/settings.php");
	$table->construct_cell($message_settings);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->file_upload_dir}</strong>");
	$table->construct_cell($mybb->settings['uploadspath']);
	$table->construct_cell($message_upload);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->avatar_upload_dir}</strong>");
	$table->construct_cell($mybb->settings['avataruploadpath']);
	$table->construct_cell($message_avatar);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->language_files}</strong>");
	$table->construct_cell("./inc/languages");
	$table->construct_cell($message_language);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->backup_dir}</strong>");
	$table->construct_cell('./'.$config['admin_dir'].'/backups');
	$table->construct_cell($message_backup);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->cache_dir}</strong>");
	$table->construct_cell('./cache');
	$table->construct_cell($message_cache);
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->themes_dir}</strong>");
	$table->construct_cell('./cache/themes');
	$table->construct_cell($message_themes);
	$table->construct_row();
	
	$table->output($lang->chmod_files_and_dirs);
	
	$page->output_footer();
}
?>
