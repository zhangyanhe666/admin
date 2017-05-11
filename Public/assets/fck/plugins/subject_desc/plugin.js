/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    CKEDITOR.plugins.add('subject_desc', {
    init: function(a){
        a.addCommand('subject_desc', new CKEDITOR.dialogCommand('subject_desc'));
        a.ui.addButton('subject_desc', {
            label: '段落',
            command: 'subject_desc',
            icon: this.path + 'images/icon.png'
        });
        CKEDITOR.dialog.add('subject_desc', function (editor) {
            return {
                title:'段落',
                minWidth: 300,
                minHeight: 50,
                contents:[{
                        id:'imgList',
                        label:'imgList',
                        title:'段落',
                        elements:[{
                                id:'imgurl',
                                label:'段落',
                                title:'段落',
                                type:'text',
                        }]
                }],
                onOk:function(){
                    var url     =   this.getValueOf('imgList', 'imgurl');
                    var img     =   url == '' ? '' : '<p class="subject-desc">'+url+'</p><p></p>';                    
                    editor.insertHtml(img);
                }
            };
        });
    }
});
})();
