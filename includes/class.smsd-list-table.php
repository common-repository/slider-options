<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WP_List_Table' ) )
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class SMSD_List_Table extends WP_List_Table{

	private function table_data(){
		global $wpdb;
		$table = $wpdb->prefix .'sm_sliders';
		$data = array();
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) == $table ){
			$sql = "SELECT ID, title, alias, updated_at FROM $table";
			$data = $wpdb->get_results( $sql, ARRAY_A );
		}
        return $data;
	}
	// get columns
	function get_columns(){
		$columns = array(
			'ID' => __('ID', 'smslider'),
			'title' => __('Name', 'smslider'),
			'alias' => 'Shortcode',
			'action' => __('Actions', 'smslider'),
			'updated_at' => __('Date', 'smslider')
		);

		return $columns;
	}

	function prepare_items(){
		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option('per_page', 'option');
		$per_page = get_user_meta( $user, $option, true );
		$per_page = $per_page ? $per_page : $this->get_items_per_page('slides_per_page', 10);
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		// get all item smslider
		$data = $this->table_data();

		// pagination
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page
		) );
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		usort( $data, array( &$this, 'usort_reorder') );
		$this->items = $data;
		
	}

	function column_default( $item, $column_name){
		$ID = $item['ID'];
		$admin_url = add_query_arg( array('slide'=>$ID), SMSD_ADMIN_URL) ."&action=";
		switch( $column_name ){
			case 'ID':
			case 'title' :
				return $item[ $column_name ];
			case 'alias':
				return "[smslider ". $item[ $column_name ] ."]";
			case 'action':
				$edit = $clone = "";
				if( current_user_can( 'smsd_edit' ) ){
					$edit = '<a class="dashicons dashicons-edit" href="'. $admin_url .'edit">'. __("Edit Slide","smslider") .'</a>';
					$clone = ' | <a title="Clone this slide" id="copyslide_'. $ID .'" class="sm-copy dashicons dashicons-admin-page" href="javascript(void:0);">'. __("Clone slide","smslider") .'</a>';
				}
					$preview = ' | <a class="dashicons dashicons-welcome-view-site" href="'. $admin_url .'preview">'. __("Preview","smslider") .'</a>';
				$config = current_user_can( "smsd_config" ) ? ' | <a class="dashicons dashicons-admin-generic" href="'. $admin_url .'setting">'. __("Setting slide", "smslider") .'</a>' : '';
				$del = current_user_can( 'smsd_delete' ) ? ' | <a class="sm-delete dashicons dashicons-trash" id="delslide_'. $ID .'" href="javascript:void(0);">'. __("Delete slide", "smslider") .'</a>' : '';
				
				return join( array( $edit, $config, $preview, $clone, $del ) );
			case 'updated_at' :
				return date( 'M d, Y', strtotime( $item[ $column_name ] ) );
			default:
				return print_r( $item, true );
		}
	}

	function get_sortable_columns(){
		$sortable_columns = array(
			'ID' => array('ID', false),
			'title' => array('title', false),
			'alias' => array('alias', false),
			'updated_at' => array('updated_at', false)
		);

		return $sortable_columns;
	}

	function usort_reorder( $a, $b){
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'updated_at';
		$order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';
		$result = strcmp( $a[$orderby], $b[$orderby] );
		return ( $order === 'asc' ) ? $result : -$result;
	}
}

?>