<?php// example of how to use basic selector to retrieve HTML contentsinclude_once('simplehtmldom_1_5/simple_html_dom.php');//Create a DOM object?><td valign="top" align="middle" style=" text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);width:90%;background:#E5E5E5;border-bottom-right-radius: 4px;min-height: 506px;"> <!-- <span>  <div  class="zzz" valign="middle" id="zzzz" style="  color:white;  font-size:14px;  text-shadow: 0 1px 0 black;  height:30px;  line-height:30px;  width:200px;  display:inline-block;  width:100%;  -moz-border-bottom-colors: none;    -moz-border-left-colors: none;    -moz-border-right-colors: none;    -moz-border-top-colors: none;    background: linear-gradient(#233143, #3E4859) repeat scroll 0 0 #233143;    border-bottom-left-radius: 1px;    border-bottom-right-radius: 1px;    border-color: -moz-use-text-color rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15);    border-image: none;    border-style: none solid solid;    border-width: 1px 0px 1px;    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.27) inset;  ">	</div>	</span>-->		<center><div id="error_container" style="position:absolute;transition:background-color 4s ease 20s;width:90%;"></div></center><div id="alert_user" value="value not found" class="blank"><div id="support"style="margin-top:20px;"><div style="padding-left:10px;padding-top:5px;padding-bottom:5px;background:white;"><table width="100%"><tr><td > <?php if($cityurl > 0) { ?> <font style=""><?php echo $xcityid>0 && !$postable_country?"$xcityname - $xcountryname":$xcountryname; ?></font> <?php } else { ?><a style="float:left;display:inline-block;text-rendering: optimizeLegibility; color:#9B9FA7; font: 300 16px Roboto,arial,sans-serif;white-space: normal;">Showing Ads in all regions</a><?php } ?> <div><a style="text-rendering: optimizeLegibility; font: 300 14px Roboto,arial,sans-serif;float:left;white-space: normal;display:inline-block;text-decoration:none;cursor:pointer;padding-left:5px;line-height:23px;"  rel="#locationprompt" id="locationmodalInput" >Select City</a><img src="images/location.png" style="padding:5px 5px 5px 5px;;float:left;display:inline-block;" height="12px" width="12px"/></td></tr></table><div class="field"></div></div></div><center><div id="error_container" style="position:absolute;transition:background-color 4s ease 20s;width:90%;"></div></center><div id="alert_user" value="value not found" class="blank"><div id="support"style="margin-top:20px;"><div id="top" style="background:white;"><div class="field"> <?php$xcityid="1";require_once("initvars.inc.php");require_once("config.inc.php");if ($xview == "main" || $show_sidebar_always) {	$searchbox_on_top = 0;	$field_sep = " &nbsp; ";}else{	$searchbox_on_top = 1;	$field_sep = " &nbsp; ";}if($dir_sort) {	$sortcatsql = "ORDER BY catname";	$sortsubcatsql = "ORDER BY subcatname";}else{	$sortcatsql = "ORDER BY pos";	$sortsubcatsql = "ORDER BY scat.pos";}?><div id="location_reload"><?$html = file_get_html('mainsearch.inc.php');foreach($html->find('div#location') as $e)     echo $e->data . '<br>';	?></div><?php// // Load HTML from a string// //Create a DOM object// $html = new simple_html_dom();// // Load HTML from a string// $html->loadHTML($html);// foreach($html->find('div#location') as $e)    // echo $e->data . '<br>';// // Find succeededs// if ($rows) {    // echo count($rows) . " \$rows found !<br />";    // foreach ($rows as $key => $row) {        // echo "<hr />";        // $columns = $row->find('td');        // // Find succeeded        // if ($rows) {            // echo count($columns) . " \$columns found  in \$rows[$key]!<br />";            // foreach ($columns as $col) {                    // echo $col->plaintext . " | ";                // }        // }        // else            // echo " /!\ Find() \$columns failed /!\ ";    // }// }echo '</div>';?><?echo '<div id="maybe"></div>'; ?><!--<a id="GO">grab DOM</div> <script type="text/javascript">// <![CDATA[    // $("#GO").click(function() {    // $.ajaxSetup({ cache: false }); // This part addresses an IE bug. without it, IE will only load the first number and will never refresh   	// //$('#location_reload').load('#location_reload')	// load('#location_reload');    // });    // ]]></script>-->	<div><div id="location" data='{"state":"ME"},{"county":"Penobscot"}'><div id="lastknownstate" data=""><form id="mainsearchform" style="padding-bottom:80px;"action="?" class="search" data-validate="parsley" method="get" action="/search" onsubmit="return checkPostFields(this);" accept-charset="UTF-8"><div style="bottom:60px;padding:0;display:inline"><input name="stateid" id="mapdatastates" data-locations="locations" value=""><input name="countyid" id="mapdatacounty" data-locations="locations" value=""><input type="hidden" value="" name="utf8"></div><input type="hidden" name="cityid" value="<?php echo $xcityid; ?>"><input type="hidden" name="lang" value="<?php echo $xlang; ?>"><table align="left"><tr align="left" stye="padding-right:10px;float:left"><td style="position:relative;display:inline-block;float:left;"><input class="" type="search" style="margin-left:8px;width:250px;font-family:'Roboto',Arial, Helvetica, sans-serif;float:left !important;" tabindex="1" placeholder="what are we looking for?" name="search" incremental="true" autofocus="autofocus" autocomplete="off"></td><td style="position:relative;display:inline-block;float:left;"><input class="" id="pricemin" class=""  placeholder="min"  style="font-family:'Roboto',Arial, Helvetica, sans-serif;width:51px;background-image:none;margin-left:4px;display:inline-block;float:left;" type="text" name="pricemin"  > <input class="" id="pricemax" class="" placeholder="max"  style="font-family:'Roboto',Arial, Helvetica, sans-serif;width:50px;background-image:none;margin-left:4px;display:inline-block;float:left;" type="text" name="pricemax" ></td>	<?phpif ($xsubcatid > 0){?>	<?php		if ($xsubcathasprice)	{	?>		<?php echo $field_sep; ?>				<input class="minmax"  type="text" name="pricemin"  size="3"><?php echo $lang['SEARCH_TO']; ?> 		<input class="minmax"  type="text" name="pricemax" size="3" class="inputs">	<?php	}	?>	<?php	foreach ($xsubcatfields as $fldnum=>$fld)	{		if($fld['SEARCHABLE'])		{	?>		<?php echo $field_sep; ?>		<?php echo $fld['NAME']; ?>: 		<?php if ($fld['TYPE'] == 'N') { ?>			<input type="text" name="x[<?php echo $fldnum; ?>][min]" size="3"><?php echo $lang['SEARCH_TO']; ?>  			<input type="text" name="x[<?php echo $fldnum; ?>][max]" size="3">		<?php } else if ($fld['TYPE'] == "D") { ?>                  			<select name="x[<?php echo $fldnum; ?>]">			<option value="">- <?php echo $lang['ALL']; ?> -</option>			<?php			foreach ($fld['VALUES_A'] as $v)			{				echo "<option value=\"$v\">$v</option>";			}			?>			</select>		<?php } else { ?>			<input type="text" name="x[<?php echo $fldnum; ?>]" size="10">		<?php } ?>	<?php		}	}	?>	<input type="hidden" name="view" value="ads">	<input type="hidden" name="subcatid" value="<?php echo $xsubcatid; ?>">	<?php}elseif ($xcatid > 0){    		echo $field_sep; ?>		<input class="minmax" type="text" name="pricemin" class="inputs" size="3"><?php echo $lang['SEARCH_TO']; ?> 		<input class="minmax" type="text" name="pricemax" size="3" class="inputs">		<?php	$sql = "SELECT subcatid, subcatname AS subcatname			FROM $t_subcats scat			WHERE catid = $xcatid				AND enabled = '1'			$sortsubcatsql";	$scatres = mysql_query($sql);	$subcatcount = mysql_num_rows($scatres);	$show_subcats = true;	if ($shortcut_categories && $subcatcount == 1) {		    // Check if the only subcat has got the same name as the cat.	    $only_subcat = mysql_fetch_array($scatres);	    if ($only_subcat['subcatname'] == $xcatname) {	        $show_subcats = false;	    }	    	    // Reset resultset pointer.	    mysql_data_seek($scatres, 0);	}			        ?><td align="center" valign="middle">            <?php if ($show_subcats) { ?>    	<?php echo $field_sep; ?>        	<select name="subcatid">    	<option value="0">- <?php echo $xcatname; ?> -</option>    	<?php        	while ($row=mysql_fetch_array($scatres))    	{    		echo "<option value=\"$row[subcatid]\">$row[subcatname]</option>\n";    	}        	?>    	</select>		<?php } ?>			<input type="hidden" name="view" value="ads">	<input type="hidden" name="catid" value="<?php echo $xcatid; ?>"><?php}elseif ($xview == "events" || $xview == "showevent"){?>	<select><option value="0">- <?php echo $xcatname; ?> -</option></select>	<input type="hidden" name="view" value="events"><?php}else{?><td  style="position:relative;float:left;display:inline-block;"><div class="stoolbar-select-cat-dropdown"style="float:left;width:70px;">		<input id="xcatid" style="font-size:10pt;font-family: 'Roboto', Arial, Helvetica, sans-serif;cursor:pointer;width:70px;margin-left:4px;"   onfocus="this.blur()"  type="text"  name="catid" class="input" placeholder="<?php echo $lang['ALL']; ?>"  value="<?php echo "category"; ?>"  readonly="readonly" />	<img class="toppointer" style="left:30px;position:absolute;z-index:900;display:none" src="images/top.png" />	<ul id="selectcat" class="list" style="right:-80px;z-index:899;top:53px">		<option style="font-family:'Roboto',Arial, Helvetica, sans-serif;" id="choose" value="0"><?php echo $lang['ALL']; ?></option>		<input id="catidvalue" name="catid" type="hidden" style="position:relative;" value="0"><?php$sql = "SELECT catid, catname AS catname			FROM $t_cats			WHERE enabled = '1'			$sortcatsql";	$catres = mysql_query($sql);	echo "<script type='text/javascript'>\n";	echo "var dropdown = new Array();\n";	echo "</script>";	while ($row=mysql_fetch_array($catres))	{				echo "<option value=\"$row[catid]\">$row[catname]</option>\n";				echo "<script type='text/javascript'>\n";				echo "dropdown[dropdown.length] = { id: '". $row['catid'] ."', name: '". $row['catname'] ."', description: '". $row['ShopDescription'] ."'};\n";		echo "</script>";	}	?>	<?php if($enable_calendar) { ?><option value="-1"><?php echo $lang['EVENTS']; ?></option><?php } ?>	</ul>		</div>	</td>	<script type="text/javascript" language="javascript">$('#selectcat').click(function() { var choicemade = document.getElementById('xcatid').value;//alert(choicemade);//alert(dropdown.toSource());for (var i = 0; i < dropdown.length; i++) {    if (dropdown[i].name === choicemade) {        //alert(dropdown[i].id);		//$("#xsubcatid").val(dropdown[i].id);		$('#catidvalue').attr( 'value', dropdown[i].id );		//alert("#xcatid placeholder"+document.getElementById('catidvalue').value);		     }	}	});</script><style></style>		<input type="hidden" name="view" value="ads">	<?php}?>	<?php echo $field_sep; ?>			<?php	if($location_sort) $sort = "ORDER BY areaname";    else $sort = "ORDER BY pos";    	$sql = "SELECT areaname FROM $t_areas WHERE cityid = $xcityid $sort";	$area_res = mysql_query($sql);	if (mysql_num_rows($area_res))	{	?>	<?php echo $field_sep; ?><td  style="position:relative;float:left;display:inline-block;"><div class=""  style="float:left;margin-left:4px;width:120px;">		</div>	</td><!--<td  style="position:relative;float:left;display:inline-block;"><div class="stoolbar-select-distance-dropdown" style="margin-left:4px;float:middle;width:20px;margin-right:10px;">	<input id="xdistanceid" style="margin-left:4px;font-size:10pt;font-family: ''Roboto'', Arial, Helvetica, sans-serif;text-align:center;cursor:pointer;width:50px;padding-left:4px;" onfocus="this.blur()"  type="text"  name="distance" class="input" value="<?php echo "miles" ?>"  readonly="readonly" />		<img class="toppointer" style="left:30px;position:absolute;z-index:900;display:none" src="images/top.png" />		<ul  id="selectdistance" class="list" style="right:-100px;z-index:900;top:53px">		<option  id="choosearea" value="0\"></option></span>			-->	<?php		//$distance_ddown = array('20','40','60','100','200','300','500','1000');					//	while($distance_row = mysql_fetch_array($distance_ddown))	//	foreach($distance_ddown as $distance_row)		// {			// echo "<option value=\"$distance_row\"";			// if ($_GET['distance'] == $distance_row) echo " selected";			// echo ">$distance_row</option>";		// }				?>			</ul>	</div>	</td>	</tr>			<tr align="middle"><td><div style="position:relative !important;margin-left:20px;">	<button  id="gogo"  style="	height:35px;width:100px;margin-top:8px;left:0px;margin-left:30px;position:relative;float:middle;display:inline-block;z-index: 500 !important;"value="<?php echo $lang['BUTTON_SEARCH']; ?>"id="abar_button_opt" class="ab_button"type="submit"><span id="ab_opt_icon" class="ab_icon">search</span></button>	<button   style="	height:35px;width:100px;margin-top:8px;left:0px;padding-left:20px;position:relative;float:middle;display:inline-block;z-index: 500 !important;"value="<?php echo $lang['BUTTON_SEARCH']; ?>"id="abar_button_opt" class="ab_button" type="submit"><div <span id="ab_opt_icon" class="ab_icon">shuffle</span></button></div>		</td></tr>		</table>	</div></span>	<?php 	}			$sql2 = "SELECT a.Latitude, a.Longitude, a.City, a.StateFullName, a.ZipCode, a.State					FROM vivaru_locations a";			$res = mysql_query($sql2);			if (mysql_num_rows($res))			{					$other_index = 1;						echo "<script type='text/javascript'>\n";						echo "var areas = new Array();\n";						echo "</script>";										while ($row = mysql_fetch_array($res))				{					$other_index++;																							echo "<script type='text/javascript'>\n";									echo "areas[areas.length] = { zipcode: '". $row['ZipCode'] ."', statenamefull: '". $row['StateFullName'] ."', cityname: '". $row['City'] ."'};\n";							echo "</script>";				}				}	?>	</form> <script language="javascript">$(function(){   $(".sinput").each(function(){     $(this).keyup(function(){       var id = $(this).attr("id"); //VALUE OF INPUT ID Ex: <input id="name">       var v = $(this).val(); //INPUT TEXT VALUE       var data = id+"="+v; //DATA TO GO TO THE AJAX FILE Ex:(name=wcet)       $.ajax({         type: "POST",         url: "validate.php", //AJAX FILE		 cache: false,         data: data+"&single=true",         success: function(e){ //"e" IS THE DATA FROM "validate.php"           $("span#"+id).html(e); //ECHOS DATA FROM "validate.php" NEXT TO THE INPUT IF NEEDED         }       });     });   });});</script><script>function checkPostFields(form) {$('#mapdata').load('js/mapsvg.min.js #jsmaplocation');//alert("hi");var msg = '';var value_missing = false;var value_city_match = false;var value_city_nomatch = false;for (var i = 0; i < areas.length; i++) {//alert("u entered: "+form.elements['xxareaid'].value);//alert("areas array: "+areas[i].areaname);//alert(areas.toSource());var cityval = areas[i].cityname;var stateval = areas[i].statenamefull;var zipval = areas[i].zipcode;var inputval = form.elements['area'].value;var n=inputval.indexOf(zipval); var o=inputval.indexOf(stateval); var p=inputval.indexOf(cityval); if (n >= 0 || o >= 0 || p >= 0)	{	//alert(areas[i].areaname.toLowerCase());				value_city_match = true;	} else {	//alert("no match")	 value_city_nomatch = true;	}	}		 if (value_city_match != true) {		//alert("no match")		$("#xxareaid").toggleClass('sinput sinput-error');	$("#alert_user_msg").toggleClass('blank blur-out');	$("#location_error").append("unable to find that location in <?php echo $site_state; ?>");$( "#location_error" ).fadeIn( 1000, function() {});			 // $( "#error" ).toggle( "fast", function(-) {// // Animation complete.// });//$(".msgerror").show();	//	$('#error').css('border', '1px solid #D6E9C6');	//	$('#error').css('background-color','#000');				$("#xxareaid").val("invalid location");	  	//		alert("no match")	msg += '<?php echo $lang['ERROR_POST_FILL_CITY']; ?>\n';		value_missing = true;		return false;}		if (form.elements['addesc'].value == ''			|| form.elements['email'].value == ''			<?php if ($image_verification) { ?>			|| form.elements['captcha'].value == ''			<?php } ?>			) {		msg += '<?php echo $lang['ERROR_POST_FILL_ALL']; ?>\n';		value_missing = true;	}	if (form.elements['addesc'].value == ''			|| form.elements['email'].value == ''			<?php if ($image_verification) { ?>			|| form.elements['captcha'].value == ''			<?php } ?>			) {		msg += '<?php echo $lang['ERROR_POST_FILL_ALL']; ?>\n';		value_missing = true;	}	if (!form.elements['agree'].checked) {		msg += '<?php echo $lang['ERROR_POST_AGREE_TERMS']; ?>\n';	}	<?php 	if(count($xsubcatfields)) {		foreach($xsubcatfields as $fldnum=>$fld) {		    if ($fld['REQUIRED']) {	?>	            if (!value_missing && !form.elements['x[<?php echo $fldnum; ?>]'].value) {            		msg = '<?php echo $lang['ERROR_POST_FILL_ALL']; ?>\n' + msg;            		value_missing = true;	            }	<?php	        }	    }	}	?>	if (msg != '') {		alert(msg);		return false;	}}</script>	<script>				$('#selectdistance').click(function() { 		var chosencat = document.getElementById('yyyy').value	//	alert(chosencat);		if(chosencat=="Distance"){				//alert("cool"+document.getElementById('hhhh').value);		$("#yyyy").val("100");		//alert(document.getElementById('hhhh').value);		}});</script><script>$(function(){	$('.stoolbar-select-area-dropdown').styleddropdown();	$('.stoolbar-select-cat-dropdown').styleddropdown();	$('.stoolbar-select-distance-dropdown').styleddropdown();//	$('.area-select').styleddropdown();});</script><script>$('#xcatid').click(function(event) {         if($('#xareaid,list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }        if($('#xdistanceid,list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }})$('#xareaid').click(function(event) {         if($('#xcatid,list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }        if($('#distanceid,list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }})$('#xdistanceid').click(function(event) {         if($('#xcatid,list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }        if($('#xareaid,list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }})</script><script>$(document).click(function(event) {     if($(event.target).parents().index($('.field')) == -1) {        if($('.list').is(":visible")) {            $('.list').hide()			$('.toppointer').hide()        }    }        })</script><script>(function($){	$.fn.styleddropdown = function(){		return this.each(function(){			var obj = $(this)			obj.find('.input').click(function() { //onclick event, 'list' fadein			obj.find('.toppointer').fadeIn(400);			obj.find('.list').fadeIn(400);						$(document).keyup(function(event) { //keypress event, fadeout on 'escape'				if(event.keyCode == 27) {				obj.find('.toppointer').fadeOut(400);				obj.find('.list').fadeOut(400);				}			});						obj.find('.list').hover(function(){ },				function(){					$(this).fadeOut(400);					obj.find('.toppointer').fadeOut(400);				});			});												obj.find('.list option').click(function() { //onclick event, change field value with selected 'list' item and fadeout 'list'			obj.find('.input')				.val($(this).html())				.css({								'color':'#333'				});			obj.find('.toppointer').fadeOut(400);			obj.find('.list').fadeOut(400);			});		});	};})(jQuery);</script><script>// hide the error message and container$(document).click(function() {     // alert("HI");			$('.blur-out').hide()		$( "#location_error" ).fadeOut( 1000, function() {});	});     </script></div></div></div></div></span>