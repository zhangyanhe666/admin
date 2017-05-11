/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    CKEDITOR.plugins.add('img', {
    init: function(a){
        a.addCommand('img', new CKEDITOR.dialogCommand('img'));
        a.ui.addButton('img', {
            label: '图片',
            command: 'img',
            icon: this.path + 'images/icon.jpg'
        });
        CKEDITOR.dialog.add('img', function (editor) {
            return {
                title:'添加图片',
                minWidth: 300,
                minHeight: 50,
                contents:[{
                        id:'imgList',
                        label:'imgList',
                        title:'添加图片',
                        elements:[{
                                id:'imgtitle',
                                label:'图片标题',
                                title:'图片标题',
                                type:'text',
                        },{
                                id:'imgurl',
                                label:'图片地址',
                                title:'图片地址',
                                type:'text',
                        },{
                                id:'imgdesc',
                                label:'图片简介',
                                title:'图片简介',
                                type:'text',
                        }]
                }],
                onOk:function(){
                    var title   =   this.getValueOf('imgList', 'imgtitle');
                    var url     =   this.getValueOf('imgList', 'imgurl');
                    var desc    =   this.getValueOf('imgList', 'imgdesc');
                    var titlehtml    =   title == '' ? '' : '    <p class="left-line"></p><p class="subject-name">'+title+'</p>';
                    var urlhtml =   url == '' ? '' : '<div><img  src="'+url+'" /></div>';
                    var deschtml=   desc == '' ? '' : '<p style="text-align: center;color:#999999;padding: 0px;font-size: 90%;display:block;">'+desc+'</p>';
                    editor.insertHtml(titlehtml+urlhtml+deschtml+"<p></p>");
                }
            };
        });
    }
});
})();
