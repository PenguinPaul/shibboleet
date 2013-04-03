<?php

/**************************************
* This plugin took a long time to make,
*  due to the way drafts work and such.
* Good thing I did it during a boring
*  university class.
*/


// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
  die("Nope.");
}

$plugins->add_hook('datahandler_post_insert_thread_post','shibboleet_thread');
$plugins->add_hook('datahandler_post_insert_post','shibboleet_post');

function shibboleet_info()
{
	return array(
		"name"			=> "XKCD/806 Compliance Plugin",
		"description"	=> "This plugin makes MyBB XKCD/806 compliant.",
		"website"		=> "http://xkcd.com/806/",
		"author"		=> "Paul H.",
		"authorsite"	=> "http://paulhedman.com",
		"version"		=> "1.0",
		"guid" 			=> "",
		"compatibility" => "*"
	);
}

function shibboleet_activate()
{
	global $mybb;

	if(!function_exists('mysupport_info'))
	{
		flash_message("You need MySupport installed for this plugin to work!", "error");
		admin_redirect("index.php?module=config-plugins");
	} else if($mybb->settings['enablemysupport'] != 1) {
		flash_message("You need MySupport to be active for this plugin to work!", "error");
		admin_redirect("index.php?module=config-plugins");
	}
}

function shibboleet_thread(&$thread)
{
	global $mybb,$db;

	if(strpos($thread->post_insert_data['message'], 'shibboleet') !== false && $mybb->settings['enablemysupport'] == 1 && $thread->post_insert_data['visible'] == 1)
	{

		$fid = $thread->post_insert_data['fid'];
		$tid = $thread->post_insert_data['tid'];

		if(empty($tid))
		{
			$tid = $thread->data['tid'];
			$fid = $thread->data['fid'];
		}

		if(mysupport_forum($fid))
		{
			$db->update_query("threads", array('status' => 2), "tid='{$tid}'");
		}
	}
}

function shibboleet_post(&$post)
{
	global $mybb,$db;
	
	if(empty($post->post_insert_data))
	{
		if(strpos($post->post_update_data['message'], 'shibboleet') !== false && $mybb->settings['enablemysupport'] == 1 && $post->post_update_data['visible'] != -2)
		{
			$fid = $post->data['fid'];
			$tid = $post->data['tid'];

			if(mysupport_forum($fid))
			{
				$db->update_query("threads", array('status' => 2), "tid='{$tid}'");
			}
		}
	} else {
		if(strpos($post->post_insert_data['message'], 'shibboleet') !== false && $mybb->settings['enablemysupport'] == 1 && $post->post_insert_data['visible'] != -2)
		{
			$fid = $post->post_insert_data['fid'];
			$tid = $post->post_insert_data['tid'];
			if(mysupport_forum($fid))
			{
				$db->update_query("threads", array('status' => 2), "tid='{$tid}'");
			}
		}
	}
}


?>
