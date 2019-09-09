<?php

/*
	Question2Answer (c) Gideon Greenspan
	Neayi Plugin (c) Neayi

	File: qa-plugin/neayi/qa-plugin.php
	Version: 1.0.0
	Description: Adds some specific stuff for Neayi


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

/*
	Plugin Name: Neayi
	Plugin URI: https://github.com/neayi/q2a-neayi
	Plugin Description: Adds some specific stuff for Neayi
	Plugin Version: 1.0.0
	Plugin Date: 2019-09-02
	Plugin Author: Bertrand Gorge - Neayi
	Plugin Author URI: https://github.com/neayi/
	Plugin License: MIT
	Plugin Minimum Question2Answer Version: 1.8.3
	Plugin Minimum PHP Version: 7
	Plugin Update Check URI: https://raw.github.com/neayi/q2a-neayi/master/qa-plugin.php
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_module('page', 'qa-neayi.php', 'qa_neayi', 'Neayi Configuration');

// Register a layer that will add the cookie JS
qa_register_plugin_layer('qa-neayi-layer.php', 'Neayi Layer');

/*
	Omit PHP closing tag to help avoid accidental output
*/

