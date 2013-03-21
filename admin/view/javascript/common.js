String.prototype.replaceAll = function(token, newToken, ignoreCase) {
    var str, i = -1, _token;
    if((str = this.toString()) && typeof token === "string") {
        _token = ignoreCase === true? token.toLowerCase() : undefined;
        while((i = (
            _token !== undefined? 
                str.toLowerCase().indexOf(
                            _token, 
                            i >= 0? i + newToken.length : 0
                ) : str.indexOf(
                            token,
                            i >= 0? i + newToken.length : 0
                )
        )) !== -1 ) {
            str = str.substring(0, i)
                    .concat(newToken)
                    .concat(str.substring(i + token.length));
        }
    }
return str;
};

function getQuerystring(key, defaultValue) {
	if(defaultValue == null) defaultValue = "";
	key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
	var qs = regex.exec(window.location.href);
	if(qs == null)
		return defaultValue;
	else
		return qs[1];
}


//-----------------------------------------
// Confirm Actions (delete, uninstall)
//-----------------------------------------
$(document).ready(function(){
    // Confirm Delete
    $('#form').submit(function(){
        if ($(this).attr('action').indexOf('delete',1) != -1) {
            if (!confirm('Deleting this entry will completely remove all data associated from the system. Are you sure?')) {
                return false;
            }
        }
    });
    	
    // Confirm Uninstall
    $('a').click(function(){
        if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
            if (!confirm('Uninstalling will completely remove the data associated from the system. Are you sure?')) {
                return false;
            }
        }
    });
});

console = console || {};
console.log = console.log || function(msg){};
console.dir = console.dir || function(obj){};
