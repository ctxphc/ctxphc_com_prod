<?php
/*
Template Name: Reg_Renewal
*/

global $defSel, $wpdb;


?>

<?php // if (!is_user_logged_in()) { auth_redirect(); } //User must be logged in to access this page!?>  
<?php //$wpdb->show_errors(); ?>

<?php

if (!is_user_logged_in()) { auth_redirect(); }

$reg_action = reg_type_renew();
 
$display_reg_warning = false;

$states_arr = load_states_array();

$orig_user_info = array();
$orig_user_meta = array();

$relationship_arr = load_relationships_array();
#retrieve current user_data

$xlogin_user_id = get_current_user_id();

$orig_user_info['mb'] =  get_userdata($xlogin_user_id);

$orig_user_meta['mb'] = get_user_meta($xlogin_user_id);

$form_data_map = load_form_data_map();

$memb_costs = get_membership_pricing();

$populate_form = array();

$relationship_map = array(
	"S" => 2,
	"P" => 3,
	"C" => 4,
	"O" => 5,
);


$associate_keys = get_associate_id_keys();

$join = "";
foreach ($associate_keys as $assoc_id){
	$key_list = $key_list .$join. $assoc_id ;
	$join ="|";
}
$assoc_keys_regex="/^(?:" . $key_list . ")/";

foreach ($associate_keys as $sub_key){
	
	if ($sub_key == "mb") continue;
	
	$sub_id  = $sub_key . "_id";
    if (isset($orig_user_meta['mb'][$sub_id][0])){
    	$sub_user_id = $orig_user_meta['mb'][$sub_id][0];
    
    	$orig_user_info[$sub_key] =  get_userdata($sub_user_id);
    	
    	$orig_user_meta[$sub_key] =  get_user_meta($sub_user_id);
    }
	    	
}


// capture original user info in session variable
//   for reference and comparison
$_SESSION['orig_user_data'] = $orig_user_info;
$_SESSION['orig_user_meta'] = $orig_user_meta;


// map user_meta data to form fields
foreach ($orig_user_meta as $sub_key => $sub_user_meta){
	
	$prefix = ($sub_key == "mb")?"":$sub_key."_";
	
	foreach ( $sub_user_meta as $orig_meta_key => $orig_meta_val ){
		// echo "//testing $orig_meta_key => $orig_meta_val[0]\n";
		if (($orig_meta_key == "memb_type") || ($orig_meta_key == "membership_type") || ($orig_meta_key == "mb_membership_type")|| ($orig_meta_key == "membership")  ){
			$memb_radio_tag = "memb_type_" . $orig_meta_val[0];
			#echo "\tdocument.getElementById(\"memb_radio_tag\").value = \"checked\" \n";
			$populate_form[$memb_radio_tag] = $orig_meta_val[0];
		}
		else {
			if ($id_name = $form_data_map[$orig_meta_key]){
				#echo ("<!-- Testing $id_name, value: $orig_meta_val[0] --> \n ");
				# echo "\tdocument.getElementById(\"$id_name\").value = \"$orig_meta_val[0]\" \n";
				#echo ("<!-- reg_match $id_name: " . (preg_match("/relationship$/",$id_name)?'TRUE':'FALSE') . "--> \n ");
				if (preg_match( "/relationship$/",$id_name)  && (isset($relationship_map[$orig_meta_val[0]])))
				{
					#echo ("<!-- Testing map for  $orig_meta_val[0] --> \n ");
					#if (isset($relationship_map[$orig_meta_val[0]])) {
					$map_val = $relationship_map[$orig_meta_val[0]];
					#echo ("<!-- mapping $orig_meta_val[0] to $map_val  --> \n  ");
					$data_val = $relationship_map[$orig_meta_val[0]];
					#}
	
				}else{
					$data_val = $orig_meta_val[0];
				}
				$form_key = (preg_match($assoc_keys_regex, $id_name))?$id_name:$sub_key."_".$id_name;
				$populate_form[$form_key]= $data_val;
			}
		}
	}
}

foreach ($orig_user_info as $sub_id => $sub_user_data){

	 $user_email =$sub_user_data->get("user_email");
	 if (strlen($user_email)){
	 	  $populate_form[$sub_id. "_" . "email"] = $user_email;
	 }
	
}

