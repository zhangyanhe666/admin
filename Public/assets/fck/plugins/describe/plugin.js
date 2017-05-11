
/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    CKEDITOR.plugins.add('describe', {
    init: function(a){
        a.addCommand('describe', new CKEDITOR.dialogCommand('describe'));
        a.ui.addButton('describe', {
            label: '简介匡',
            command: 'describe',
            icon: this.path + 'images/icon.jpg'
        });
        CKEDITOR.dialog.add('describe', function (editor) {
            return {
                title:'简介内容',
                minWidth: 300,
                minHeight: 50,
                contents:[{
                        id:'describeContent',
                        label:'describeContent',
                        title:'简介内容',
                        elements:[{
                                id:'pm',
                                label:'片名',
                                title:'片名',
                                type:'text',
                        },{
                                id:'dy',
                                label:'导演',
                                title:'导演',
                                type:'text',
                        },{
                                id:'zy',
                                label:'主演',
                                title:'主演',
                                type:'text',
                        },{
                                id:'lx',
                                label:'类型',
                                title:'类型',
                                type:'text',
                        },{
                                id:'yy',
                                label:'语言',
                                title:'语言',
                                type:'text',
                        },{
                                id:'dbpf',
                                label:'豆瓣评分',
                                title:'豆瓣评分',
                                type:'text',
                        },]
                }],
                onOk:function(){
                    var format          =   function(name,value){
                        if(value != ''){
                            return '<table cellspacing="5" style="border-collapse:separate; border-spacing:3px;"><tr>\
                                <td style="white-space: nowrap;color: #999999;padding-right:4px;">'+name+'</td>\
                                <td style="color: #333333;">'+value+'</td>\
                            </tr></table>';
                        }else{
                            return '';
                        }
                    }
                    var pm              =   this.getValueOf('describeContent', 'pm');
                    var dy              =   this.getValueOf('describeContent', 'dy');
                    var zy              =   this.getValueOf('describeContent', 'zy');
                    var lx              =   this.getValueOf('describeContent', 'lx');
                    var yy              =   this.getValueOf('describeContent', 'yy');
                    var dbpf            =   this.getValueOf('describeContent', 'dbpf');
                    var table            =   format('片名',pm)+format('导演',dy)+format('主演',zy)+format('类型',lx)+format('语言',yy)+format('豆瓣评分',dbpf);
                    var html             =   '<div style="padding: 18px;;margin-top:31px; margin-left:12px;margin-bottom:21px;margin-right:12px;background: url(\'http://static1.wukongtv.com/special/zhuanti214/img/background_line.png\');background-size: 100% 100%;">'+table+'</div>';
                    
                    editor.insertHtml(html);

                }
            };
        });
    }
});
})();

