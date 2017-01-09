/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 5/17/2015
 * Time: 7:36 PM
 * version: 0.5
 */

/*jslint browser:true */

var $j = jQuery.noConflict();

$j(document).ready(function () {
    "use strict";
    var $formID = $j('#regForm');
    $j($formID).validationEngine({promptPosition: "centerRight"});
    $j($formID).validationEngine('attach');
});

$j(document).ready(function () {
    "use strict";
    if ($j("#memb_type_1:checked[type='radio']").val() === "1") {
        $j("#spouse_spacer").hide();
        $j("#spouse_info").hide();
        $j("#family_spacer").hide();
        $j("#family_info").hide();
    } else if ($j("#memb_type_2:checked[type='radio']").val() === "2") {
        $j("#spouse_spacer").hide();
        $j("#spouse_info").hide();
        $j("#family_spacer").show();
        $j("#family_info").show();
    } else if ($j("#memb_type_3:checked[type='radio']").val() === "3") {
        $j("#spouse_spacer").show();
        $j("#spouse_info").show();
        $j("#family_spacer").hide();
        $j("#family_info").hide();
    } else if ($j("#memb_type_4:checked[type='radio']").val() === "4") {
        $j("#spouse_spacer").show();
        $j("#spouse_info").show();
        $j("#family_spacer").show();
        $j("#family_info").show();
    } else {
        $j("#spouse_spacer").hide();
        $j("#spouse_info").hide();
        $j("#family_spacer").hide();
        $j("#family_info").hide();
    }
    $j("form input[name='memb_type'][type='radio']").click(function () {
        if ($j("#memb_type_1:checked[type='radio']").val() === "1") {
            $j("#spouse_spacer").hide();
            $j("#spouse_info").hide();
            $j("#family_spacer").hide();
            $j("#family_info").hide();
        } else if ($j("#memb_type_2:checked[type='radio']").val() === "2") {
            $j("#spouse_spacer").hide();
            $j("#spouse_info").hide();
            $j("#family_spacer").show();
            $j("#family_info").show();
        } else if ($j("#memb_type_3:checked[type='radio']").val() === "3") {
            $j("#spouse_spacer").show();
            $j("#spouse_info").show();
            $j("#family_spacer").hide();
            $j("#family_info").hide();
        } else if ($j("#memb_type_4:checked[type='radio']").val() === "4") {
            $j("#spouse_spacer").show();
            $j("#spouse_info").show();
            $j("#family_spacer").show();
            $j("#family_info").show();
        } else {
            $j("#spouse_spacer").hide();
            $j("#spouse_info").hide();
            $j("#family_spacer").hide();
            $j("#family_info").hide();
        }
    });
});

$(document).ready(function () {
    alert('hello world, We are Ready');
    <?php
    foreach ( $orig_user_meta as $orig_meta_key => $orig_meta_val ){
        // echo "//testing $orig_meta_key => $orig_meta_val[0]\n";
        if (($orig_meta_key == "memb_type") || ($orig_meta_key == "membership_type") || ($orig_meta_key == "mb_membership_type")|| ($orig_meta_key == "membership")  ){
            $memb_radio_tag = "memb_type_" . $orig_meta_val[0];
	  		#echo "\tdocument.getElementById(\"memb_radio_tag\").value = \"checked\" \n";
            echo "\$(\"#$memb_radio_tag\").prop(\"checked\", true );\n\t";
        }
        else {
            if ($id_name = $form_data_map[$orig_meta_key]){
            # echo "\tdocument.getElementById(\"$id_name\").value = \"$orig_meta_val[0]\" \n";
                echo "\$(\"#$id_name\").val(\"$orig_meta_val[0]\") \n\t";
            }
        }
    }
    ?>
});