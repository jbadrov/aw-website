<?php

/*
Template Name: AutonomyWorks Change pass
*/
$changed = NULL ;
if(isset($_POST['change']) and isset($_POST['pass'])){
		global $wpc_client;
		$ID = $wpc_client->current_plugin_page['client_id'] ;
		if( is_numeric($ID) && $ID > 0 ) {
			$client_gps = $wpc_client->cc_get_client_groups_id($ID); //array of string
			$allowed_gps = array('3','4'); //allowed groups IDs
			$intersect = array_intersect( $client_gps , $allowed_gps ) ;
			if(!empty($intersect)) {
				$pass = $_POST['pass'] ; 
				$userdata = array( 
					'ID' => esc_attr($ID),
					'user_pass' => $pass 
				);
				$changed = $wpc_client->cc_client_update_func( $userdata );
			}else die('You are not allowed !!');
		} 
}

//load scripts
add_action( 'wp_enqueue_scripts', function(){
	wp_enqueue_style( 'bootstrap-css', get_template_directory_uri() . '/css/bootstrap.css' );
} );

get_header('autonomyworks'); 
?>

		<div id="primary_home" class="content-area">
        <h1>Change Password</h1>
        <?php if($changed){?>
		<div class="success">Your password has been changed !</div>	
		<?php }?>
        <form method="post" action="#">
        	<div class="row">
              <div class="form-group col-md-4">
                <label for="pass">New Password</label>
                <input type="password" class="form-control" name="pass">
              </div>
              <div class="form-group  col-md-4">
                <label for="pass2">Repeat New Password</label>
                <input type="password" class="form-control"  name="pass2">
              </div>
              <div class="form-group col-md-4">
              <button type="submit" name="change" class="btn btn-default">Change Password</button>
              </div>
          	</div>
          
        </form>
               
<?php get_footer('centro'); ?>