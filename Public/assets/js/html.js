(function(window){
    var html      =   {};
    html.addTool    =   function(obj,uri,name){
        var a   =   $("<a>");
        a.html(name);
        a.attr('class',"btn btn-primary btn-sm");
        a.attr('href',uri);
        $(obj).append(a);
    };
    html.addOption  =   function(obj,uri,name,k='',target=''){
        $(obj).each(function(){
            var param   =   [];
            var href    =   uri;
            var a       =   $("<a>").html(name).attr('class',"btn btn-sm");
            // click
            if(href.indexOf('(') != -1){
                a.attr('onclick',href);
            }else{
                if(k!=''){
                    for(v of k){
                        param.push(v+'='+$(this).data(v));
                    }
                    href    =   href+'?'+param.join('&');
                }
                a.attr('href',href);
                if(k!=''){
                    a.attr('target',target);
                }
            }
            $(this).append(a);
        })
    }

    window.html    =   html;
}(window));

