<?php include './header.php';?>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>	
<script type="text/javascript" >
var linkElement = document.createElement("link");
linkElement.rel = "stylesheet";
linkElement.href = "./ftree/css/filetree.css"; //Replace here
document.head.appendChild(linkElement);

$(document).ready( function() {

	$( '#container' ).html( '<ul class="filetree start"><li class="wait">' + 'Generating Tree...' + '<li></ul>' );
	
	getfilelist( $('#container') , '<?php echo $user_dirname; ?>' );
	
	function getfilelist( cont, root ) {
	
		$( cont ).addClass( 'wait' );
			
		$.post( 'Foldertree.php', { dir: root }, function( data ) {
	
			$( cont ).find( '.start' ).html( '' );
			$( cont ).removeClass( 'wait' ).append( data );
			if( 'Sample' == root ) 
				$( cont ).find('UL:hidden').show();
			else 
				$( cont ).find('UL:hidden').slideDown({ duration: 500, easing: null });
			
		});
	}
	
	$( '#container' ).on('click', 'LI A', function() {
		var entry = $(this).parent();
		
		if( entry.hasClass('folder') ) {
			if( entry.hasClass('collapsed') ) {
						
				entry.find('UL').remove();
				getfilelist( entry, escape( $(this).attr('rel') ));
				entry.removeClass('collapsed').addClass('expanded');
			}
			else {
				
				entry.find('UL').slideUp({ duration: 500, easing: null });
				entry.removeClass('expanded').addClass('collapsed');
			}
		} else {
			$( '#selected_file' ).text( "File:  " + $(this).attr( 'rel' ));
		}
	return false;
	});
	
});
</script>

<div id="container"> </div>
<div id="selected_file"></div>

<?php include './footer.php'; ?>
