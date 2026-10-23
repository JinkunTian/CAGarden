(function(){
    'use strict';
    var menu=document.querySelector('[data-workstudy-menu]');
    if(menu){
        var navigation=document.getElementById(menu.getAttribute('aria-controls'));
        menu.addEventListener('click',function(){
            var expanded=menu.getAttribute('aria-expanded')!=='true';
            navigation.classList.toggle('in',expanded);
            menu.classList.toggle('collapsed',!expanded);
            menu.setAttribute('aria-expanded',expanded?'true':'false');
            menu.setAttribute('aria-label',expanded?'收起导航菜单':'展开导航菜单');
        });
        navigation.addEventListener('keydown',function(event){
            if(event.key==='Escape'&&menu.getAttribute('aria-expanded')==='true'){
                menu.click();menu.focus();
            }
        });
    }
    if(window.jQuery){
        window.jQuery('.ws-nav .dropdown').on('shown.bs.dropdown hidden.bs.dropdown',function(event){
            this.querySelector('.ws-account').setAttribute('aria-expanded',event.type==='shown'?'true':'false');
        });
    }
    var cards=document.querySelectorAll('[data-project-card]');
    var filters=document.querySelector('[data-project-filters]');
    var total=document.querySelector('[data-project-total]');
    var counts={all:cards.length,open:0,mine:0};
    for(var i=0;i<cards.length;i++){
        if(cards[i].getAttribute('data-status')==='1'){counts.open++;}
        if(cards[i].getAttribute('data-applied')==='1'){counts.mine++;}
    }
    if(total){total.textContent='共 '+counts.all+' 个项目';}
    if(filters&&cards.length){
        var buttons=filters.querySelectorAll('[data-project-filter]');
        var empty=document.querySelector('[data-filter-empty]');
        var status=document.querySelector('[data-filter-status]');
        function applyFilter(value){
            var visible=0;
            for(var n=0;n<cards.length;n++){
                var match=value==='all'||(value==='open'&&cards[n].getAttribute('data-status')==='1')||(value==='mine'&&cards[n].getAttribute('data-applied')==='1');
                cards[n].hidden=!match;
                if(match){visible++;}
            }
            for(var b=0;b<buttons.length;b++){
                var active=buttons[b].getAttribute('data-project-filter')===value;
                buttons[b].classList.toggle('is-active',active);
                buttons[b].setAttribute('aria-pressed',active?'true':'false');
            }
            empty.hidden=visible>0;
            if(!visible){
                empty.querySelector('[data-filter-empty-title]').textContent=value==='mine'?'你还没有提交报名':'当前暂无开放报名';
                empty.querySelector('[data-filter-empty-description]').textContent=value==='mine'?'选择开放报名的项目，填写并上传报名表后，记录会显示在这里。':'可以查看全部项目，留意后续招募信息。';
            }
            status.textContent=(value==='mine'?'我的报名':value==='open'?'报名中':'全部项目')+'：'+visible+' 个项目。';
        }
        for(var j=0;j<buttons.length;j++){
            var key=buttons[j].getAttribute('data-project-filter');
            buttons[j].querySelector('[data-filter-count]').textContent=counts[key];
            buttons[j].setAttribute('aria-controls','project-list');
            buttons[j].addEventListener('click',function(){applyFilter(this.getAttribute('data-project-filter'));});
        }
        document.querySelector('[data-reset-filter]').addEventListener('click',function(){applyFilter('all');buttons[0].focus();});
        filters.hidden=false;
    }
    var file=document.querySelector('[data-workstudy-file]');
    if(file){
        var feedback=document.querySelector('[data-file-feedback]');
        function checkFile(){
            var selected=file.files&&file.files[0];
            var error='';
            if(selected){
                if(!/\.(doc|docx)$/i.test(selected.name)){error='请选择 DOC 或 DOCX 格式的 Word 报名表。';}
                else if(selected.size<=0){error='文件内容为空，请重新选择填写完成的报名表。';}
                else if(selected.size>10*1024*1024){error='文件超过 10MB，请压缩后重新选择。';}
            }
            file.setCustomValidity(error);
            file.setAttribute('aria-invalid',error?'true':'false');
            feedback.classList.toggle('is-error',!!error);
            feedback.textContent=error||(selected?'已选择：'+selected.name+'（'+(selected.size<1024*1024?Math.ceil(selected.size/1024)+' KB':(selected.size/1024/1024).toFixed(2)+' MB')+'）':'');
            return !error;
        }
        file.addEventListener('change',checkFile);
        file.form.addEventListener('submit',function(event){
            if(!checkFile()){event.preventDefault();file.reportValidity();file.focus();}
        },true);
        window.addEventListener('pageshow',checkFile);
    }
})();
