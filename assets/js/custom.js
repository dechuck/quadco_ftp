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


$(function () {
   $("#demo").jstree({
        "core" : { "initially_open" : [ "root" ] },
       "ui" : {
	   'select_limit' : 1,
       },
        "html_data" : {
	    //      "data" :    "<li rel='drive'><a>Test1</a></li><li rel='folder'><a>Test2</a><ul><li><a>Test2</a></li><li><a>Test2</a><ul><li><a>Test3</a></li></ul></li><li>TEST</li></ul>"
	    "data" : HTML
        },
        "plugins" : [ "themes", "html_data", "types", "ui" ],
	"types" : {
            // I set both options to -2, as I do not need depth and children count checking
            // Those two checks may slow jstree a lot, so use only when needed
            "max_children" : -2,
            "max_depth" : -2,
            // I want only `drive` nodes to be root nodes
            // This will prevent moving or creating any other type as a root node
            "valid_children" : [ "drive" ],
            "types" : {
                // The default type
                "default" : {
		    
                    // I want this type to have no children (so only leaf nodes)
                    // In my case - those are files
                    "valid_children" : "none",
                    // If we specify an icon for the default type it WILL OVERRIDE the theme icons
                    "icon" : {
                        "image" : ROOT + "assets/img/file.png"
                    }
                },
                // The `folder` type
                "folder" : {
                    // can have files and other folders inside of it, but NOT `drive` nodes
                    "valid_children" : [ "default", "folder" ],
                    "icon" : {
                        "image" : ROOT + "assets/img/folder.png"
                    }
                },
                // The `drive` nodes
                "drive" : {
                    // can have files and folders inside, but NOT other `drive` nodes
                    "valid_children" : [ "default", "folder" ],
                    "icon" : {
                        "image" : ROOT + "assets/img/root.png"
                    },
                    // those prevent the functions with the same name to be used on `drive` nodes
                    // internally the `before` event is used
                    "start_drag" : false,
                    "move_node" : false,
                    "delete_node" : false,
                    "remove" : false
                }
            }
        },
	
	
    });
    
    $(document).on('click', '.view_image_action', function(event){
	var centerW = $('#visual_div').width() / 2;
	var centerH = $('#visual_div').height() / 2;

	$('#visual_div').html('<div id="img_div"><img id="visual_image" src="' 
			      + $(this).attr('meta-path') + '" /></div>');
	var img = new Image();
	var $img = $('#visual_image');

	img.onload = function() {
	    // alert(this.width + 'x' + this.height);
	    var imgLeftPad = centerW - (this.width / 2);
	    var imgTopPad = centerH - (this.height / 2); 
	    $('#img_div').attr('style', 'padding-left:' + imgLeftPad + 'px; padding-top:' + imgTopPad + 'px;');
	   // $('#img_div').attr('style', 'position:absolute; left:' + imgLeftPad + '; top:' + imgTopPad + ';');
	}
	img.src = $(this).attr('meta-path');
    });

    $(document).on('click', '.view_textfile_action', function(event) {
	ajax_request(ROOT + 'modules/ajax/read_file.php',
		     'file_to_read=' + $(this).attr('meta-path'), 
		     function () {
			 $('#visual_div').html('<pre class="prettyprint"><xmp>' + this.responseText 
					       + '</xmp></pre>');
		     },
		     [], 'xhr');
	
    });
    
    // $(document).on('click', '.download_action', function(event) {
    // 	var string = "";
    // 	$('#demo').jstree('get_selected').each(function (i) {
    // 	    // ajax_request(ROOT + 'modules/ajax/download_file.php',
    // 	    // 	     'file_to_download=' + $(this).attr('meta-path'), 
    // 	    // 	     function () {
    // 	    // 		 alert(this.responseText);
    // 	    // 	     },
    // 	    // 	     [], 'xhr');
    // 	    // postwith('modules/ajax/download_file.php', {file_to_download: '"' + $(this).attr('meta-path') + '"'});
    // 	    // alert($(this).attr('meta-name'));
    // 	    string += ((string.length > 0) ? '&' : '') + $(this).attr('meta-path');
    // 	});
    // 	alert(string);
    // 	// $('#dowload_file_id').val(string);
    // 	postwith('modules/ajax/download_file.php', {file_to_download: '"' + string + '"'});
    // });

    $(document).on('click', '.download_action', function(event) {
	var path = $('#demo').jstree('get_selected').attr('meta-path');
	var name = $('#demo').jstree('get_selected').attr('meta-name');
    	postwith('modules/ajax/download_file.php', {file_name: name, file_to_download: path});
    });
});
