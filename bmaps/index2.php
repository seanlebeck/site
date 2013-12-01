<!DOCTYPE HTML>
<html>
<head>

    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="css/bootstrap-responsive.css" rel="stylesheet"/>
    <link href="css/base.css" rel="stylesheet"/>

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/raphael.js"></script>
    <script type="text/javascript" src="js/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="js/mapsvg.min.js?v=5.5.4"></script>
    <script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>
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

    <button class="btn btn-info" onclick="$('#js-code').toggle();return false;">Show JavaScript code for the map above</button>
<br /><br /><br />




 
    <div class="row" style="overflow: hidden;">

        <div class="span6">
            <div id="mapsvg-usa"></div>
        </div>
        <div class="span6" id="mapsvg-states-cont">
           
        </div>

    </div>
<iframe src="http://www.makeaclickablemap.com/map.php?e60892c8bf7a7f6c96e1546401a05ef0f3c71c15" frameborder="0" scrolling="no" height="472" width="630"></iframe>
        <script type="text/javascript">
$(document).ready(function(){
$('#mapsvg-states').hide;
});
            $('#mapsvg-usa').mapSvg({
			source: 'maps/usa.svg', width: 1200, responsive: 1,
            colors: {background: 'transparent', hover: 4, selected: 10, stroke: '#4374E0'},
            tooltipsMode: 'names',
			
            regions: {'TX': {selected: true}},
            onClick: function(){

               var file = 'usa-'+this.name.toLowerCase()+'.svg';
               
               console.log($('#mapsvg-states').html());
               
               if($('#mapsvg-states').find('svg').length){
                $('#mapsvg-states').mapSvg().destroy();
               }
               
                              

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
            
            

           $('#mapsvg-states').mapSvg({
                source : 'maps/counties/usa-tx.svg',
                responsive: 1,
                colors: {background: 'transparent', stroke: '#ffffff'},
                width: $('#mapsvg-usa').width(),
                tooltipsMode: 'names',
                zoomButtons: {show: true, location: 'right'},
                zoom: 1,
                pan: 1
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