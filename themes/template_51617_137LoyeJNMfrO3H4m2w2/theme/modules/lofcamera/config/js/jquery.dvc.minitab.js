/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$.fn.dvcTab = function(options) {
    var defaults = {
        prefix: 'dvc-tab-container',
        selectedNavCls: 'tabs-selected',
        tabCls:'dvc-tab',
        navCls: 'dvc-nav',
        fxSpeed: 300,
        start: 0
    }
    var opt = $.extend(defaults, options); 
    
    return this.each(function(i,item) { 
        var itemid = '';
        if(!$(item).attr('id')) {
            itemid = opt.prefix+i;
        } else {
            itemid = $(item).attr('id');
        }
        $(item).attr('id', itemid);   
        $(item).addClass('minitab-main');
        opt.containerId = '#'+itemid; 
        var tabs = $(opt.containerId+'> div'); 
        var navi = $(opt.containerId+'> ul');    
        
        //add some class :
        tabs.addClass(opt.tabCls);
        navi.addClass(opt.navCls);    
        var navSub = $(opt.containerId+' .'+opt.navCls+' li a'); 

        $.fn.dvcTab.selectTab($(navSub[opt.start]), opt);
        navSub.click(function(event){
            ie8SafePreventEvent(event);
            var parent = $.fn.dvcTab;            
            opt.containerId = getParentLv3ID(this);                                
            parent.selectTab($(this), opt);            
        });
    });
    
};
$.fn.dvcTab.selectTab = function(elem, options){
    var selectedId = elem.attr('href');
    $(options.containerId+' > .'+options.navCls+' li').removeClass(options.selectedNavCls)
    elem.parent().addClass(options.selectedNavCls); 
    
    $(options.containerId+' > .'+options.tabCls).css('display', 'none');
    $(selectedId).css('display', 'block');
}
function getParentLv3ID(elem) {
    var p1 = $(elem).parent().get(0);
    var p2 = $(p1).parent().get(0);
    var p3 = $(p2).parent().get(0);
    return '#'+$(p3).attr('id');
}
function ie8SafePreventEvent(e){
    if(e.preventDefault){
        e.preventDefault();        
    } else{
        e.stop();
    }
    e.returnValue = false;
    e.stopPropagation();        
    
}
