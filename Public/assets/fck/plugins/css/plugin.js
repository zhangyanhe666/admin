/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    var dialogCommand= { 
        exec:function(editor){ 
            var link1   =   CKEDITOR.document.createElement('link');
            var link2   =   CKEDITOR.document.createElement('link');
            var link3   =   CKEDITOR.document.createElement('link');
            link1.setAttribute('rel','stylesheet');
            link1.setAttribute('type','text/css');
            link1.setAttribute('href','http://static1.wukongtv.com/special/zhuantijson/css/slick.css');
            link2.setAttribute('rel','stylesheet');
            link2.setAttribute('type','text/css');
            link2.setAttribute('href','http://static1.wukongtv.com/special/zhuantijson/css/slick-theme.css');
            link3.setAttribute('rel','stylesheet');
            link3.setAttribute('type','text/css');
            link3.setAttribute('href','http://static1.wukongtv.com/special/zhuantijson/css/video_detail_history.css');
            editor.insertElement(link1);
            editor.insertElement(link2);
            editor.insertElement(link3);

        }
    };
    CKEDITOR.plugins.add('css', {
    init: function(a){
        var b = a.addCommand('css', dialogCommand);
        a.ui.addButton('css', {
            label: 'css',
            command: 'css',
            icon: this.path + 'images/icon.png'
        });
    }
});
})();
