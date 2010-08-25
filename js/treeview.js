<!-- <![CDATA[

if(typeof(IMAGES) =='undefined'){
        IMAGES ='../images/'
}

if(typeof(excol)=='undefined'){
        // define by default
        expids        ={};

        findnode=function(obj, tag, name, child){
                if(! child){
                        window.findobj =undefined;
                }

                if(obj && obj.childNodes){
                        for(var i =0; i <obj.childNodes.length; i ++){
                                if(typeof(window.findobj) !='undefined'){
                                        return window.findobj;
                                }
                                temp =obj.childNodes.item(i);
                                if(temp.nodeType !=3){
                                        if(temp.tagName && temp.tagName.toLowerCase() ==tag.toLowerCase() && (!name || (name && temp.name && name.toLowerCase() ==temp.name.toLowerCase()))){
                                                return window.findobj =temp;
                                        }
                                }
                                findnode(obj.childNodes.item(i), tag, name, 1);
                        }
                }
                return window.findobj;
        }

        chsel =function(oid){
                oid =parseInt(oid);
                if(typeof(expids[oid])!='object'){
                        expids[oid] =Array(0,0);
                }
                if(typeof(expids['active']) =='undefined'){
                        expids['active'] =oid;
                }
                expids[expids['active']][1] =0;
                expids[expids['active'] =oid][1] =1;
        }

        frmsbm =function(){
                ret ='';
                for(oid in expids){
                        if(! parseInt(oid)){
                                continue;
                        }
                        if(typeof(expids[oid]) =='object'){
                                if((count =parseInt(expids[oid][0]) +parseInt(2 *expids[oid][1])) >0){
                                        ret +=((ret)? ',': '')+ oid+ ':'+ count;
                                }
                        }else if(parseInt(expids[oid])){
                                ret +=((ret) ?',': '')+ oid+ ':'+ parseInt(expids[oid]) %2;
                        }
                }
                return ret;
        }

        excol =function(obj,oid,act){
                if(typeof(excol_images) =='undefined'){
                        excol_images =Array('expand', 'collapse','pixel-cross','pixel-line');
                        for(i =0; i <excol_images.length; i++){
                                name =excol_images[i];
                                excol_images[i] =newImg(IMAGES+ 'tree/'+ name+ '.gif');
                        }
                }
                oid =parseInt(oid);
                tmp =obj;

                while((tag =tmp.tagName.toLowerCase()) !='tr'){
                        tmp =tmp.parentNode;
                }
                if(tag =='tr'){
                        child =tmp.parentNode.childNodes.item(tmp.sectionRowIndex+1);
                        img2=findnode(findnode(tmp, 'table'), 'img');
                        img =findnode(tmp, 'img');

                        if(typeof(expids[oid]) !='array'){
                                        expids[oid] =Array(0,0);
                        }
                        expids[oid][0] =(child.style.display)? 1: 0;

                        if(child.style.display !=''){
                                child.style.display ='';

                                if(img2 && img2.src.toLowerCase().indexOf('pixel-line') >-1){
                                        img2.src =excol_images[2].src;
                                }else if(img2 && act){
                                        img2.src =img2.src.replace(/((_active)*)(?=\.\w+(\?[;&=%\w]+)?)/, '_active');
                                }
                                if(img2 && act){
                                        img2.src =img2.src.replace(/_active(?=\.\w+(\?[;&=%\w]+)?)/, '');
                                }
                                img.src =excol_images[1].src;
                        }else{
                                child.style.display ='none';

                                if(img2 && img2.src.toLowerCase().indexOf('pixel-cross') >-1){
                                        img2.src =excol_images[3].src;
                                }else if(img2 && act){
                                        img2.src =img2.src.replace(/_active(?=\.\w+(\?[;&=%\w]+)?)/, '');
                                }
                                if(img2 && act){
                                        img2.src =img2.src.replace(/_active(?=\.\w+(\?[;&=%\w]+)?)/, '');
                                }
                                img.src        =excol_images[0].src;
                        }
                }
        }
}
// preload images
function newImg(src){
    var tmp =new Image();
    tmp.src =src;
    return tmp;
}
function nodesel(evt){
        evt=(window.event)? window.event: ((evt)? evt: null);
        obj=(typeof(evt.srcElement) !='undefined')? evt.srcElement: ((typeof(evt.target) !='undefined')? evt.target: null);

        if(evt && obj){
                if((t =typeof(expsel)) !='undefined'){
                    if(t !='object'){
                            expsel =document.getElementById(expsel);
                    }
                    if(expsel){
                            expsel.className ='treeView';
                    }
                }
                tmp =obj.id.split('_');
                (expsel=obj).className="treeViewSel";

                if(obj.href.lastIndexOf('#') +1 !=obj.href.length){
                        var tree =frmsbm();
                        obj.href =obj.href.replace(/[&]*tree=[^&]*/gi, '')+ ((tree)? '&tree=' +tree+','+tmp[1]+':3': '');
                }
        }
}
// ]]> -->