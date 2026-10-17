(function(){
    'use strict';
    document.addEventListener('submit',function(event){
        var form=event.target;
        if(event.defaultPrevented||!form.hasAttribute('data-workstudy-submit')){return;}
        if(form.getAttribute('data-submitting')==='1'){event.preventDefault();return;}
        form.setAttribute('data-submitting','1');
        var buttons=form.querySelectorAll('button[type=submit]');
        for(var i=0;i<buttons.length;i++){
            buttons[i].setAttribute('data-original-html',buttons[i].innerHTML);
            buttons[i].disabled=true;
            buttons[i].textContent=form.getAttribute('data-loading-text')||'正在保存…';
        }
    });
    window.addEventListener('pageshow',function(){
        var forms=document.querySelectorAll('form[data-submitting]');
        for(var i=0;i<forms.length;i++){
            forms[i].removeAttribute('data-submitting');
            var buttons=forms[i].querySelectorAll('button[data-original-html]');
            for(var j=0;j<buttons.length;j++){
                buttons[j].disabled=false;
                buttons[j].innerHTML=buttons[j].getAttribute('data-original-html');
                buttons[j].removeAttribute('data-original-html');
            }
        }
    });
})();
