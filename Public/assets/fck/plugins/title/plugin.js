/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    CKEDITOR.plugins.add('title', {
    init: function(a){
        a.addCommand('title', new CKEDITOR.dialogCommand('title'));
        a.ui.addButton('title', {
            label: '播放按钮',
            command: 'title',
            icon: this.path + 'images/icon.jpg'
        });
        CKEDITOR.dialog.add('title', function (editor) {
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
                        url:'/Specialnew/getMovice?movicename='+movicename,
                        success:function(data){
                            var ret     =   eval("("+data+")");
                            var action  =   ret.status  ==   'Y' ? 'onclick="showMovice(\''+ret.data.wx+'\',\''+ret.data.desc+'\')"'  :   '';
                            var titlep  =   '<div class="title">'+title+'</div>'; //  标题
                            var movicep =   '<div  id="movice" class="top-info" '+action+' ><span class="line">he</span>&nbsp;&nbsp;'+movicename+'<span class="time">'+datetime+'</span>&nbsp;&nbsp;<span class="line">he</span></div><p></p>'; //  影评人
                            //var line    =   '<div style="height: 1px;margin-top:6px;margin-bottom:21px;background-color: #e1e1e1;width: 80px;position: relative;left: 50%;margin-left: -40px;"></div><p>&nbsp;</p>'; //  横线
                            editor.insertHtml(titlep+movicep);
                        },
                    });

                }
            };
        });
    }
});
})();

