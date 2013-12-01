<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="author" content="Roman S. Stepanov" />


    <link href="../css/bootstrap.min.css" rel="stylesheet"/>
	<script src="jquery.tools.min.js"></script>
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"/>
    <link href="../css/base.css" rel="stylesheet"/>

    <script type="text/javascript" src="js/jquery.js"></script>
	<script src="jquery.tools.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/raphael.js"></script>
    <script type="text/javascript" src="../js/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="../js/mapsvg.min.js?v=5.5.4"></script>
    <script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>
	<div class="location_select">
	
    <script type="text/javascript">
        if(window.location.protocol=="file:"){
            $(document).ready(function(){
              $('#main-body').prepend('<div class="alert alert-error" style="margin-top: 20px;">SVG map files can\'t be loaded when HTML file is just opened from local folder - because JavaScript don\'t have access to files on your local machine. Please upload demo to server to see maps.</div>');
            });
        }
    </script>

</head>




    <script type="text/javascript">

           var currentLocation = [geoip_latitude(), geoip_longitude()];

           $('#mapsvg-2').mapSvg({
                source:        'maps/world_with_states.svg',    // Path to SVG map
                colors: {stroke: '#aaaaaa', selected: -20, hover: 7},
                width: 1170,
                onClick: function(e,m){
                    if(this.node.id=='Mongolia') return;
                    var obj = this.mapsvg_type == 'region' ? '<b>'+this.node.id+'</b>' : 'a <b>marker</b>';
                    m.showPopover(e,'You clicked '+obj+' and this is a pop-up info box with close button. <br />You can put <ins>any</ins> <strong>HTML</strong> <em>tags</em> here. <br />Also it\'s useful for links: <a href="http://google.com" target="_blank">google.com</a>');

                    
                },
                marks:          [
                                    { c: currentLocation,
                                      attrs: {'src': 'markers/pin1_red.png'},
                                      tooltip: '<strong>You are here!</strong><br />'

                                            +geoip_city()+'<br />'
                                            +geoip_country_name()+'<br />'
                                            +'Coordinates: '+currentLocation
                                    },
                                    { c: [38.927099,-77.021713],
                                    attrs: {'src': 'markers/pin1_yellow.png'},
                                      tooltip: '<strong>Washington, DC</strong><br />This marker is set by latitude / longitude coordinates:<br />38.893438, -77.03167'
                                    },
                                    { c: [51.49763,-0.148315],
                                      attrs: {'src': 'markers/pin1_green.png'},
                                      tooltip: '<strong>London</strong><br />Coordinates: 51.49763, -0.148315'
                                    }
                                    ,
                                    { c: [33.504759,100.283203],
                                      attrs: {'src': 'markers/pin1_blue.png'},
                                      tooltip: '<strong>China</strong> is disabled for demonstration purpose -<br />so it isn\'t clickable or selectable.'
                                    }

                                 ],
                regions: {
                    Mexico: {
                                tooltip: 'You can set any colors and styles for any region.<br />',
                                attr:{fill: '#F3E4B2'}
                          },
                    Russia: {
                                tooltip: '<strong>Russia:</strong> Click here to see a popover box',
                                attr:{fill: '#FF9176'},
                                popover: 'This is info box with close button. <br />You can put <ins>any</ins> <strong>HTML</strong> <em>tags</em> here. <br />Also it\'s useful for links: <a href="http://google.com" target="_blank">google.com</a>'
                            },
                    China: {
                                disabled: true,
                                attr: {fill: '#F7D5BA'}
                           },
                    Mongolia: {
                                tooltip: '<strong>Mongolia:</strong> link to <em>google.com</em> is attached to this country. <br />Try to click! (link will open in new window).',
                                attr:{fill: '#A4DFA3', href: 'http://map/&t=1'}
                            },
                    Kazakhstan: {
                                tooltip: '<strong>Kazakhstan:</strong> tooltips can contain any HTML:<br /><img src="http://farm9.staticflickr.com/8162/7706013408_80a182713f_m.jpg"/>',
                                attr:{fill: '#F9DD7B'}
                            }
                },
                tooltipsMode:    'combined',
                zoom: 1,
                pan:1,
                responsive:1,
                zoomLimit: [-100,100]
            });

    </script>

    <pre id="js-code" style="display: none;">
