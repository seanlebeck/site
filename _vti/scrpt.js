$( document ).ready( 
	function ()
		{
		
		$( '#call_back_btn' ).click( function()
		{
		
			$.post( "test.php" , {} ,
				function( data )
				{
				  $( '#responseText' ).val(data);
				}
		
		);
		
		});
		
		
		
		}
	
);