<?php


//This is the access privilege file
$is_admin=false;

$current_user_roles='H5';

$current_user_parent_role_seq='H1::H2::H3::H4::H5';

$current_user_profiles=array(2,3,);

$profileGlobalPermission=array('1'=>1,'2'=>1,);

$profileTabsPermission=array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>1,'22'=>0,'23'=>0,'24'=>1,'25'=>0,'26'=>0,'27'=>0,'30'=>0,'31'=>0,'32'=>0,'33'=>0,'34'=>0,'35'=>0,'36'=>1,'37'=>0,'38'=>0,'39'=>0,'40'=>0,'41'=>0,'42'=>0,'43'=>0,'45'=>0,'46'=>0,'47'=>0,'48'=>0,'49'=>0,'50'=>1,'51'=>0,'52'=>0,'54'=>0,'55'=>0,'56'=>1,'57'=>0,'28'=>0,);

$profileActionPermission=array(2=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>1,6=>1,10=>0,),4=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>1,6=>1,8=>1,10=>0,),6=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>1,6=>1,8=>1,10=>0,),7=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>1,6=>1,8=>1,9=>0,10=>0,),8=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,6=>1,),9=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,),13=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>1,6=>1,8=>1,10=>0,),14=>array(0=>1,1=>1,2=>1,3=>0,4=>0,7=>1,5=>1,6=>1,10=>0,),15=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,),16=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,),18=>array(0=>1,1=>1,2=>1,3=>0,4=>0,7=>1,5=>1,6=>1,10=>0,),19=>array(0=>1,1=>1,2=>1,3=>0,4=>0,7=>1,5=>1,6=>1,10=>0,),20=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,),21=>array(0=>1,1=>1,2=>1,3=>0,4=>1,7=>1,5=>0,6=>0,),22=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,),23=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,),25=>array(0=>1,1=>0,2=>0,3=>0,4=>0,7=>0,6=>0,11=>0,),26=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,),36=>array(0=>1,1=>1,2=>1,3=>0,4=>1,7=>1,5=>1,6=>1,8=>1,12=>1,13=>1,),37=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,10=>0,),38=>array(0=>1,1=>1,2=>1,3=>0,4=>0,7=>1,5=>0,6=>0,10=>0,),41=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,10=>0,),45=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,),46=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,10=>0,),47=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,10=>0,),48=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,5=>0,6=>0,10=>0,),50=>array(0=>0,1=>0,2=>0,3=>0,4=>0,7=>0,),40=>array(5=>1,6=>1,10=>0,),);

$current_user_groups=array(2,3,4,);

$subordinate_roles=array();

$parent_roles=array('H1','H2','H3','H4',);

$subordinate_roles_users=array();

$user_info=array('user_name'=>'ibrahim_g','is_admin'=>'off','user_password'=>'$1$ib000000$FoS7.bggb0DpOw/oRUhFc/','confirm_password'=>'$1$ib000000$FoS7.bggb0DpOw/oRUhFc/','first_name'=>'Ibrahim','last_name'=>'Ghazal','roleid'=>'H5','email1'=>'ibrahim.ghazal@gallagher-me.com','status'=>'Active','activity_view'=>'Today','lead_view'=>'Today','hour_format'=>'12','end_hour'=>'','start_hour'=>'08:00','title'=>'Sales &amp; Technical Engineer','phone_work'=>'','department'=>'Sales','phone_mobile'=>'+961 71 189970','reports_to_id'=>'5','phone_other'=>'','email2'=>'','phone_fax'=>'','secondaryemail'=>'','phone_home'=>'','date_format'=>'dd-mm-yyyy','signature'=>'ibrahim_sign.png','description'=>'','address_street'=>'','address_city'=>'Saida','address_state'=>'','address_postalcode'=>'','address_country'=>'','accesskey'=>'pwYOPpmYEfPufo38','time_zone'=>'Asia/Beirut','currency_id'=>'1','currency_grouping_pattern'=>'123,456,789','currency_decimal_separator'=>'.','currency_grouping_separator'=>',','currency_symbol_placement'=>'$1.0','imagename'=>'','internal_mailer'=>'0','theme'=>'woodspice','language'=>'en_us','reminder_interval'=>'','no_of_currency_decimals'=>'2','truncate_trailing_zeros'=>'1','dayoftheweek'=>'Monday','callduration'=>'5','othereventduration'=>'5','calendarsharedtype'=>'public','default_record_view'=>'Detail','leftpanelhide'=>'0','rowheight'=>'medium','defaulteventstatus'=>'Select an Option','defaultactivitytype'=>'Select an Option','hidecompletedevents'=>'0','phone_crm_extension'=>'107','is_owner'=>'0','layout'=>'','defaultcalendarview'=>'MyCalendar','currency_name'=>'USA, Dollars','currency_code'=>'USD','currency_symbol'=>'&#36;','conv_rate'=>'1.00000','record_id'=>'','record_module'=>'','id'=>'9');
?>