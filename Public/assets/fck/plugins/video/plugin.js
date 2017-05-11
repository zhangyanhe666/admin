/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
   CKEDITOR.plugins.add('video', {
    init: function(a){
        a.addCommand('video', new CKEDITOR.dialogCommand('video'));
        a.ui.addButton('video', {
            label: '影视',
            command: 'video',
            icon: this.path + 'images/icon.png'
        });
       CKEDITOR.dialog.add('video', function (editor) {
              return {
                title:'填写影视信息',
                minWidth: 700,
                minHeight: 400,
                contents:[{
                        id:'movice',
                        label:'movice',
                        title:'填写影视信息',
                        elements:[{
                                id:'title',
                                label:'标题',
                                title:'标题',
                                type:'text',
                        },{
                                id:'imgurl',
                                label:'图片地址',
                                title:'图片地址',
                                type:'text',
                        },{
                                id:'desc',
                                label:'描述',
                                title:'描述',
                                type:'textarea',
                                rows:'20',
                                cols:'40',
                        }]
                }],
                onOk:function(){
                    var title       =   this.getValueOf('movice', 'title');
                    var imgurl      =   this.getValueOf('movice', 'imgurl');
                    var desc        =   this.getValueOf('movice', 'desc');
                    var descArr     =   desc.split("\n");
                    var descp       =   '';
                    if(desc != ''){
                        for(i in descArr){
                            descp   +=  '<p class="video-desc" >'+descArr[i]+'</p>';
                        }
                    }
                    if(title != ''){
                        title   =   '<div class="left-line"></div><p class="subject-name">'+title+'</p>';
                    }
                    if(imgurl != ''){
                        imgurl  =   '<img class="video-img" src="'+imgurl+'">'
                    }
                    
                    var html        =   '';
                    if(imgurl != '' || descp !=''){
                        html        =   '<div id="video">\
                            <div class="video-div">'+imgurl+descp+'</div></div><p></p>';
                    }else{
                        html        =   '<p></p>';
                    }
            
                    editor.insertHtml(title+html);
                }
            };
        });
    }
});
})();

