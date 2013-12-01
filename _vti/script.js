$( document ).ready( 
	function ()
		{
		
		$( '#call_back_btn' ).click( function()
		{
		
			$.post( "ajax.php" , {} ,
				function( data )
				{
				  $( '#responseText' ).val(data);
				}
		
		);
		
		});
		
		
		
		}
	
);