(function(window){
    var admin      =   {};
    admin.list =   {};

    admin.menuOpen  =   function(parentid){
        $('#menu-'+parentid).addClass('open');
        $('#menu-'+parentid).children('ul').addClass('nav-show');
        $('#menu-'+parentid).children('ul').show();
        $('#menu-'+parentid).parents('.submenu').show();
        $('#menu-'+parentid).parents('.submenu').addClass('nav-show');
    };
    admin.get       =   function(k){
        var reg = new RegExp("(^|&)"+ encodeURIComponent(k) +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }
    admin.down      =   function(){
        var tag     =   window.location.search.indexOf("?") > -1 ? '&' :  '?';  
        var origin  =   window.location.origin;
        var pathname=   window.location.pathname.replace(/(\/$)/,'').replace(/(\/index$)/,'');
        var search  =   window.location.search;
        window.location.href    =   origin+pathname+'/down'+search+tag+'downcode='+$('#downcode').val();
    };

    admin.checkInput    =   function (name,value,text){
        check  =   admin.get(name) == value ? 'checked="checked"' : '';
        $('#checkInput').append('<input type="checkbox" name="'+name+'"  '+check+' value=\"'+value+'"/>'+text);
    }
    admin.createOption  =   function(optionList,defaultVal){
        var optionLabel   = '';
        for(o in optionList){
            selected    =   o == defaultVal  ? 'selected' : "";
            optionLabel     +=  '<option value="'+o+'" '+selected+'>'+optionList[o]+'</option>';
        }
        return optionLabel;
    }
    admin.selectInput   =   function(option,fieldName,columnVal){
        var delButton     = '<span class="btn " style="margin-top:0px;margin-left: -30px;padding:1px 6px; " onclick="$(this).parent().remove()">-</span>';
        for(i in fieldName){
            var optionList      = admin.createOption(option,fieldName[i]);
            var delButton     = i == 0 ? '' : '<span class="btn " style="margin-top:0px;margin-left: -30px;padding:1px 6px; " onclick="$(this).parent().remove()">-</span>';
            var selectInput   = '<div class="selectDiv" style="width:auto;float:left;"><select id="sel" style="width:auto;float:left;" name="fieldName[]">'+optionList+'</select><input style="width:auto;" type="text" class="form-control" id="" value="'+columnVal[i]+'" name="fieldVal[]" placeholder="">'+delButton+'</div>'
            $('#selectInput').append(selectInput);
        }
    }
    admin.upload    =   function(){
        $('#_updateExcel').change(function(){
                if($(this).val()==''){
                    return false;
                }        
                $("#_updateForm").ajaxSubmit({ 
                    dataType:  'json', //数据格式为json 
                    success: function(data) {     
                        $('#over').hide();
                        $('.file-progress').hide();
                        if(data.status=='Y'){
                            alert('数据上传成功')
                            window.location.reload();
                        }else{
                            alert(data.msg);
                        }
                        
                    }
                });
                $(this).val('');
        });
    };
    admin.addSInput =   function(){
        var str     =   '<span class="btn " style="margin-top:0px;margin-left: -30px;padding:1px 6px; " onclick="$(this).parent().remove()">-</span>';
        var formgroup   =   $('.form-group').eq(0);
        var select  =   formgroup.children().eq(0).clone();
        var width   =   select.children('div').css('width');
        select.children('div').remove();
        formgroup.append(select.append(str));
        $('input[name="is_like"]').prop('checked',true);
        $('select[name="fieldName[]"]').chosen({width:width});
    }
    //删除
    admin.deleteItem    =   function (id){
        if(!confirm('你确认删除该项吗')){
            return false;
        }

        $.ajax({
            url:admin.uri+'delete', 
            data:'id='+id,
            type:"GET",
            success:function(data){
                res =   eval('('+data+')');
                if(res.status == 'Y'){
                    alert('删除成功');
                    window.location.reload();
                }else{
                    alert(res.msg);
                }
            },
        })
    }
    admin.listOptions   =   function(opt){
        var options   =   {"edit":"编辑","copy":"复制","delete":"删除","transfer":"迁移到线上"};

        $('table .tr-item').each(function(){
          for(a in opt){
              var attr  = {
                class:"btn  btn-sm",
                onclick:"admin.operationItem('"+opt[a]+"',"+$(this).data('id')+")",
              };
              var button  = $('<a>'+options[opt[a]]+'</a>').attr(attr);
              $(this).children(':last').append(button);
          }

        });
    }

    admin.operationItem     =   function(type,id){
          var uri   = admin.uri+type+'?id='+id;
          switch(type){
            case 'edit':
            case 'copy':
              location.href = uri;
              break;
            case 'transfer':
              admin.execurl(uri,0);
              break;
            case 'delete':
              admin.deleteItem(id);
              break;
          }
    
    }

    admin.tool      =   function(toolList){
        var tools   = {"down":"<select id=\"downcode\">\
                                  <option value=\"utf8\">utf8</option>\
                                  <option value=\"gbk\">gbk</option>\
                              </select><a class=\"btn btn-primary btn-sm\" onclick=\"admin.down()\">csv下载</a>",
                      "upload":"<form id=\"_updateForm\" style='display: none;'  action=\"uploadExcel\" method=\"post\" enctype=\"multipart/form-data\">\
                                    <input type=\"file\" name=\"excel\" id=\"_updateExcel\" style=\"display:none;\">\
                                </form>\
                                <a class=\"btn btn-primary btn-sm\" onclick=\"$('#_updateExcel').click()\">数据上传</a>",
                      "add":"<a class=\"btn btn-primary btn-sm\" href=\""+admin.uri+"add?menu_id="+admin.get('menu_id')+"\">添加</a>",
                      "tableconfig":"<a class=\"btn btn-primary btn-sm\" href=\""+admin.uri+"add?menu_id="+admin.get('menu_id')+"\" target=\"_blank\">表结构快速通道</a>",
                      "custom":"<a class=\"btn btn-primary btn-sm\" href=\""+admin.uri+"index?menu_id="+admin.get('menu_id')+"&custom=on\">自定义显示</a>",
                      "index":"<a class=\"btn btn-primary btn-sm\" href=\""+admin.uri+"index?menu_id="+admin.get('menu_id')+"\">完成自定义</a>"};
        for(tool in toolList){
            $('#tools').append(tools[toolList[tool]]+'<br/>');
        }
    }

    //default显示
    admin.edit  =   function (obj,id){
        $(".doEditInput").click();
        var objTd   =   $(obj).parent();
        var val     =   $.trim($(obj).html());
        var field   =   $(obj).attr('field');
        var str     =   '<input type="text"  value="'+admin.htmlspecialchars(val)+'"/><input class="doEditInput" onclick="admin.doEdit(this,'+id+')" type="button" value="√"/><input type="button" class="editInput" onclick="admin.revoke(this,'+id+')" value="×"/>';
        objTd.data('val',val);
        objTd.data('field',field);
        objTd.html(str);
        $('td >').on('mousedown', function(e){
            e.stopPropagation();
        });
    }
    admin.revoke    =   function (obj,id){
        var objTd   =   $(obj).parent();
        var val     =   objTd.data('val');
        var field   =   objTd.data('field');
        var str     =   '<span onclick="admin.edit(this,'+id+')"  style="height:50px;display:block;" field="'+field+'">'+val+'</span>';
        objTd.html(str);
                $('td >').on('mousedown', function(e){
            e.stopPropagation();
        });
    }
    admin.doEdit    =   function (obj,id){
        var val     =   $(obj).prev().val();
        var field   =   $(obj).parent().data('field');
        $.ajax({
            url:admin.uri+'doEditColumn',
            data:"id="+id+"&"+field+"="+encodeURIComponent(val),
            type:'post',
            dataType:'text',
            success:function(res){
                admin.hideLoading();
                res    =   eval("("+res+")");
                if(res.status=='Y'){
                    $(obj).parent().data('val',val);
                    admin.revoke(obj,id);
                }else{
                    if(res.msg){
                        alert(res.msg);
                    }else{
                        alert('更新失败');
                    }

                }
            },
            beforeSend:function(){
                admin.loading();
            }
        });

    } 
    admin.judge     =   function (obj,id){
        var param   =   eval("("+$(obj).attr('param')+")");
        var val     =   $(obj).attr('val') == param.on ? param.off : param.on;
        var field   =   $(obj).attr('field');
        $.ajax({
            url:admin.uri+'doEditColumn',
            data:"id="+id+"&"+field+"="+val,
            type:'post',
            dataType:'text',
            success:function(res){
                admin.hideLoading();
                res    =   eval("("+res+")");
                if(res.status!='Y'){                 
                    alert('更新失败');
                }else{
                    $(obj).attr('val',val)
                }
            },
            beforeSend:function(){
                admin.loading();
            }
        });

    }  
    admin.columnSwitch  =   function (t){
        var column  =   $(t).attr('column');
        var val     =   $(t).attr('val') == 1 ? 0 : 1;
        $.ajax({
            url:admin.uri+'columnSwitch',
            data:"column="+column+"&val="+val,
            type:'GET',
            success:function(res){
                admin.hideLoading();
                $(t).attr('val',val);
            },
            beforeSend:function(){
                admin.loading();
            }
        });
    }
    admin.delchecked    =   function (){
        var deleteCheckbox  =   $('.deleteCheckbox:checked');
        var id = '0&';
        if(deleteCheckbox.length == 0){
            alert('没有选中项');
            return false;
        }
        deleteCheckbox.each(function(){
            id +=   $(this).attr('name')+'='+$(this).val()+'&';        
        });
        this.deleteItem(id);
    }
    
    //show
    admin.showText  =   function(t){
        var x_max = $(window).width();
        var y_max = $(window).height();
        var div_width = $("#login").width() + 20;//20是边框
        var div_height = $("#login").height() + 20;
        var _x_max = x_max - div_width;//最大水平位置
        var _y_max = y_max - div_height;//最大垂直位置
        var scom    =   $(t).next().html();
        $('#showcom').html('');
        $('#showcom').html(scom);
        var wtop   =   document.body.scrollTop;
        var x = (x_max - div_width) / 2;//水平居中
        var y = wtop + (y_max - div_height) / 2;//垂直居中
        $("#login").css({"left": x + 'px',"top": y + 'px'});//设置初始位置,防止移动后关闭再打开位置在关闭时的位置
        $("#login").css("display","block");
        $("#over").css("display","block");
        $('#showcom').focus();
        $('#showcom').select();
        return false;
    }
    //hide
    admin.hideText  =   function(){
        $('#showcom').html('');
        $("#login").css("display","none");
        $("#over").css("display","none");
    }
    
    admin.textMousedown =   function(title){//title代表鼠标按下事件
        var x_max = $(window).width();
        var y_max = $(window).height();
        var div_width = $("#login").width() + 20;//20是边框
        var div_height = $("#login").height() + 20;
        var _x_max = x_max - div_width;//最大水平位置
        var _y_max = y_max - div_height;//最大垂直位置

        var point_x = title.pageX;//鼠标横坐标,有资料说pageX和pageY是FF独有,不过经过测试chrome和IE8是可以支持的,其余的浏览器没有装,没测
        var point_y = title.pageY;//鼠标纵坐标
        var title_x = $(this).offset().left;//标题横坐标
        var title_y = $(this).offset().top;//标题纵坐标
        $(document).bind("mousemove",function(move){
            $(this).css("cursor","move");
            var _point_x = move.pageX;//鼠标移动后的横坐标
            var _point_y = move.pageY;//鼠标移动后的纵坐标
            var _x = _point_x - point_x;//移动的水平距离
            var _y = _point_y - point_y;//移动的纵向距离
            // console.debug('水平位移: ' + _x + '垂直位移: ' + _y);
            var __x = _x + title_x;//窗口移动后的位置
            var __y = _y + title_y;//窗口移动后的位置
            __x > _x_max ? __x = _x_max : __x = __x;//水平位置最大为651像素
            __y > _y_max ?__y = _y_max : __y = __y;//垂直位置最大为300像素
            __x < 0 ? __x = 0 : __x = __x;//水平位置最小为0像素
            __y < 0 ?__y = 0 : __y = __y;//垂直位置最小为0像素
            // console.debug('标题X:' + title_x + '标题Y:' + title_y);
            $("#login").css({"left":__x,"top":__y});
        });//绑定鼠标移动事件,这里绑定的是标题,但是如果移动到区域外的话会导致事件不触发
        $(document).mouseup(function(){
        $(this).unbind("mousemove");//鼠标抬起,释放绑定,防止松开鼠标后,指针移动窗口跟着移动
        });
    };
    admin.doAdd   =   function(){
        $.ajax({
            cache:true,
            type:"POST",
            url :admin.addUrl,
            data:$("#album_form").serialize()+'&'+$('#attach_form').serialize(),
            async:false,
            error:function(request){
                alert('添加失败');
            },
            success:function(data){
                var data    =   eval("("+data+")");
               if(data.status == 'Y'){
                   location.href    =   $('input[name="referer"]').val();
               }else{
                   alert(data.msg);
               }
            }
        })
    }
    //后台登录
    admin.login =   function(){
            var user=$(".loginno").val();
            var pass=$(".loginpass").val();
            var role=$(".rolename").val();
            if(user == ""){
                    alert("请输入账号");
                    return;
            }
            if(pass == ""){
                    alert("请输入密码");
                    return;
            }
            $.ajax({
                    url:admin.loginCheckUrl,
                    data:$("#userform").serialize(),
                    type:'post',
                    dataType:'text',
                    success:function(res){
                        var data    =   eval('('+res+')');
                        if(data.status == 'Y'){
                            location.href    =   $('input[name="referer"]').val();
                        }else{
                            alert(data.msg);
                        }
                     }
            });
    }
    //搜索字段类型js
    admin.albuminfo =   function(tt,fieldName){
        $('input[name="'+fieldName+'"]').val($(tt).attr('prompt'));
        $('input[name="'+fieldName+'"]').next().val($(tt).text());
    }
    admin.clareWrapper =   function(){
        $('.wrapper').html("");
        $('.wrapper').hide();
    }
    admin.selectPrompt  =   function(t){
         var selectPrompt    =   $(t).val();
        var albumLeft       =   $(t).offset().left;
        var albumHeight     =   $(t).outerHeight();
        var albumTop        =   $(t).offset().top+albumHeight;
        var fieldName       =   $(t).prev().attr('name');
        if($(".wrapper").length == 0){
            var wrapper         =   '<div class="wrapper" style="display: none;"></div>';
            $('.main-content').append(wrapper);
        }
        $(".wrapper").css({
            'left':albumLeft,
            'top':albumTop
        });
        $.ajax({
            url:admin.searchUrl,
            data:'fieldName='+fieldName+'&selectPrompt='+selectPrompt,
            type:'GET',
            error:function(){
                $('.wrapper').html("<div class='option'>没有查询到数据</div>");
            },
            success:function(res){
                var res =   eval("("+res+")");
                var str  =  '';
                $('.wrapper').show();
                if(res.status == "Y" && !jQuery.isEmptyObject(res.data)){
                    for(var i in res.data){
                        str +=  "<div class='option' onclick='admin.albuminfo(this,\""+fieldName+"\")' prompt='"+i+"'>"+res.data[i]+"</div>";
                    }
                    $('.wrapper').html(str);
                }else{
                    $('.wrapper').html("<div class='option'>没有查询到数据</div>");
                }
            }
        });
        
    }
        admin.loading   =   function(){
            if($('#over').length == 0){
                var loading =   '<div id="over"></div><div id="loading"></div>'
                $('.main-content').prepend(loading);
            }
            $('#over').show();
            $('#loading').show();
        }
        admin.hideLoading   =   function(){
            $('#over').hide();
            $('#loading').hide();
        }
        admin.execurl   =   function(url,tag){
                $.ajax({
                    url:url,
                    type:'GET',
                    success:function(ret){
                        admin.hideLoading();
                        res =   eval("("+ret+")"); 
                        if(res.status=='Y'){
                            if(tag==0){
                                var msg     =   res.msg!='' ? res.msg : '成功';
                                alert(msg);
                            }else{
                                var msg     =   res.msg!='' ? res.msg : '成功';
                                alert(msg);
                                location.href   =   location.href;
                            }
                        }else{
                            alert(res.msg);
                        }

                    },
                    beforeSend:function(){
                        admin.loading();
                    }
                });
        }
        admin.htmlspecialchars  =   function(str){
            str =   str.replace(/"/g,'&quot;');
            str =   str.replace(/'/g,'&#039;');
            return str
        }
        admin.htmlspecialchars_decode   =   function(str){
            str = str.replace(/&quot;/g, "\"");  
            str = str.replace(/&#039;/g, "'");  
            return str; 
        }
        admin.addslashes    =   function(str){
            str     =   str.replace(/\\/g,'\\\\');
            str     =   str.replace(/'/g,'\\\'');
            str     =   str.replace(/"/g,'\\\"');
            return str;
        }
        admin.checkedboxAll =   function(t){
            var inputs  =   $(t).parent().next().children('div').children('input');
            inputs.length == 0 && (inputs    =   $(t).parent().next().children('label').children('input'));
            inputs.each(function(){
                if($(this).prop("checked") != $(t).prop("checked")){
                    $(this).click();
                }
            });
        }
        admin.itemToggle    =   function(t){
            $(t).toggleClass('down');
            $(t).parent().next().toggle();
        }
        admin.checkboxList_show =   function(){
            var html    =   $('.showSwitch').html();
            if(html == '展开'){
                $('.showSwitch').html('收起');
                $('.arrow-list').addClass('down');
                $('.arrow-list').parent().next().show();
            }else{        
                $('.showSwitch').html('展开');
                $('.arrow-list').removeClass('down');
                $('.arrow-list').parent().next().hide();
            }
        }
        admin.checkedList   =   function(checkedList){
            $('.container').append($('#attach_form'));
            $('#attach_form').append($('#submit'));
            $('#checkboxList').next().show();
            $('input[name="__check[]"]').each(function(){
                if($.inArray($(this).val(),checkedList)>-1){
                    $(this).click();
                }
            });
        }
        //填充当前包名的数据
        admin.fillBaoming   =   function(){
            $('#baoming').css('float','left');
            $('#baoming').css('width','70%');
            $('#baoming').css('margin-right','10px');
            var label =   '<select id="source"><option value="wukong.app">当贝市场</option><option  value="wukong.app_shafa">沙发市场</option><select>';
            $('#baoming').before('<br/>');
            $('#baoming').after(label+'<input type="button" value="同步" onclick="admin.synchronous()">');
        }
        
        admin.hotNewRepate      =   function(){
             $(this).blur(function(){
                var fieldName   =   $(this).attr('name');
                var val         =   $(this).val();
                if(val!=''){
                    admin.checkRepate(fieldName,val);
                }
            })
        }
        admin.synchronous   =   function(url){
                var source  =   $('#source').val();
                var baoming =   $('#baoming').val();
                if(baoming==''){
                    alert('包名不能为空');
                    return false;
                }
                $.ajax({
                    url:admin.synchronousUrl+'?source='+source+'&baoming='+baoming,
                    success:function(res){
                        var res =   eval("("+res+")");
                        if(res.status=='Y'){
                            $('input[type="text"]').val('');
                            $('textarea').val('');
                            for(var k in res.data){
                                $('input[name="'+k+'"]').val(res.data[k]);
                                if(jQuery.inArray(k, ['summary','piclist'])>-1){
                                    $('textarea[name="'+k+'"]').val(res.data[k]);
                                }
                                if(k == 'screenshots'){
                                    $('textarea[name="piclist"]').val(res.data[k]);
                                }
                            }
                        }else{
                            alert('未查到数据');
                        }
                    }
                });
        }
        admin.checkRepate   =   function(fieldName,val){
                $.ajax({
                    url:admin.checkRepateUrl + '?fieldName='+fieldName+'&val='+val,
                    success:function(res){
                        var res =   eval("("+res+")");
                        if(res.status=='Y' && confirm('该排序已经存在，是否替换已有排序')){
                            $.ajax({
                                url:admin.updateSort+'?fieldName='+fieldName+'&val='+val,
                                success:function(ress){ 
                                    var ress =   eval("("+ress+")");
                                    if(ress.status == 'Y'){
                                        alert('更新排序成功');
                                    }else{
                                        alert('更新排序失败');
                                    }
                                }
                            });
                        }
                    }
                });
        }
        admin.signLine  =   function(){
            $('tr').click(function(){
                $('.signLine').removeClass();
                $(this).addClass('signLine');
            });
        }
        admin.previewSmall  =   function(img,selection){
            var thumb       =   $(img).next();
            var thumbWidth  =   parseInt(thumb.css('width'));
            var thumbHeight =   parseInt(thumb.css('height'));
            var scaleX = thumbWidth / selection.width; 
            var scaleY = thumbHeight / selection.height; 
            $(img).next().children('img').css({ 
                    width: Math.round(scaleX * parseInt($(img).css('width'))) + 'px', 
                    height: Math.round(scaleY * parseInt($(img).css('height'))) + 'px',
                    marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
                    marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
            });
            $('#_imgX1').val(selection.x1);
            $('#_imgY1').val(selection.y1);
            $('#_imgX2').val(selection.x2);
            $('#_imgY2').val(selection.y2);
            $('#_imgW').val(selection.width);
            $('#_imgH').val(selection.height);
            $('#_thumbW').val(thumbWidth);
            $('#_thumbH').val(thumbHeight);
        }
        admin.previewImage      =   function(file){
            var img         =   admin.currentImgColumn.children('img')[0];
            var thumb       =   $(img).next();
            var maxHeight   =   parseInt(thumb.css('height'));
            var maxWidth    =   parseInt(thumb.css('width'));
            if (file.files && file.files[0])
            {
                var reader = new FileReader();
                reader.onload = function(evt){
                    img.src = evt.target.result;
                    thumb.children('img')[0].src = evt.target.result;
                    thumb.show();
                }
                reader.readAsDataURL(file.files[0]);
                var aspect =   maxHeight/maxWidth;
                $(img).imgAreaSelect({ aspectRatio: '1:' + aspect,onSelectChange: admin.previewSmall ,handles: true, instance: true });
            }
        }
        admin.selectImg =   function(t){
            admin.currentImgColumn  =   $(t).parent();
            $('#_imgFile').click();
        }
        admin.submitImge    =   function(t){
            $(t).parent().children('img').first().next().hide();
            if($(t).parent().children('img')[0].src.indexOf('http') >= 0){
                $(t).parent().children('img')[0].src    =   $(t).parent().children('input').first().val();
                return ;
            }
            $("#imgform").ajaxSubmit({ 
                beforeSend:function(){
                    $('#over').show();
                },
                dataType:  'json', //数据格式为json 
                success: function(ret) {     
                    $('#over').hide();
                    if(ret.status=='Y'){
                        $(t).parent().children('input').first().val(ret.data.url);
                        $(t).parent().children('img')[0].src    =   ret.data.url;
                        $('div[class^="imgareaselect"').hide();
                        $('#_imgX1').val('');
                    }else{
                        alert(ret.msg);
                    }
                }
            });
        }
        admin.nestable  =   function(){
            if(!this.isPc()){
                return;
            }
            $('.overflowx').nestable({
                maxDepth:1,
                rootClass:'overflowx',
                listNodeName :'tbody',
                itemNodeName:'tr',
                listClass:'table',
                itemClass:'tr-item',
                handleClass:'td-handle',
            });
            $('td >').on('mousedown', function(e){
                    e.stopPropagation();
            });

            $('.overflowx').on('change', function(e) {  
                var sort    =   $('.overflowx').nestable('serialize');
                $.ajax({
                    url:admin.uri+'sort',
                    data:{'sort':sort},
                    dataType:'json',
                    type:'POST',
                    success:function(ret){
                        if(ret.status == 'N'){
                            alert(ret.msg);
                        }
                    }
                });
            });  
        }
        //判断pc端
        admin.isPc      =   function(){
            var userAgentInfo = navigator.userAgent;
            var Agents = ["Android", "iPhone",
                        "SymbianOS", "Windows Phone",
                        "iPad", "iPod"];
            var flag = true;
            for (var v = 0; v < Agents.length; v++) {
                if (userAgentInfo.indexOf(Agents[v]) > 0) {
                    flag = false;
                    break;
                }
            }
            return flag;
        }
        window.admin    =   admin;
        admin.weekCheck =   function(){
            var parentChecked   =   $(this).prop("checked");
            $(this).parent().children('div').children('input').each(function(){
                $(this).prop("checked",parentChecked);
            });
        };
        admin.listColumn    =   function(listColumn){
            if(admin.get('custom') != 'on'){
                return ;
            }
            $(listColumn).each(function(){
              var column  = $(this).data('column');
              var val     = $(this).data('val');
              var checked = val == 0 ? 'checked' : '';
              var div     = '  <span onclick="admin.columnSwitch(this)" column="'+column+'" val="'+val+'}">\
              <label>\
                  <input type="checkbox" '+checked+' style="width:0px;" class="ace ace-switch ace-switch-6">\
                  <span class="lbl"></span> \
              </label>\
              </span>';
              $(this).append(div);
            });
        }
        admin.listInit  =   function(){
            $('.table').before('<div style="margin-top:5px;">\
                <input type="checkbox" id="checkall">全选<input type="checkbox" id="checkreverse">反选  \
                <a class="btn btn-primary btn-sm" onclick="javascript:void(0);" \
                    href="javascript:admin.delchecked();">删除</a></div>');
            $('.table tr:eq(0)').prepend('<td></td>');
            $('.table tr:gt(0)').each(function(index){
                $(this).children().each(function(index){
                    var html    =   admin.list.td(this,index,);
                    $(this).html(html);
                })
                $(this).prepend('<td><input type="checkbox" name="id['+index+']" class="deleteCheckbox" value="'+$(this).data('id')+'"></td>');
            });
            $('#checkall').click(function(){
                var parentChecked   =   $(this).prop("checked");
                $('.deleteCheckbox').each(function(){
                    $(this).prop("checked",parentChecked);
                });
            })
            $('#checkreverse').click(function(){
                $('.deleteCheckbox').each(function(){
                    if($(this).prop("checked")){
                        $(this).prop("checked",false);
                    }else {
                        $(this).prop("checked",true);
                    }
                });
            })
            $('select[name="page_num"]').change(function(){
                $(".form-inline").submit();
            });
            $('select[name="fieldName[]"]').chosen();
        }
        admin.list.td   =   function(t,index){
            var val     =   $(t).html();
            if(!!admin.listTdType[index]){
                // switch(admin.listTdType[index]){
                //     case 'id':
                //         console.log('id');
                //     break;
                //     case 'defaultType': 
                //         console.log('default');
                //     break;
                // }

            }
            return val;
        }
}(window));

