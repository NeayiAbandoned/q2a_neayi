<?php

/*
	Question2Answer (c) Gideon Greenspan
	Open Login Plugin (c) Alex Lixandru

	http://www.question2answer.org/


	File: qa-plugin/open-login/qa-open-page-logins.php
	Version: 3.0.0
	Description: Implements the business logic for the plugin custom page


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


class qa_neayi {
	const ADD_TEST_DATA_BTN = 'neayi_admin_create_test_data_btn';
	const REMOVE_TEST_DATA_BTN = 'neayi_admin_delete_test_data_btn';

	private function reset_options_for_id( $id )
	{
		$reset_options = array();

		switch ( $id ) {
			case 1 :
				//nothing to reset , already done with the ok module
				break;
		}

		if ( count( $reset_options ) ) {
			donut_reset_options( $reset_options );
		}
	}

	public function admin_form( &$qa_content )
	{
		$saved = false;
		$error = false;

		if ( qa_clicked( self::ADD_TEST_DATA_BTN ) )
		{
			if ( qa_check_form_security_code( 'neayi/admin_options', qa_post_text( 'code' ) ) )
			{
				require_once dirname( __FILE__ ) . '/qa-neayi-test-data.php';
				$testData = new neayi_test_data();
				$testData->addAllTestData();
				$saved = true;
				qa_opt( 'neayi_test_data_set_ok', 1 );
			}
			else
			{
				$error = qa_lang_html( 'admin/form_security_expired' );
			}
		}

		if ( qa_clicked( self::REMOVE_TEST_DATA_BTN ) )
		{
			if ( qa_check_form_security_code( 'neayi/admin_options', qa_post_text( 'code' ) ) )
			{
				require_once dirname( __FILE__ ) . '/qa-neayi-test-data.php';
				$testData = new neayi_test_data();
				$testData->removeAllTestData();
				$saved = true;
				qa_opt( 'neayi_test_data_set_ok', 0 );
			}
			else
			{
				$error = qa_lang_html( 'admin/form_security_expired' );
			}
		}

		$form = array(
			'ok'      => $saved ? "Data added" : null,
			'fields'  => array(
				'simple_note' => array(
					'type'  => 'static',
					'label' => "Test data",
					'error' => $error,
				),
			),
			'buttons' => array(
				array(
					'label' => "Add Neayi test data",
					'tags'  => 'NAME="' . self::ADD_TEST_DATA_BTN . '"',
				),
				array(
					'label' => "Remove Neayi test data",
					'tags'  => 'NAME="' . self::REMOVE_TEST_DATA_BTN . '"',
				),
			),
			'hidden'  => array(
				'code' => qa_get_form_security_code( 'neayi/admin_options' ),
			),
		);

		return $form;
	}

}

/*
	Omit PHP closing tag to help avoid accidental output
*/
