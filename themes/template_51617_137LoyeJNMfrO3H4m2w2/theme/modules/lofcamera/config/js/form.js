/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function() {    
    
    //create params tab :
    $(function() {
        $( "#loftab" ).dvcTab({
            navCls: 'tabs-nav'
        });
    });	         
    
    
});

function reportFilesSelected(elem, view){ 
    
    var input = document.getElementById($(elem).attr('id'));
    var ul = document.getElementById(view);
    while (ul.hasChildNodes()) {
        ul.removeChild(ul.firstChild);
    }
    for (var i = 0; i < input.files.length; i++) {
        var li = document.createElement("li");
        li.innerHTML = '<span> '+input.files[i].name+'</span>';
        ul.appendChild(li);
    }
    if(!ul.hasChildNodes()) {
        var li = document.createElement("li");
        li.innerHTML = 'No Files Selected';
        ul.appendChild(li);
    }    
}

function selectImage(elem) {
    var myParent = $(elem).parent('div'); 
    $(myParent).toggleClass('image_for_remove');
}

function changeToLanguage(id) {
    
    var current_flag = '<img src="../img/l/' + id + '.jpg" class="pointer" id="language_current_' + id + '" onclick="toggleLanguageFlags(this);" alt="" />';
    
    $('.displayed_flag').html(current_flag);
    $('.language_flags').css('display', 'none');
    var target_id = 'lang_'+id; 
    $('.info_lang').each(function(){
        if($(this).attr('id') == target_id) {
            $(this).fadeIn();
        } else {
            $(this).css('display', 'none');
        }
        
    });
}