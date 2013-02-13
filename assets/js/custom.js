function postwith (to,p) {
  var myForm = document.createElement("form");
  myForm.method="post" ;
  myForm.action = to ;
  for (var k in p) {
    var myInput = document.createElement("input") ;
    myInput.setAttribute("name", k) ;
    myInput.setAttribute("value", p[k]);
    myForm.appendChild(myInput) ;
  }
  document.body.appendChild(myForm) ;
  myForm.submit() ;
  document.body.removeChild(myForm) ;
}

function ajax_request(file_path, send_opt, callback, args, context)
{
    //opt args
    send_opt = typeof send_opt !== 'undefined' ? send_opt : '';
    args = typeof args !== 'undefined' ? args : []; 
    context = typeof context !== 'undefined' ? context : null;

    var xhr = getXMLHttpRequest();
    xhr.onreadystatechange = function() {
	if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
	    if (typeof callback !== 'undefined')
	    {
		if (context == 'xhr')
		    context = xhr;
		callback.apply(context, args);
	    }
	    // printi(xhr.responseText);
	    // alert(xhr.responseText);
	}
    };
    xhr.open("POST", file_path, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(send_opt);
    
}

var stringToFunction = function(str) {
    var arr = str.split(".");
    
    var fn = (window || this);
    for (var i = 0, len = arr.length; i < len; i++) {
	fn = fn[arr[i]];
    }
    
    if (typeof fn !== "function") {
	throw new Error("function not found");
    }
    
    return  fn;
};

var Options = function (html, root) {
    this.html = html;
    this.root = root;

}
