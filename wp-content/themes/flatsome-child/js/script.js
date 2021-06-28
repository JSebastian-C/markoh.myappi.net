function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

jQuery(function($){
	$.post(
		'/wp-admin/admin-ajax.php?action=custom_ajax&caction=manage_courts',
		null,
		function(r){
			if(r.success){
				$(".menu-item-191").show();
			}else{
				$(".menu-item-191").hide();
			}
		},
		"json"
	)
	$('li.menu-item-192 a').on( 'click',function(){
		if(confirm("¿Desea salir de la aplicación?")){
			$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=logout',
					null,
					function(r){
						alert(r.message);
						if(r.success)
							window.location.href="/clogin";
					},"json")
		}
	});
	
	if(window.location.pathname=='/'){
		setTimeout(function(){
			if(getCookie('first_time')==1){
				if(jQuery('body').hasClass( 'logged-in' )){
					window.location.href="/start";
				}else{					
					window.location.href="/clogin";
				}
			}else{
				window.location.href="/intro";
			}
		},3000)
	}
});
