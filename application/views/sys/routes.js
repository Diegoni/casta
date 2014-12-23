var index_domain = 0;
function slash_item(){
    var pref = Ext.app.base_url;
    if (pref != '' && pref.substr(-1) != '/') {
        pref += '/';
    }
    return pref;
}

function site_url(uri){
    if (uri == null) {
        return slash_item() + Ext.app.index_page;
    }
    else {
        var suffix = (Ext.app.url_suffix == false) ? '' : Ext.app.url_suffix;
        suffix = slash_item() + ((Ext.app.index_page!='')?(Ext.app.index_page + '/'):'') + uri + suffix;
        return suffix;
    }
}
