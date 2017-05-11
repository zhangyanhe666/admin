/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    CKEDITOR.plugins.add('firstImg', {
    init: function(a){
        a.addCommand('firstImg', new CKEDITOR.dialogCommand('firstImg'));
        a.ui.addButton('firstImg', {
            label: '首图',
            command: 'firstImg',
            icon: this.path + 'images/icon.png'
        });
        CKEDITOR.dialog.add('firstImg', function (editor) {
            return {
                title:'添加首图',
                minWidth: 300,
                minHeight: 50,
                contents:[{
                        id:'imgList',
                        label:'imgList',
                        title:'添加首图',
                        elements:[{
                                id:'imgurl',
                                label:'图片地址',
                                title:'图片地址',
                                type:'text',
                        }]
                }],
                onOk:function(){
                    var url     =   this.getValueOf('imgList', 'imgurl');
                    var img     =   url == '' ? '' : '<div><img src="'+url+'" class="top-img"/></div><p></p>';                    
                    editor.insertHtml(img);
                }
            };
        });
    }
});
})();
