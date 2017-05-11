/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    var dialogCommand= { 
        exec:function(editor){ 
            editor.insertHtml('<div><button  id="share">&nbsp; &nbsp; 分享 &nbsp; &nbsp;</button></div><p></p>');
        }
    };
    CKEDITOR.plugins.add('share', {
    init: function(a){
        var b = a.addCommand('share', dialogCommand);
        a.ui.addButton('share', {
            label: '分享按钮',
            command: 'share',
            icon: this.path + 'images/icon.jpg'
        });
    }
});
})();
