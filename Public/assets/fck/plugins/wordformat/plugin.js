/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
var fs      =   true;
var selection= '';
(function(){
    
    var dialogCommand= { 
        exec:function(editor){           
            selection   =   editor.getSelection().getStartElement().$
        } 
    };
    CKEDITOR.plugins.add('wordformat', {
    init: function(a){
        a.addCommand('wordformat', dialogCommand);
        a.ui.addButton('wordformat', {
            label: '格式刷',
            command: 'wordformat',
            icon: this.path + 'images/icon.jpg'
        });
    }
});
})();