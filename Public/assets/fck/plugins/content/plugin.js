/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    var dialogCommand= { 
        exec:function(editor){ 
            editor.insertHtml('<p style="">内容</p><p></p>');
         } 
    };
    CKEDITOR.plugins.add('content', {
    init: function(a){
        var b = a.addCommand('content', dialogCommand);
        a.ui.addButton('content', {
            label: '内容',
            command: 'content',
            icon: this.path + 'images/icon.jpg'
        });
    }
});
})();
