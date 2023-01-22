<?php
// -- BEGIN LICENSE BLOCK ----------------------------------
//
// This file is a plugin for Dotclear 2.
// 
// Copyright (c) 2013 FredM
// Licensed under the GPL version 2.0 license.
// A copy of this license is available in LICENSE file or at
// http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//
// -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_RC_PATH')) { return; }

$this->registerModule(
	/* Name */			"GalleryInsert",
	/* Description*/		"Insert pictures gallery inside your post",
	/* Author */			"Fred",
	/* Version */			'0.413',
	/* Properties */
	array(
		'permissions' => 'usage,contentadmin',
		'type' => 'plugin',
		'dc_min' => '2.14',
		'support' => 'http://f.montin.free.fr/MULTIBLOG/blog-photos-fred/index.php?post/2011/09/30/Plugin-GalleryInsert',
		'details' => 'http://plugins.dotaddict.org/dc2/details/GalleryInsert'
		)
);