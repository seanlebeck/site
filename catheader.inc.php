<!--STARTING THE SIGN IN AJAX MOD - SEAN--><style>.login_modal {    display:none;    opacity:0.8;	position:relative;	z-index:999;  }  .login_modal h2 {    margin:0px;    padding:5px;	padding-left:0px;    font-size:14px;	  } .logout_modal {    background-color:#fff;	width: 500px;	padding:10px;	padding-left:0px;    display:none;    text-align:left;    border:1px solid #333;    opacity:0.8;	border-radius: 6px;    -moz-border-radius:6px;    -webkit-border-radius:6px;    -moz-box-shadow: 0 0 50px #ccc;    -webkit-box-shadow: 0 0 50px #ccc;  }  .logout_modal h2 {    margin:0px;    padding:5px;	padding-left:0px;    font-size:14px;  }  .master_modal{	left: 366.5px;    top: 109px;      padding: 30px 30px;    top: 0;	color:#737373;    width: auto;    z-index: 1101;	background: none repeat scroll 0 0 padding-box #FFFFFF;    border: medium none rgba(0, 0, 0, 0);    box-shadow: 0 0 40px rgba(0, 0, 0, 0.4);    outline: 0 none;    position: absolute;	overflow:hidden;	display:block;  }    .master_modal2{    background: linear-gradient(#FFFFFF, #F2F6F9) repeat scroll 0 0 rgba(0, 0, 0, 0);    border-radius: 6px 6px 0px 0px;    box-shadow: 0 0 0 1px rgba(14, 41, 57, 0.12), 0 2px 5px rgba(14, 41, 57, 0.44), 0 -1px 2px rgba(14, 41, 57, 0.15) inset;	padding:0px;		margin-top:80px;    display:none;    text-align:left;    border:1px solid #FFFFFF;    opacity:1;	overflow:hidden;		border-radius: 6px;    -moz-border-radius:6px;    -webkit-border-radius:6px;    -moz-box-shadow: 0 0 50px #ccc;    -webkit-box-shadow: 0 0 50px #ccc;  }      .master_modal1{    background: linear-gradient(#FFFFFF, #F2F6F9) repeat scroll 0 0 rgba(0, 0, 0, 0);    border-radius: 6px 6px 0px 0px;    box-shadow: 0 0 0 1px rgba(14, 41, 57, 0.12), 0 2px 5px rgba(14, 41, 57, 0.44), 0 -1px 2px rgba(14, 41, 57, 0.15) inset;	padding-bottom:10px;	padding:0px;		margin-top:0px;    display:none;    text-align:left;    border:1px solid #FFFFFF;    opacity:1;	overflow:auto;	height:80%;	border-radius: 6px;    -moz-border-radius:6px;    -webkit-border-radius:6px;    -moz-box-shadow: 0 0 50px #ccc;    -webkit-box-shadow: 0 0 50px #ccc;  }  </style>      <!-- This is for login ajax popout --><div class="login_modal" id="loginprompt"><?php include("zajaxlogin.php"); ?><div style="padding:5px;"></div></div><script>$(document).ready(function() {    var triggers = $(".loginmodalInput").overlay({      // some mask tweaks suitable for modal dialogs      mask: {        color: '#ebecff',        loadSpeed: 200,        opacity: 0.9      },      closeOnClick: true  });  });</script><div class="master_modal" id="allsubcatsprompt"><?php include("categories/allsubcatsajax.php"); ?><div ></div></div><script>$(document).ready(function() {    var triggers = $("#allsubcatsmodalInput").overlay({    // some mask tweaks suitable for modal dialogs    s mask: {      color: '#ebecff',      loadSpeed: 200,      opacity: 0.9      },     closeOnClick: true   });  }); </script><div class="master_modal" id="autoprompt"><img  style="position:absolute;float:right;z-index:9999;" src='images/xclose.png;'><?php include("categories/autoajax.php"); ?><div style=""></div></div><!--<img  style="position:absolute;float:right;z-index:9999;" src='images/xclose.png;'>--> 	<!--  Joe start for sale --><!--  Joe start for sale --><!--  Joe start for sale --><!--  Joe start for sale --><!--  Joe start for sale --><center><div class="master_modal" id="forsaleprompt"><?php include("categories/forsaleajax.php"); ?><div style=""></center></div></div><script>$(document).ready(function() {    var triggers = $("#forsalemodalInput").overlay({      // some mask tweaks suitable for modal dialogs      mask: {        color: '#ebecff',        loadSpeed: 200,        opacity: 0.9      },      closeOnClick: true  });  });</script><!--  Joe end for sale --><!--  Joe start for sale -->    <!--  Joe end for sale --><!--  Joe start for sale -->  <div class="master_modal" id="petsprompt"> <?php include("categories/petsajax.php"); ?> <div style=""> </div> </div> <script>  $(document).ready(function() {   var triggers = $("#petsmodalInput").overlay({ // some mask tweaks suitable for modal dialogs     mask: {       color: '#ebecff',    loadSpeed: 200,     opacity: 0.9      },     closeOnClick: true  });  });  </script> <!--  Joe end for sale --> <!--  Joe start for sale -->  <div class="master_modal" id="housingprompt"> <?php include("categories/housingajax.php"); ?> <div style=""> </div> </div> <script>  $(document).ready(function() {    var triggers = $("#housingmodalInput").overlay({   // some mask tweaks suitable for modal dialogs     mask: {       color: '#ebecff',     loadSpeed: 200,     opacity: 0.9      },     closeOnClick: true  });  });</script><!--  Joe end for sale --><!--  Joe start for sale -->  <div class="master_modal" id="communityprompt"> <?php include("categories/communityajax.php"); ?> <div style=""> </div> </div> <script>$(document).ready(function() {   var triggers = $("#communitymodalInput").overlay({    // some mask tweaks suitable for modal dialogs     mask: {        color: '#ebecff',    loadSpeed: 200, opacity: 0.9      },   closeOnClick: true  });  }); </script><!--  Joe end for sale --><!--  Joe start for sale -->   <div class="master_modal" id="jobsprompt"> <?php include("categories/jobsajax.php"); ?> <div style=""> </div> </div> <script> $(document).ready(function() {   var triggers = $("#jobsmodalInput").overlay({    // some mask tweaks suitable for modal dialogs       mask: {      color: '#ebecff',      loadSpeed: 200,    opacity: 0.9    },     closeOnClick: true  }); }); </script><!--  Joe end for sale --><!--  Joe start for sale -->  <div class="master_modal" id="servicesprompt"> <?php include("categories/servicesajax.php"); ?> <div style=""> </div> </div> <script>$(document).ready(function() {    var triggers = $("#servicesmodalInput").overlay({    // some mask tweaks suitable for modal dialogs     mask: {     color: '#ebecff',      loadSpeed: 200,     opacity: 0.9      },      closeOnClick: true });   }); </script><!--  Joe end for sale ----------------------------------------------------------------><!--  Joe end for sale ----------------------------------------------------------------><!--  Joe end for sale ----------------------------------------------------------------><!--  Joe end for sale ----------------------------------------------------------------><!--  Joe end for sale ----------------------------------------------------------------><div class="master_modal" id="logoutprompt"><?php include("ajaxlogout.php"); ?><div style="padding:5px;"></div></div><script>$(document).ready(function() {    var triggers = $("#logoutmodalInput").overlay({      // some mask tweaks suitable for modal dialogs      mask: {        color: '#ebecff',        loadSpeed: 200,        opacity: 0.9      },      closeOnClick: true  });  });</script><!--ENDING THE AJAX SIGN IN  MOD SEAN --><style>    .map_modal{	left: 366.5px;c    top: 109px;      padding: 30px 30px;    top: 0;	color:#737373;    width: auto;    z-index: 1101;	background: none repeat scroll 0 0 padding-box #FFFFFF;    border: medium none rgba(0, 0, 0, 0);    box-shadow: 0 0 40px rgba(0, 0, 0, 0.4);    outline: 0 none;    position: absolute;	overflow:hidden;	display:none;  }</style><script>$(document).ready(function() {  var triggers = $("#locationmodalInput").overlay({    // some mask tweaks suitable for modal dialogs     mask: {      color: '#ebecff',      loadSpeed: 200,      opacity: 0.9      },     closeOnClick: true   });  });	</script>	<script>	  // $("#locationmodalInput").live('click', function() {      // alert("before clicked map_modal - "+($('.map_modal').css('display')));  // $("#locationprompt").css('display','block')    // alert("after clicked map_modal - "+($('.map_modal').css('display')));  // });	</script><div style="position:absolute;"><div class="map_modal" id="locationprompt"><div class="location_select">	    <pre id="js-code" style="display: none;">var currentLocation = [geoip_latitude(), geoip_longitude()];$183('#mapsvg-2').mapSvg({    source:        'maps/world_high.svg',    // Path to SVG map    colors: {base: '#cccccc', stroke: '#aaaaaa', selected: 10},    tooltipsMode: 'combined',    zoom: true,    pan: true,    responsive: true,    width: 1170,    zoomLimit: [0,100],    onClick: function(e,m){        if(this.node.id=='Mongolia') return;        var obj = this.mapsvg_type == 'region' ? '&lt;b&gt;'+this.node.id+'&lt;/b&gt;' : 'a &lt;b&gt;marker&lt;/b&gt;';        m.showPopover(e,'You clicked '+obj+' and this is a pop-up info box with close button. &lt;br /&gt;You can put &lt;ins&gt;any&lt;/ins&gt; &lt;strong&gt;HTML&lt;/strong&gt; &lt;em&gt;tags&lt;/em&gt; here. &lt;br /&gt;Also it\'s useful for links: &lt;a href="http://google.com" target="_blank"&gt;google.com&lt;/a&gt;');    },    regions: {        USA: {                    tooltip: '&lt;strong&gt;USA:&lt;/strong&gt; As you can see, you can paint regions in any colors.&lt;br /&gt;',                    attr:{fill: '#F3E4B2'}              },    }});</pre>    <div class="row" style="overflow: hidden;">        <div class="span6">				<a style="cursor:pointer;" id="gobackNE">return to New England</a>				<a style="cursor:pointer;" id="gobackUSA">return to USA</a>            <div id="mapsvg-usa"></div>              <div id="mapsvg-states" style="min-height:  200px;"></div>	        </div>    </div>        <script type="text/javascript">    $183('#gobackNE').hide();		$183('#mapsvg-states').hide();              	            $183('#mapsvg-usa').mapSvg({source: 'maps/usa.svg', width: 1200, responsive: 1,            colors: {background: 'transparent', hover: 4, selected: 10, stroke: '#4374E0'},            tooltipsMode: 'names',            regions: {'TX': {selected: true}},            onClick: function(){			$183('#mapsvg-usa').hide();			$183('#mapsvg-states').show();			              $183('#gobackNE').show();               var file = 'usa-'+this.name.toLowerCase()+'.svg';			                            console.log($183('#mapsvg-states').html());                            if($183('#mapsvg-states').find('svg').length){                $183('#mapsvg-usa').hide();              	                $183('#mapsvg-states').mapSvg().destroy();               }                                             $183( "#gobackNE" ).click(function() {													  $183('#mapsvg-states').mapSvg().destroy();							  $183('#mapsvg-usa').show();	});		               $183('#mapsvg-states').mapSvg({                    source : 'maps/counties/'+file,                    //responsive: 1,                    colors: {background: 'transparent', base: "#DDDDDD", stroke: '#ffffff'},                    width: $183('#mapsvg-usa').width(),                    height: $183('#mapsvg-usa').height(),                    tooltipsMode: 'names',                    zoomButtons: {show: true, location: 'right'},                    zoom: 1,                    pan: 1               });                            }            });                                </script>        <script type="text/javascript">            $183('#maplinks a').on('click', function(e){               e.preventDefault();               var file = $183(this).attr('data-svg');               if($183('#mapsvg-other').find('svg').length){                $183('#mapsvg-other').empty().mapSvg().destroy();               }               $183('#mapsvg-other').mapSvg({                    source : 'maps/'+file,                    responsive: 1,                    width: $183('#mapsvg-other-cont').width(),                    height: $183('#mapsvg-other-cont').height(),                    tooltipsMode: 'names',                    zoomButtons: {show: true, location: 'left'},                    zoom: 1,                    pan: 1               });            });        </script>        <br /><br /></div></div></body></html><div style="padding:5px;"></div></div></div>