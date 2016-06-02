<?php

class Orbis_Checklist_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis_checklist' );
		$this->set_db_version( '1.0.0' );

		$this->plugin_include( 'includes/post.php' );
	}

	public function loaded() {
		$this->load_textdomain( 'orbis_checklist', '/languages/' );
	}
}
