

function SetState(el,st){
		alert(el.id);
		//eval(s);
		if(st){
			el.className='mmactive';
		}
		else{
			alert(el.id);
			if(el.name!=c){
				el.className='mmdactive';
			};
		};
	}




function preloadImg(){
	if (document.images){
		var imgSrc = preloadImg.arguments;
		imgArray = new Array(imgSrc.length);
		for (var c = 0; c < imgSrc.length; c++){
			imgArray[c] = new Image();
			imgArray[c].src = imgSrc[c];
		}
  }
}

function setImgSrc(idVal, srcVal){
  if (document.images) document.images[idVal].src = srcVal;
}

function setClass(obj, cl){
	if (obj.className!=cl) obj.className = cl;
}

function setClassById(objid, cl){
	if (!cl) cl = '';
	document.getElementById(objid).className = cl;
}

function changeDisplayById(objId){
	for (c = 0; c < changeDisplayById.arguments.length; c++){
		obj = document.getElementById(changeDisplayById.arguments[c]);
		if (obj.style.display == 'none') obj.style.display = 'block';
		else obj.style.display = 'none';
	}
}

function gotoURL(url){
	if (!url) url = "/";
	if (window.event){
		var src = window.event.srcElement;
		if((src.tagName != 'A') && ((src.tagName != 'IMG') || (src.parentElement.tagName != 'A'))){
			if (window.event.shiftKey) window.open(url);
			else document.location = url;
		}
	} else document.location = url;
}

function popupURL(url){
	window.open(url);
}

function getLeftPos(obj){
	var res = 0;
	while (obj){
		res += obj.offsetLeft;
		//alert(obj.style.borderLeftWidth);
		obj = obj.offsetParent;
	}
	return res;
}

function getTopPos(obj){
	var res = 0;
	while (obj){
		res += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return res;
}

function chbCheckAll(formObj, checkName, checkVal){
	var el = formObj.elements;
	for (count = 0; count < el.length; count++)
		if (el[count].name == checkName + '[]')
			if (!el[count].disabled) el[count].checked = checkVal;
}

function chbExamAll(formObj, checkName, resName){
	var checkCount = 0;
	var boxCount = 0;
	var el = formObj.elements;
	for (count = 0; count < el.length; count++)
		if (el[count].name == checkName + '[]'){
			boxCount++;
			if (el[count].checked || el[count].disabled) checkCount++;
		}
	formObj.elements[resName].checked = (checkCount == boxCount);
}

function chbIsAllEmpty(formObj, checkName){
	var checkCount = 0;
	var boxCount = 0;
	var el = formObj.elements;
	for (count = 0; count < el.length; count++)
		if (el[count].name == checkName + '[]'){
			boxCount++;
			if (el[count].checked) checkCount++;
		}
	return(checkCount == 0);
}

function chbIsOnlyOne(formObj, checkName){
	var checkCount = 0;
	var boxCount = 0;
	var el = formObj.elements;
	for (count = 0; count < el.length; count++){
		if (el[count].name == checkName + '[]'){
			boxCount++;
			if (el[count].checked) checkCount++;
		}
	}
	return(checkCount == 1);
}

function disableAll(){
	for (c1 = 0; c1 < document.forms.length;  c1++){
		var formElements = document.forms[c1].elements;
		for (c2 = 0; c2 < formElements.length;  c2++) formElements[c2].disabled = true;
	}
}

function AddEventHandler(obj,event, func){
	if (obj.attachEvent) { // IE
		obj.attachEvent("on" + event, func);
	} else if (obj.addEventListener)  // Gecko / W3C
		obj.addEventListener(event, func, true);
}

function RemoveEventHandler(obj,event, func){
	if (obj.attachEvent) { // IE
		obj.detachEvent("on" + event, func);
	} else if (obj.addEventListener)  // Gecko / W3C
		obj.removeEventListener(event, func, true);
}

function showPopup(url){
  var printWin = window.open(url, '', 'width=488, height=530, scrollbars=no, resizable=no');
}