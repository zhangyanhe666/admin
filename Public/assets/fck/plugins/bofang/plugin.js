/**
 * Title:CKEditor插件示范
 * Author:铁木箱子(http://www.mzone.cc)
 * Date:2010-08-02
 */
(function(){
    CKEDITOR.plugins.add('bofang', {
    init: function(a){
        a.addCommand('bofang', new CKEDITOR.dialogCommand('bofang'));
        a.ui.addButton('bofang', {
            label: '播放按钮',
            command: 'bofang',
            icon: this.path + 'images/icon.jpg'
        });
        CKEDITOR.dialog.add('bofang', function (editor) {
            return {
                title:'选择播放方式',
                minWidth: 300,
                minHeight: 50,
                contents:[{
                        id:'bofangList',
                        label:'bofangList',
                        title:'选择播放方式',
                        elements:[{
                                type:'select',
                                label:'bofangSelect',
                                id: 'bofangSelect',
                                'default': 'aniu',
                                items: [                                                                  
                                    ['欧洲杯', 'ozb'],                                    
                                    ['腾讯投屏', 'txtp'],                                    
                                    ['vst投屏', 'vsttp'],
                                    ['阿牛直播', 'aniu'],
                                    ['斗鱼直播', 'douyu'],    
                                    ['电视家直播', 'dsj'], 
                                    ['电视猫直播', 'dsm'],                                    
                                    ['cibn点播', 'cibn'], 
                                    ['电视猫点播', 'dsmdian'],
                                    ['爱奇艺点播', 'iqiyi'],
                                    ['芒果点播', 'mango'],
                                    ['蜜蜂点播', 'mifeng'],
                                    ['魔力点播', 'moli'],
                                    ['腾讯点播', 'qq'],
                                    ['vst点播', 'vstdian'],
                                    ['优酷点播', 'youku'],
                                ]
                        },{
                                type:'select',
                                label:'typeSelect',
                                id: 'typeSelect',
                                'default': 'phone',
                                items: [                                                                  
                                    ['手机详情', 'phone'],                                    
                                    ['盒子详情', 'box'],
                                    ['CIBN付费', 'cibnvip'],
                                ]
                        },{
                                id:'bofangId',
                                label:'播放id',
                                title:'播放id',
                                type:'text',
                        }]
                }],
                onOk:function(){
                    var bofangSource    =   this.getValueOf('bofangList', 'bofangSelect');
                    var bofangId        =   this.getValueOf('bofangList', 'bofangId');
                    var type            =   this.getValueOf('bofangList', 'typeSelect');
                    $.ajax({
                        url:'/Specialnew/getVid?source='+bofangSource+'&id='+encodeURIComponent(bofangId),
                        success:function(data){
                            var ret    =   eval("("+data+")");
                            var action  =   '';
                            switch(type){
                                case 'phone':
                                    action  =   'zhuanti.phone(\''+ret.data.id+'\')';
                                break;                                
                                case 'box':
                                    action  =   'zhuanti.bofang(\''+bofangSource+'\',\''+ret.data.id+'\')';
                                break;                                
                                case 'cibnvip':
                                    action  =    'zhuanti.phone(\''+ret.data.id+'\',\'cibnvip\')';
                                break;
                            }                           
                            editor.insertHtml('<div><button  class="play" onclick="'+action+'">播放</button></div><p></p>');
                        },
                    });

                }
            };
        });
    }
});
})();