/*foreach ( $orig_user_meta as $orig_meta_key => $orig_meta_val ){
	// echo "//testing $orig_meta_key => $orig_meta_val[0]\n";
	if (($orig_meta_key == "memb_type") || ($orig_meta_key == "membership_type") || ($orig_meta_key == "mb_membership_type")|| ($orig_meta_key == "membership")  ){
		$memb_radio_tag = "memb_type_" . $orig_meta_val[0];
		#echo "\tdocument.getElementById(\"memb_radio_tag\").value = \"checked\" \n";
		$populate_form[$memb_radio_tag] = $orig_meta_val[0];
	}
	else {
		if ($id_name = $form_data_map[$orig_meta_key]){
			#echo ("<!-- Testing $id_name, value: $orig_meta_val[0] --> \n ");
			# echo "\tdocument.getElementById(\"$id_name\").value = \"$orig_meta_val[0]\" \n";
			#echo ("<!-- reg_match $id_name: " . (preg_match("/relationship$/",$id_name)?'TRUE':'FALSE') . "--> \n ");
			if (preg_match( "/relationship$/",$id_name)  && (isset($relationship_map[$orig_meta_val[0]])))
			{
				#echo ("<!-- Testing map for  $orig_meta_val[0] --> \n ");
				#if (isset($relationship_map[$orig_meta_val[0]])) {
					$map_val = $relationship_map[$orig_meta_val[0]];
					#echo ("<!-- mapping $orig_meta_val[0] to $map_val  --> \n  ");
					$data_val = $relationship_map[$orig_meta_val[0]];
				#}
				
			}else{
			    $data_val = $orig_meta_val[0];
			}
			$populate_form[$id_name]= $data_val;
		}
	}
}*/

get_header();
?>

