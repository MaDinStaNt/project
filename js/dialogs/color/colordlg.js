// JavaScript Document



//var BasePath = '/js/dialogs/color/';
ColorDlg_BasePath = '';

StandartColors = '#000000,#993300,#333300,#003300,#003366,#000080,#333399,#333333,#800000,#FF6600,#808000,#808080,#008080,#0000FF,#666699,#808080,#FF0000,#FF9900,#99CC00,#339966,#33CCCC,#3366FF,#800080,#999999,#FF00FF,#FFCC00,#FFFF00,#00FF00,#00FFFF,#00CCFF,#993366,#C0C0C0,#FF99CC,#FFCC99,#FFFF99,#CCFFCC,#CCFFFF,#99CCFF,#CC99FF,#FFFFFF' ;
var AutoColor = '#000000';



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


function ColorDlg_MouseClick(Sender){
	Sender.style.display = "none";
}

function ColorDlg_MouseOver(Sender){
		Sender.className = 'ColorSelected';
		ColorDlg.Mouse = 1;
}

function ColorDlg_MouseOut(Sender){
		Sender.className = 'ColorDeselected';
		ColorDlg.Mouse = 0;
		AddEventHandler(document,'mousedown',ColorDlg_MouseDownMSG);
}


function ColorDlg_SetColor(name,clr){
	ColorDlg.Color = clr;
	if (ColorDlg.div != null){
		ColorDlg.div.style.backgroundColor = clr;
		//alert(clr.indexOf('rgb'));
		if( clr.indexOf('rgb') > -1 ){
			clr = clr.toLowerCase();
			clr = clr.replace('rgb(','');
			clr = clr.replace(')','');
			var ar = clr.split(', ');
			if ( ar.length>0){
				clr = '#';
				for (var i = 0; i<3; i++){
					var s = parseInt(ar[i]).toString(16);
					if (s.length == 1)
						s = '0' + s;
					clr += s;
				}
			}
		}
	}
	ColorDlg.input.value = clr;
	ColorDlg.hide();
}


function ColorDlg_MouseDownMSG(){
	if ( ColorDlg.Mouse == 0 )
		ColorDlg.hide()
	else
		RemoveEventHandler(document,'mousedown',ColorDlg_MouseDownMSG);
}

	
function RunDlg(obj){
	Dialog.obj = ColorDlg;
	Dialog.show(ColorDlg_BasePath+'colorselector.html','', 400, 330);
	if (ColorDlg.ie){
		ColorDlg.Color = ColorDlg.Result;
		ColorDlg_SetColor('', ColorDlg.Color);		
	}
	
}

CColorDlg = function (name){
	this.Name = name;
	this.block = document.createElement("DIV");
	this.block.style.position = "absolute";
	this.block.style.display = "none";
	this.block.style.zIndex = 101;
	this.Color = AutoColor;
	this.Mouse = 0;
	this.input = null;
	this.div = null;
	this.ie = ( /msie/i.test(navigator.userAgent) &&  !/opera/i.test(navigator.userAgent) );
	this.Result = '';
	this.onSetValue = function(value){}
	this.id = name;
	document.body.appendChild(this.block);
	
	this.setVal = function(val){
		ColorDlg_SetColor('',val);
	}
	
	this._createHider = function (){
		var f=null;
		var filter = 'filter:progid:DXImageTransform.Microsoft.alpha(style=0,opacity=0);';
		var id = 'dynarch-menu-hider-' + (this.id);
		window.self.document.body.insertAdjacentHTML('beforeEnd', '<iframe id="'+id+'" scroll="no" frameborder="0" '+'style="position:absolute;visibility:hidden;'+filter+'border:0;top:0;left:0;width:0;height:0;" '+'src="javascript:false;"></iframe>');
		f = window.self.document.getElementById(id);
		return f;
	};
	
	this._closeHider = function (f){
		if (f) 
			f.style.visibility="hidden";
	};
	
	this._setupHider = function (f, x, y, w, h){
		if (f){
			var s=f.style;
			s.left=x+"px";
			s.top=y+"px";
			s.width=w+"px";
			s.height=h+"px";
			s.visibility="visible";
		}
	};
	
	
	this.show = function(input_name){
		this.input = document.getElementById(input_name);		
		this.div = document.getElementById(input_name+'_div');
		var parent = this.div;
		AddEventHandler(document,'mousedown',ColorDlg_MouseDownMSG);
		this.block.style.display = "block";
		var leftVal = getLeftPos(parent);
		var topVal = getTopPos(parent);
		this.block.style.left = leftVal+ "px";
		this.block.style.top = topVal +  "px";
		var aColors = StandartColors.toString().split(',');
		var len = aColors.length;
		var i = 0;
		var s = '<div class="MenuStyle" >\
		<table width="100%" cellspacing="0" border="0" cellpadding="0" ><tr><td><div onmouseover="ColorDlg_MouseOver(this);" onmouseout="ColorDlg_MouseOut(this);" class="ColorDeselected" onclick="ColorDlg_SetColor(\''+this.Name+'\',\''+AutoColor+'\')" ><table width="100%"  cellspacing="0" border="0" cellpadding="0" ><tr><td><div class="ColorBoxBorder"><div class="ColorBox" style="background-color:' + AutoColor + '"></div></div></td><td align="center" width="100%" ><span class="ColorDlgFont">Auto</span></td></tr></table></div></td></tr></table>';
		s +='<table  cellspacing="0" border="0" cellpadding="0" >';
		while (i < len){
			s += '<tr>';
			for ( var j=i; j < i+8; j++ )
				s += '<td><div onmouseover="ColorDlg_MouseOver(this);" onmouseout="ColorDlg_MouseOut(this);" class="ColorDeselected" onclick="ColorDlg_SetColor(\''+this.Name+'\', document.getElementById(\'d_'+i+'_'+j+'\').style.backgroundColor)">\
				<div class="ColorBoxBorder"><div class="ColorBox" id="d_'+i+'_'+j+'" style="background-color:'+aColors[j]+'"></div></div></div></td>';
			i = j;
			s += '</tr>'; 
		}
		s += '</table>\
			<table cellspacing="0" border="0" cellpadding="0" width="144" ><tr><td width="144" onmouseover="ColorDlg_MouseOver(this);" onmouseout="ColorDlg_MouseOut(this);" class="ColorDeselected" align="center" onclick="RunDlg();" ><span class="ColorDlgFont">Other...</span></td></tr></table>\
		</div>';
		this.block.innerHTML = s;
		
		if (this.ie){
			if (!this.hider)
				this.hider = this._createHider();
			this._setupHider(this.hider, leftVal, topVal, this.block.offsetWidth, this.block.offsetHeight);
		}
	
	}
	
	this.hide = function(){
		this.block.style.display = "none";
		if (this.ie)
			this._closeHider(this.hider);
	}
}


function loadColorDlg(){
	ColorDlg = new CColorDlg('color1');
}
