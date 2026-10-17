(function(){
    'use strict';
    var table=document.querySelector('.ws-applications-table');
    var column=document.getElementById('workstudy-name-column');
    var handle=document.querySelector('[data-name-resizer]');
    if(!table||!column||!handle){return;}

    var key='ca.workstudy.applications.nameColumnWidth.v1';
    var minimum=80;
    var maximum=360;
    function limit(width){return Math.max(minimum,Math.min(maximum,Math.round(width)));}
    function update(width,save){
        width=limit(width);
        column.style.width=width+'px';
        handle.setAttribute('aria-valuenow',String(width));
        if(save){
            try{window.localStorage.setItem(key,String(width));}catch(ignore){}
        }
    }
    try{
        var saved=parseInt(window.localStorage.getItem(key),10);
        if(isFinite(saved)){update(saved,false);}
    }catch(ignore){}
    handle.tabIndex=0;
    document.body.classList.add('ws-table-resize-ready');

    handle.addEventListener('keydown',function(event){
        var current=parseInt(handle.getAttribute('aria-valuenow'),10)||Math.round(column.getBoundingClientRect().width);
        if(event.key==='ArrowLeft'||event.key==='ArrowRight'){
            event.preventDefault();
            update(current+(event.key==='ArrowRight'?16:-16),true);
        }else if(event.key==='Home'){
            event.preventDefault();update(minimum,true);
        }else if(event.key==='End'){
            event.preventDefault();update(maximum,true);
        }
    });

    if(typeof window.PointerEvent==='function'){
        var pointerId=null,startX=0,startWidth=0;
        function move(event){
            if(event.pointerId!==pointerId){return;}
            update(startWidth+event.clientX-startX,false);
        }
        function stop(event){
            if(event.pointerId!==pointerId){return;}
            update(parseInt(handle.getAttribute('aria-valuenow'),10)||startWidth,true);
            pointerId=null;
            handle.classList.remove('is-dragging');
            document.removeEventListener('pointermove',move);
            document.removeEventListener('pointerup',stop);
            document.removeEventListener('pointercancel',stop);
        }
        handle.addEventListener('pointerdown',function(event){
            if(event.button!==0){return;}
            event.preventDefault();
            pointerId=event.pointerId;
            startX=event.clientX;
            startWidth=column.getBoundingClientRect().width;
            handle.classList.add('is-dragging');
            if(handle.setPointerCapture){handle.setPointerCapture(pointerId);}
            document.addEventListener('pointermove',move);
            document.addEventListener('pointerup',stop);
            document.addEventListener('pointercancel',stop);
        });
    }
})();