<div id="content">
		<div class="spacer"></div>
		<?php if ( have_posts() ) : while ( have_posts() ) :
			the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div id="post_title" class="post_title">
					<h1>
						<a href="<?php the_permalink() ?>" rel="bookmark"
						   title="Permanent Link to <?php the_title_attribute(); ?>">
							<?php the_title(); ?>
						</a>
					</h1>
				</div>
				<!-- Post_title -->
				<div class="clear"></div>
				<div class="entry">
					<?php the_content( 'more...' ); ?>
					<div class="clear"></div>
					<div class="spacer"></div>
					<div>
						<h2 class="ctxphc_center">Central Texas Parrot Head Club</h2>
					</div>

					<!-- removed reg warning and introductory text copied from mem_reg.php -->
					

					<div class="spacer"></div>


					<div class="reg_form_row" id="mem_reg_types">
						<h4>Membership Types:</h4>
						<ul class="ctxphc_reg_type" id="memb_reg_list">
							<li>Individual -
								$<?php echo $memb_costs[ 1 ]->cost; ?></li>
							<li>Individual + Child -
								$<?php echo $memb_costs[ 2 ]->cost; ?></li>
							<li>Couples -
								$<?php echo $memb_costs[ 3 ]->cost; ?></li>
							<li>Household -
								$<?php echo $memb_costs[ 4 ]->cost; ?></li>
						</ul>
					</div>

					<form class="memb_reg_form" id="regForm" name="regForm" method="post"
					      action="<?php bloginfo( 'url' ); ?>/registration-review/">
						<input type="hidden" name="mb_relationship" value=1 />
						<input type="hidden" name="reg_action" value="<?php echo $reg_action?>"/>
						<fieldset class="reg_form" id="memb_type">
							<legend><span class="memb_legend">Membership Options</span></legend>
							<div class="memb_type" id="memb_type_div">
								<!-- Individual Member Option current member-->
								<input class="memb_type" id="memb_type_1" type="radio" name="memb_type" value="1" <?php echo(array_key_exists("memb_type_1", $populate_form)?"checked=\"checked\"":""); ?> />
								<label class="memb_type" for="memb_type_1">Individual $<?php echo $memb_costs[ 1 ]->cost; ?></label>

								<!-- Individual + Child(ren) Member Option -->
								<input class="memb_type" id="memb_type_2" type="radio" name="memb_type" value="2" <?php echo(array_key_exists("memb_type_2", $populate_form)?"checked=\"checked\"":""); ?> />
								<label class="memb_type" for="memb_type_2">Individual + Children
									$<?php echo $memb_costs[ 2 ]->cost; ?></label>

								<!-- Couple Member Option -->
								<input class="memb_type" id="memb_type_3" type="radio" name="memb_type" value="3" <?php echo(array_key_exists("memb_type_3", $populate_form)?"checked=\"checked\"":""); ?>/>
								<label class="memb_type" for="memb_type_3">Couple $<?php echo $memb_costs[ 3 ]->cost; ?></label>

								<!-- Household Member Option -->
								<input class="memb_type" id="memb_type_4" type="radio" name="memb_type" value="4" <?php echo(array_key_exists("memb_type_4", $populate_form)?"checked=\"checked\"":""); ?> />
								<label class="memb_type" for="memb_type_4">Household $<?php echo $memb_costs[ 4 ]->cost; ?></label>
							</div>
						</fieldset>

						<div class="spacer"></div>

						<fieldset class="reg_form" id="personal_info">
							<legend><span class="memb_legend">Your Information</span></legend>
							<div class="reg_form_row" id="personal_info_div">
								<label class="reg_first_name" id="lbl_mb_first_name" for="mb_first_name">First Name:</label>
								<input class="reg_first_name validate[required, custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="mb_first_name" name="mb_first_name" type="text"
								       value="<?php echo(isset($populate_form["mb_first_name"])? $populate_form["mb_first_name"]:"");?>" title="first_name"/>

								<label class="reg_last_name" id="lbl_mb_last_name" for="mb_last_name">Last Name:</label>
								<input class="reg_last_name validate[required, custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="mb_last_name" name="mb_last_name" type="text"
								       value="<?php echo(isset($populate_form["mb_last_name"])? $populate_form["mb_last_name"]:"");?>"/>
							</div>
							<div class="reg_form_row">
								<label class="cm_birthday" id="lbl_mb_birthday" for="mb_birthday">Birthdate:</label>
								<input class="cm_birthday validate[required, custom[date]]"
								       data-prompt-position="bottomLeft" id="mb_birthday" name="mb_birthday" type="date" 
								       value="<?php echo(isset($populate_form["mb_birthday"])? $populate_form["mb_birthday"]:"");?>"/>
								    
								<label class="reg_email" id="lbl_mb_email" for="mb_email">Email:</label>
								<input class="reg_email validate[required, custom[email]]" data-prompt-position="bottomLeft"
								       id="mb_email" name="mb_email" type="email" 
								       value="<?php echo(isset($populate_form["mb_email"])? $populate_form["mb_email"]:"");?>"/>
							</div>
							<div class="reg_form_row">
								<label class="reg_phone" id="lbl_mb_phone" for="mb_phone">Phone:</label>
								<input class="reg_phone validate[required, custom[phone]]"
								       data-prompt-position="bottomLeft" id="mb_phone" name="mb_phone" type="tel"
								       value="<?php echo(isset($populate_form["mb_phone"])? $populate_form["mb_phone"]:"");?>"/>

								<label id="lbl_mb_occupation" for="mb_occupation">Occupation:</label>
								<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
								       id="mb_occupation" name="mb_occupation" type="text" 
								       value="<?php echo(isset($populate_form["mb_occupation"])? $populate_form["mb_occupation"]:"");?>"/>
							</div>
						</fieldset>

						<div class="spacer"></div>

						<fieldset class="reg_form" id="mb_address">
							<legend><span class="memb_legend">Address</span></legend>
							<div class="reg_form_row">
								<label id="lbl_mb_addr1" for="mb_addr1">Address:</label>
								<input class="validate[required, custom[address]]" data-prompt-position="bottomLeft"
								       id="mb_addr1" name="mb_addr1" type="text" 
								       value="<?php echo(isset($populate_form["mb_addr1"])? $populate_form["mb_addr1"]:"");?>"/>

								<label id="lbl_mb_addr2" for="mb_addr2">Suite/Apt:</label>
								<input class="validate[custom[onlyLetterNumber]]" data-prompt-position="bottomLeft"
								       id="mb_addr2" name="mb_addr2" type="text" 
								       value="<?php echo(isset($populate_form["mb_addr2"])? $populate_form["mb_addr2"]:"");?>"/>
							</div>
							<div class="reg_form_row">
								<label id="lbl_mb_city" for="mb_city">City:</label>
								<input class="validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft"
								       id="mb_city" name="mb_city" type="text" 
								       value="<?php echo(isset($populate_form["mb_city"])? $populate_form["mb_city"]:"");?>"/>

								<label id="lbl_mb_state" for="mb_state">State:</label>
								<select class="validate[required]" id="mb_state" name="mb_state">
									<?php $defSel = isset($populate_form["mb_state"])? $populate_form["mb_state"]:"TX";
									echo showOptionsDrop( $states_arr, $defSel, true ); ?>
								</select>

								<label id="lbl_mb_zip" for="mb_zip">Zip:</label>
								<input id="mb_zip" class="validate[required, custom[zip]]"
								       data-prompt-position="bottomLeft" name="mb_zip" type="text" 
								       value="<?php echo(isset($populate_form["mb_zip"])? $populate_form["mb_zip"]:"");?>"/>
							</div>
						</fieldset>

						<div class="spacer" id="spouse_spacer"></div>

						<fieldset class="reg_form" id="spouse_info">
							<legend><span class="memb_legend">Spouse/Partner</span></legend>
							<div class="reg_form_row">
								<label class="reg_first_name" id="lbl_sp_first_name" for="sp_first_name">First Name:</label>
								<input class="reg_first_name validate[custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="sp_first_name" name="sp_first_name" type="text"
								       value="<?php echo(isset($populate_form["sp_first_name"])? $populate_form["sp_first_name"]:"");?>"/>

								<label class="reg_last_name" id="lbl_sp_last_name" for="sp_last_name">Last Name:</label>
								<input class="reg_last_name validate[condRequired[sp_first_name], custom[onlyLetterSp]]"
								       data-prompt-position="bottomLeft" id="sp_last_name" name="sp_last_name" type="text"
								       value="<?php echo(isset($populate_form["sp_last_name"])? $populate_form["sp_last_name"]:"");?>"/>
							</div>
							<div class="reg_form_row">
								<label class="cm_birthday" id="lbl_sp_birthday" for="sp_birthday">Birthdate:</label>
								<input class="cm_birthday validate[condRequired[sp_first_name], custom[date]]" id="sp_birthday"
								       data-prompt-position="bottomLeft" name="sp_birthday" type="date"
								       value="<?php echo(isset($populate_form["sp_birthday"])? $populate_form["sp_birthday"]:"");?>"/>

								<label class="reg_email" id="lbl_sp_email" for="sp_email">Email:</label>
								<input class="reg_email validate[condRequired[sp_first_name], custom[email]]" data-prompt-position="bottomLeft"
								       id="sp_email" name="sp_email" type="email" 
								       value="<?php echo(isset($populate_form["sp_email"])? $populate_form["sp_email"]:"");?>"/>
							</div>
							<div class="reg_form_row">
								<label class="reg_phone" id="lbl_sp_phone" for="sp_phone">Phone:</label>
								<input class="reg_phone validate[condRequired[sp_first_name], custom[phone]]" data-prompt-position="bottomLeft"
								       id="sp_phone" name="sp_phone" type="tel" 
								       value="<?php echo(isset($populate_form["sp_phone"])? $populate_form["sp_phone"]:"");?>"/>

								<label class="sp_relationship" id="lbl_sp_relationship"
								       for="sp_relationship">Relationship:</label>
								<select class="sp_relationship validate[condRequired[sp_first_name]]"
								        id="sp_relationship" name="sp_relationship">
									<?php $defSel = isset($populate_form["sp_relationship"])? $populate_form["sp_relationship"]:""?>
									<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
								</select>

							</div>
						</fieldset>

						<div class="spacer" id="family_spacer"></div>
						<!--    BEGIN 1ST FAMILY MEMBER  -->

						<fieldset class="reg_form" id="family_info">
							<legend><span class="memb_legend">Family Members</span></legend>
							<section id="child1">
								<div class="reg_form_row">
									<label class="reg_first_name" for="c1_first_name">First Name:</label>
									<input class="reg_first_name validate[custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c1_first_name" name="c1_first_name" type="text"
									       value="<?php echo(isset($populate_form["c1_first_name"])? $populate_form["c1_first_name"]:"");?>"/>

									<label class="reg_last_name" for="c1_last_name">Last Name:</label>
									<input class="reg_last_name validate[condRequired[c1_first_name], custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c1_last_name" name="c1_last_name" type="text"
									       value="<?php echo(isset($populate_form["c1_last_name"])? $populate_form["c1_last_name"]:"");?>"/></div>
								<div class="reg_form_row">
									<label class="cm_birthday" id="lbl_c_birthday" for="c1_birthday">Birthdate:</label>
									<input class="cm_birthday validate[condRequired[c1_first_name], custom[date]]"
									       data-prompt-position="bottomLeft" id="c1_birthday" name="c1_birthday" type="date"
									       value="<?php echo(isset($populate_form["c1_birthday"])? $populate_form["c1_birthday"]:"");?>"/>
									       

									<label class="child_relationship" id="lbl_c1_relationship"
									       for="c1_relationship">Relationship:</label>
									<select class="child_relationship validate[condRequired[c1_first_name]]" id="c1_relationship" name="c1_relationship">
										<?php $defSel = isset($populate_form["c1_relationship"])? $populate_form["c1_relationship"]:""; ?>
										<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
									</select>
								</div>
								<div class="reg_form_row">
									<label class="child_email" id="lbl_c1_email" for="c1_email">Email:</label>
									<input class="child_email validate[condRequired[c1_first_name], custom[email]]" data-prompt-position="bottomLeft"
									       id="c1_email" name="c1_email" type="email" 
									       value="<?php echo(isset($populate_form["c1_email"])? $populate_form["c1_email"]:"");?>"/>
								</div>
							</section>
							<!--  //END of CHILD1 -->

							<div class="spacer"></div>
							<!--    BEGIN 2ND FAMILY MEMBER  -->

							<section id="child2">
								<div class="reg_form_row">
									<label class="reg_first_name" id="lbl_c2_first_name" for="c2_first_name">First Name:</label>
									<input class="reg_first_name validate[custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c2_first_name" name="c2_first_name" type="text"
									       value="<?php echo(isset($populate_form["c2_first_name"])? $populate_form["c2_first_name"]:"");?>"/>

									<label class="reg_last_name" id="lbl_c2_last_name" for="c2_last_name">Last Name:</label>
									<input class="reg_last_name validate[condRequired[c2_first_name],custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c2_last_name" name="c2_last_name" type="text"
									       value="<?php echo(isset($populate_form["c2_last_name"])? $populate_form["c2_last_name"]:"");?>"/>
								</div>
								<div class="reg_form_row">
									<label class="cm_birthday" id="lbl_c2_birthday" for="c2_birthday">Birthdate:</label>
									<input class="cm_birthday validate[condRequired[c2_first_name],custom[date]]" id="c2_birthday" name="c2_birthday" type="date"
									       value="<?php echo(isset($populate_form["c2_birthday"])? $populate_form["c2_birthday"]:"");?>"/>

									<label class="child_relationship" id="lbl_c2_relationship"
									       for="c2_relationship">Relationship:</label>
									<select class="child_relationship validate[condRequired[c2_first_name]]" id="c2_relationship" name="c2_relationship">
										<?php $defSel = isset($populate_form["c2_relationship"])? $populate_form["c2_relationship"]:4;?> 
										<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
									</select>
								</div>
								<div class="reg_form_row">
									<label class="child_email" id="lbl_c2_email" for="c2_email">Email:</label>
									<input class="child_email validate[condRequired[c2_first_name], custom[email]]" data-prompt-position="bottomLeft"
									       id="c2_email" name="c2_email" type="email" 
									       value="<?php echo(isset($populate_form["c2_email"])? $populate_form["c2_email"]:"");?>"/>
								</div>
							</section>
							<!--  //END CHILD2 -->

							<div class="spacer"></div>
							<!--    BEGIN 3RD FAMILY MEMBER  -->

							<section id="child3">
								<div class="reg_form_row">
									<label class="reg_first_name" id="lbl_c3_first_name" for="c3_first_name">First Name:</label>
									<input class="reg_first_name validate[custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c3_first_name" name="c3_first_name" type="text"
									       value="<?php echo(isset($populate_form["c3_first_name"])? $populate_form["c3_first_name"]:"");?>"/>

									<label class="reg_last_name" id="lbl_c3_last_name" for="c3_last_name">Last Name:</label>
									<input class="reg_last_name validate[condRequired[c3_first_name], custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c3_last_name" name="c3_last_name" type="text"
									       value="<?php echo(isset($populate_form["c3_last_name"])? $populate_form["c3_last_name"]:"");?>"/>
								</div>
								<div class="reg_form_row">
									<label class="cm_birthday" id="lbl_c3_birthday" for="c3_birthday">Birthdate:</label>
									<input class="cm_birthday validate[condRequired[c3_first_name] custom[date]]" id="c3_birthday" name="c3_birthday" type="date"
									       value="<?php echo(isset($populate_form["c3_birthday"])? $populate_form["c3_birthday"]:"");?>"/>

									<label class="child_relationship" id="lbl_c3_relationship" for="c3_relationship">Relationship:</label>
									<select class="child_relationship validate[condRequired[c3_first_name]]" id="c3_relationship" name="c3_relationship">
										<?php $defSel =isset($populate_form["c3_relationship"])? $populate_form["c3_relationship"]:4; ?>
										<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
									</select>
								</div>
								<div class="reg_form_row">
									<label class="child_email" id="lbl_c3_email" for="c3_email">Email:</label>
									<input class="child_email validate[condRequired[c3_first_name],custom[email]]" data-prompt-position="bottomLeft"
									       id="c3_email" name="c3_email" type="email" 
									       value="<?php echo(isset($populate_form["c3_email"])? $populate_form["c3_email"]:"");?>"/>
								</div>
							</section>
							<!--  //END of CHILD3  -->

							<div class="spacer"></div>
							<!--    BEGIN 4th FAMILY MEMBER  -->

							<section id="child4">
								<div class="reg_form_row">
									<label class="reg_first_name" id="lbl_c4_first_name" for="c4_first_name">First Name:</label>
									<input class="reg_first_name validate[custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c4_first_name" name="c4_first_name" type="text"
									       value="<?php echo(isset($populate_form["c4_first_name"])? $populate_form["c4_first_name"]:"");?>"/>

									<label class="reg_last_name" id="lbl_c4_last_name" for="c4_last_name">Last Name:</label>
									<input class="reg_last_name validate[condRequired[c4_first_name], custom[onlyLetterSp]]"
									       data-prompt-position="bottomLeft" id="c4_last_name" name="c4_last_name" type="text"
									       value="<?php echo(isset($populate_form["c4_last_name"])? $populate_form["c4_last_name"]:"");?>"/>
								</div>
								<div class="reg_form_row">
									<label class="cm_birthday" id="lbl_c4_birthday" for="c4_birthday">Birthdate:</label>
									<input class="cm_birthday validate[condRequired[c4_first_name] custom[date]]" id="c4_birthday" name="c4_birthday" type="date"
									       value="<?php echo(isset($populate_form["c4_birthday"])? $populate_form["c4_birthday"]:"");?>"/>

									<label class="child_relationship" id="lbl_c4_relationship"
									       for="c4_relationship">Relationship:</label>
									<select class="child_relationship validate[condRequired[c4_first_name]]" id="c4_relationship" name="c4_relationship">
										<?php $defSel=isset($populate_form["c4_relationship"])? $populate_form["c4_relationship"]:4; ?>
										<?php echo showOptionsDrop( $relationship_arr, $defSel, true ); ?>
									</select>
								</div>
								<div class="reg_form_row">
									<label class="child_email" id="lbl_c4_email" for="c4_email">Email:</label>
									<input class="child_email validate[condRequired[c4_first_name], custom[email]]" data-prompt-position="bottomLeft"
									       id="c4_email" name="c4_email" type="email" 
									       value="<?php echo(isset($populate_form["c4_email"])? $populate_form["c4_email"]:"");?>"/>
								</div>
							</section>
							<!--  //END of CHILD4  -->
						</fieldset>

						<div class="spacer"></div>

						<div>
							<input class="ctxphc_button3 screen" id="reg_submit" type="submit" name="registration"
							       value="Submit"/>
						</div>

					</form>
				</div>
				<!-- entry -->
			</div>
			<!-- post -->

			<?php
		endwhile;
		endif;
		?>

	</div>


	
	<!-- content -->

<?php get_sidebar(); ?>
<?php get_footer();?>
	