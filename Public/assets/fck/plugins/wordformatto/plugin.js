/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    
    var dialogCommand= { 
        exec:function(editor){    
            var doc     =   selection.cloneNode();
            parentNode(editor.getSelection().getStartElement().$).style.cssText     =   parentNode(selection).style.cssText;
            doc.innerText = editor.getSelection().getNative().toString();
            tmpp =   parents(selection,doc);
            editor.insertHtml(tmpp.outerHTML);
       } 
    };
    CKEDITOR.plugins.add('wordformatto', {
    init: function(a){
        a.addCommand('wordformatto', dialogCommand);
        a.ui.addButton('wordformatto', {
            label: '格式内容',
            command: 'wordformatto',
            icon: this.path + 'images/icon.jpg'
        });
    }
});
})();
function parentNode(obj){
    if(obj.parentNode.nodeName != 'BODY'){
        obj     =   parentNode(obj.parentNode);
    }
    return obj;
}
function parents(obj,doc){
    if(obj.parentNode.nodeName != 'BODY'){
        var docp     =   obj.parentNode.cloneNode();
        docp.innerHTML  =   doc.outerHTML;
        return parents(obj.parentNode,docp);
    }
    return doc;
}