var currentLocation = [geoip_latitude(), geoip_longitude()];

$('#mapsvg-2').mapSvg({

    source:        'maps/world_high.svg',    // Path to SVG map
    colors: {base: '#cccccc', stroke: '#aaaaaa', selected: 10},
    tooltipsMode: 'combined',
    zoom: true,
    pan: true,
    responsive: true,
    width: 1170,
    zoomLimit: [0,100],

    onClick: function(e,m){
        if(this.node.id=='Mongolia') return;
        var obj = this.mapsvg_type == 'region' ? '&lt;b&gt;'+this.node.id+'&lt;/b&gt;' : 'a &lt;b&gt;marker&lt;/b&gt;';
        m.showPopover(e,'You clicked '+obj+' and this is a pop-up info box with close button. &lt;br /&gt;You can put &lt;ins&gt;any&lt;/ins&gt; &lt;strong&gt;HTML&lt;/strong&gt; &lt;em&gt;tags&lt;/em&gt; here. &lt;br /&gt;Also it\'s useful for links: &lt;a href="http://google.com" target="_blank"&gt;google.com&lt;/a&gt;');
    },

    marks:          [
                        { c: currentLocation,
                          attrs: {'src': 'markers/pin1_red.png'},
                          tooltip: '&lt;strong&gt;You are here!&lt;/strong&gt;&lt;br /&gt;'

                                +geoip_city()+'&lt;br /&gt;'
                                +geoip_country_name()+'&lt;br /&gt;'
                                +'Coordinates: '+currentLocation
                        },
                        { c: [38.893438,-77.03167],
                        attrs: {'src': 'markers/pin1_yellow.png'},
                          tooltip: '&lt;strong&gt;Washington, DC&lt;/strong&gt;&lt;br /&gt;This marker is set by latitude / longitude coordinates:&lt;br /&gt;38.893438, -77.03167'
                        },
                        { c: [51.49763,-0.148315],
                          attrs: {'src': 'markers/pin1_green.png'},
                          tooltip: '&lt;strong&gt;London&lt;/strong&gt;&lt;br /&gt;Coordinates: 51.49763, -0.148315'
                        }
                        ,
                        { c: [33.504759,100.283203],
                          attrs: {'src': 'markers/pin1_blue.png'},
                          tooltip: '&lt;strong&gt;China&lt;/strong&gt; is disabled for demonstration purpose -&lt;br /&gt;so it isn't clickable or selectable.'
                        }

                     ],
    regions: {
        USA: {
                    tooltip: '&lt;strong&gt;USA:&lt;/strong&gt; As you can see, you can paint regions in any colors.&lt;br /&gt;',
                    attr:{fill: '#F3E4B2'}
              },
        Russia: {
                    tooltip: '&lt;strong&gt;Russia:&lt;/strong&gt; Click here to see a popover box',
                    attr:{fill: '#FF9176'},
                },
        China: {
                    disabled: true,
                    attr: {fill: '#F7D5BA'}
               },
        Mongolia: {
                    tooltip: '&lt;strong&gt;Mongolia:&lt;/strong&gt; link to &lt;em&gt;google.com&lt;/em&gt; is attached to this country. &lt;br /&gt;Try to click! (link will open in new window).',
                    attr:{fill: '#A4DFA3', href: 'http://google.com', target: 'blank'}
                },
        Kazakhstan: {
                    tooltip: '&lt;strong&gt;Kazakhstan:&lt;/strong&gt; tooltips can contain any HTML:&lt;br /&gt;&lt;img src=&quot;http://farm9.staticflickr.com/8162/7706013408_80a182713f_m.jpg&quot;/&gt;',
                    attr:{fill: '#F9DD7B'}
                }
    }
});

