function GetAsync(theUrl, callback){
    var HttpRq = new XMLHttpRequest();
    HttpRq.onreadystatechange = function() { 
        if (HttpRq.readyState == 4 && HttpRq.status == 200)
            callback(HttpRq.responseText);
    }
    HttpRq.open("GET", theUrl, true);
    HttpRq.send(null);
}
function _GET(parameterName) {
    var result = null,
    tmp = [];
    location.search.substr(1).split("&").forEach(function (item) {
    	tmp = item.split("=");
         if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
    return result;
}