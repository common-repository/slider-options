<?php 

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class SMSD_Activator{
	// activate
	public function __construct(){
		// upgrade
		$old_version = SMSlider::get_option( 'version', true );
		file_put_contents(dirname(__FILE__).'/txt.txt', version_compare( '1.1.2', $old_version ));
		if( ! empty( $old_version ) && version_compare( SMSD_V, $old_version ) > 0 ){
			// update required
			$this->upgrade();
			if( version_compare( '1.1.2', $old_version ) > 0 ){
				$this->upgrade_v12();
			}
		}

	}

	public function active(){
		$this->install();
		$this->setting();
	}

	private function upgrade(){
		$this->update_option( 'version', SMSD_V );
	}

	private function upgrade_v12(){
		$dfault = SMSlider::get_libs();
		if( ! $dfault ){
			wp_die('Sorry, install Failed');
		}
		
		$types = SMSlider::get_option('types');
		if( empty( $types ) ){
			$this->update_option( 'manager', json_encode( $dfault->default->role ) );
			// update role
			$this->manage_sliders( $dfault->default->role );
			$this->update_option( 'settings',  json_encode( $dfault->default->settings ) );
			$this->update_option( 'types', '{"bootstrap":"plug","flexslider":"plug"}');
			
		}
	}

	private function install(){
		global $wpdb;
		$table = $wpdb->prefix .'sm_sliders';
		
		// check exist table sm_sliders
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ){
			$sql = "CREATE TABLE `$table` (
					`ID` INT(11) NOT NULL AUTO_INCREMENT,
					`title` VARCHAR(100) NOT NULL,
					`alias` VARCHAR(45) NOT NULL UNIQUE,
					`params` TEXT NOT NULL,
					`updated_at` TIMESTAMP,
					`created_at` TIMESTAMP,
					PRIMARY KEY (`ID`)
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
		}
	}

	/**
	 * @since 1.0.3
	 * @return void add options
	 */
	private function setting(){
		$dfault = SMSlider::get_libs();
		if( ! $dfault ){
			wp_die( __('Sorry, install Failed','smslider') );
		}
		$this->add_option( 'version', SMSD_V );
		$this->add_option( 'settings',  json_encode( $dfault->default->settings ) );
		$this->add_option( 'size', json_encode( $dfault->default->size ) );
		$this->add_option( 'type', json_encode( new stdClass ) );
		$this->add_option( 'types', '{"bootstrap":"plug","flexslider":"plug"}');
		$this->add_option( 'manager', json_encode( $dfault->default->role ) );
		// update role
		$this->manage_sliders( $dfault->default->role );
		foreach( $dfault->active as $id ){
			if( isset($dfault->libs->{$id}) ){
				$this->add_option( $id, json_encode( $dfault->libs->{$id} ) );
			}
		}
	}

	// deactivatesm
	public function deactive(){
		$cleanup = false;
		if( $cleanup ){
			$this->del_setting();
			$this->del_term_slider();
			$this->del_smslider();
			$this->del_post_slider();
		}
	}

	private function del_setting(){
		global $wpdb;
		$sql = $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like %s",'-smsd_%');
		$wpdb->query($sql);
	}

	private function del_smslider(){
		global $wpdb;
		$table = $wpdb->prefix .'sm_sliders';
		$sql = "DROP TABLE IF EXISTS $table;";
		$wpdb->query($sql);
	}

	private function del_post_slider(){
		global $wpdb;
		delete_post_meta_by_key('_smsd-meta_type');
		delete_post_meta_by_key('_smsd-meta_target');
		$wpdb->delete( $wpdb->posts, array('post_type'=>'smsd-type'));
	}

	private function add_option( $name, $value ){
		$name = "-smsd_{$name}";
		return add_option( $name, $value );
	}

	private function manage_sliders( $roles ){
			global $wp_roles;
			// @since 1.1.2.2 update for security
			if( !current_user_can('manage_options') ){
				die(__('Cheatin&#8217; uh?', 'smslider'));
			}
			$roles = SMSlider::get_option( 'manager', false, $roles );
			foreach( $wp_roles->roles as $r=>$v ){
				$role = get_role( $r );
				if( isset( $roles->$r ) ){
					foreach( SMSlider::$caps as $cap=>$v ){
						if( in_array( $cap, $roles->$r ) ){
							$role->add_cap( $cap );
						}else{
							$role->remove_cap( $cap );
						}
					}
				}else{
					foreach( SMSlider::$caps as $cap=>$v ){
						$role->remove_cap( $cap );
					}
				}
			}
			return true;
		}

	private function update_option( $name, $value ){
		$name = "-smsd_{$name}";
		return update_option( $name, $value );
	}

	private function del_term_slider(){	
		global $wpdb;
		$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.term_id, t.slug, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", "smj-slider" ) );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
				$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
			}
		}
		
		// Delete Taxonomy
		$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => 'smj-slider' ), array( '%s' ) );
	}
}
?>