</pre>


    <div class="row" style="overflow: hidden;">

        <div class="span6">
				<a style="cursor:pointer;" id="gobackNE">return to New England</a>
				<a style="cursor:pointer;" id="gobackUSA">return to USA</a>
            <div id="mapsvg-usa"></div>
  
            <div id="mapsvg-states" style="min-height:  200px;"></div>
	
        </div>

    </div>

        <script type="text/javascript">

            $('#mapsvg-usa').mapSvg({source: 'maps/usa.svg', width: 1200, responsive: 1,
            colors: {background: 'transparent', hover: 4, selected: 10, stroke: '#4374E0'},
            tooltipsMode: 'names',
            regions: {'TX': {selected: true}},
            onClick: function(){
			$('#mapsvg-usa').hide();
			              $('#gobackNE').show();
               var file = 'usa-'+this.name.toLowerCase()+'.svg';
			             
               console.log($('#mapsvg-states').html());             
               if($('#mapsvg-states').find('svg').length){
                $('#mapsvg-usa').hide();
              	
                $('#mapsvg-states').mapSvg().destroy();

               }
               
                              $( "#gobackNE" ).click(function() {
						
							  $('#mapsvg-states').mapSvg().destroy();
							  $('#mapsvg-usa').show();
	});
		
               $('#mapsvg-states').mapSvg({
                    source : 'maps/counties/'+file,
                    //responsive: 1,
                    colors: {background: 'transparent', base: "#DDDDDD", stroke: '#ffffff'},
                    width: $('#mapsvg-usa').width(),
                    height: $('#mapsvg-usa').height(),
                    tooltipsMode: 'names',
                    zoomButtons: {show: true, location: 'right'},
                    zoom: 1,
                    pan: 1
               });
                
            }
            });
            
            


        </script>

        <script type="text/javascript">


            $('#maplinks a').on('click', function(e){
               e.preventDefault();
               var file = $(this).attr('data-svg');


               if($('#mapsvg-other').find('svg').length){
                $('#mapsvg-other').empty().mapSvg().destroy();
               }

               $('#mapsvg-other').mapSvg({
                    source : 'maps/'+file,
                    responsive: 1,
                    width: $('#mapsvg-other-cont').width(),
                    height: $('#mapsvg-other-cont').height(),
                    tooltipsMode: 'names',
                    zoomButtons: {show: true, location: 'left'},
                    zoom: 1,
                    pan: 1
               });

            });

        </script>
        <br /><br />




<div class="alert alert-info">
    &darr; Select any map from menu
</div>






<div class="row" style="overflow: hidden;">

    <div class="span3">
        <ul id="maplinks" class="nav nav-tabs nav-stacked other-maps">
            <li><a href="#" data-svg="world_high.svg">World</a></li>
            <li><a href="#" data-svg="world_with_states.svg">World with USA states</a></li>
            <li><a href="#" data-svg="usa.svg">USA</a></li>
            <li><a href="#" data-svg="usa-labels.svg">USA + short labels</a></li>
            <li><a href="#" data-svg="usa-labels-full.svg">USA + full labels</a></li>
            <li><a href="#" data-svg="europe.svg">Europe</a></li>
            <li><a href="#" data-svg="asia.svg">Middle Asia</a></li>
          
        </ul>
    </div>

    <div class="span9" id="mapsvg-other-cont">
        <div id="mapsvg-other"></div>
    </div>

</div>



<script type="text/javascript">


    $('#maplinks a').on('click', function(e){
       e.preventDefault();
       var file = $(this).attr('data-svg');


       if($('#mapsvg-other').find('svg').length){
        $('#mapsvg-other').empty().mapSvg().destroy();
       }

       $('#mapsvg-other').mapSvg({
            source : 'maps/'+file,
            responsive: 1,
            colors: {base: '#cccccc', stroke: "#999999", selected: 9, hover: 5},
            regions: {labels: {attr: {fill: '#555555'}}},
            width: $('#mapsvg-other-cont').width(),
            tooltipsMode: 'names',
            zoomButtons: {show: true, location: 'left'},
            zoom: 1,
            pan: 1
       });

    });

</script>




</div>
</body>
</html>