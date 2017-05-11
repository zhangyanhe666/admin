/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
   CKEDITOR.plugins.add('title1', {
    init: function(a){
        a.addCommand('title1', new CKEDITOR.dialogCommand('title1'));
        a.ui.addButton('title1', {
            label: '标题',
            command: 'title1',
            icon: this.path + 'images/icon.jpg'
        });
       CKEDITOR.dialog.add('title1', function (editor) {
              return {
                title:'填写标题信息',
                minWidth: 300,
                minHeight: 50,
                contents:[{
                        id:'movice',
                        label:'movice',
                        title:'填写标题信息',
                        elements:[{
                                id:'title',
                                label:'标题名',
                                title:'标题名',
                                type:'text',
                        },{
                                id:'movicename',
                                label:'影评人名称',
                                title:'影评人名称',
                                type:'text',
                        },{
                                id:'datetime',
                                label:'日期',
                                title:'日期',
                                type:'text',
                        }]
                }],
                onOk:function(){
                    var title       =   this.getValueOf('movice', 'title');
                    var movicename  =   this.getValueOf('movice', 'movicename');
                    var datetime    =   this.getValueOf('movice', 'datetime');
                    $.ajax({
                        url:'getMovice?movicename='+movicename,
                        success:function(data){
                            var ret     =   eval("("+data+")");
                            var action  =   ret.status  ==   'Y' ? 'onclick="showMovice(\''+ret.data.wx+'\',\''+ret.data.desc+'\')"'  :   '';
                            var titlel   =   '<p class="title">'+title+'</p>';
                            var name   =   '<p id="movice" class="top-desc" '+action+'><span class="line">h</span>&nbsp;&nbsp;'+movicename;
                            var time   =   ' <span class="time">'+datetime+'</span>&nbsp;&nbsp;<span class="line">h</span></p><p></p>';
                            editor.insertHtml(titlel+name+time);
                        },
                    });

                }
            };
        });
    }
});
})();

