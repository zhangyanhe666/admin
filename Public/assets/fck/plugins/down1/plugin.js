/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    var dialogCommand= { 
        exec:function(editor){ 
            var down             =   $("#cke_content").find('iframe').contents().find("#down")
            if(down.length == 0){
                            editor.insertHtml('<div id="down">\
    <img src="http://static1.wukongtv.com/special/zhuanti214/img/logo.png" class="down-logo">\
    <div class="btn-text-area">\
        <div class="down-app-name">悟空遥控</div>\
        <div class="down-app-desc">电视必备神器，免费看视频。</div>\
    </div>\
    <img class="down-btn" src="http://static1.wukongtv.com/special/zhuanti214/img/download.png">\
</div><p></p>');
            }else{
                $("#cke_content").find('iframe').contents().find("#down").remove();
            }
         } 
    };
    CKEDITOR.plugins.add('down', {
    init: function(a){
        var b = a.addCommand('down', dialogCommand);
        a.ui.addButton('down', {
            label: '下载',
            command: 'down',
            icon: this.path + 'images/icon.jpg'
        });
    }
});
})();
