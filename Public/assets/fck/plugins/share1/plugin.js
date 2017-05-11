/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    var dialogCommand= { 
        exec:function(editor){ 
            editor.insertHtml('<button  id="share">&nbsp; &nbsp; 分享 &nbsp; &nbsp;</button><p></p>');
        }
    };
    CKEDITOR.plugins.add('share1', {
    init: function(a){
        var b = a.addCommand('share1', dialogCommand);
        a.ui.addButton('share1', {
            label: '分享按钮',
            command: 'share1',
            icon: this.path + 'images/icon.jpg'
        });
    }
});
})();
