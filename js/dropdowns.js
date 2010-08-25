var lastMenuId = 0;
var timer;
var mSheets = new Array();
var currZ = 100;
var mReady = false;
var is_ie = ( /msie/i.test(navigator.userAgent) &&
		   !/opera/i.test(navigator.userAgent) );
function MenuLink(textVal, linkVal, subVal){
	this.text = textVal;
	this.action = linkVal;
	this.submenu = subVal;
}

function menuHideAll(){
	for (var c in mSheets) mSheets[c].hide();
}

function menuHideTimerSet(){
	timer = window.setTimeout(menuHideAll, 100);
}

function menuHideTimerReset(){
	if (timer) window.clearTimeout(timer);
}

function menuAddLink(textVal, linkVal){
	this.links[this.links.length] = new MenuLink(textVal, linkVal, null);
}

function menuAddSubmenu(textVal, linkVal){
	this.links[this.links.length] = new MenuLink(textVal, linkVal, new MenuSheet(this));
}

function menuShow(leftVal, topVal){
	this.block.style.left = leftVal + "px";
	this.block.style.top = topVal + "px";
	this.block.style.display = "block";

	if (is_ie)
	{
		if (!this.hider)
			this.hider = this._createHider();
		this._setupHider(this.hider, leftVal, topVal, this.block.offsetWidth, this.block.offsetHeight);
	}
}

function menuHide(){
	if (is_ie)
		this._closeHider(this.hider);
	this.hideCh();
	this.block.style.display = "none";
}

function menuFlip(leftVal, topVal){
	var disp = this.block.style.display;
	if (disp == "none") this.show(leftVal, topVal);
	else this.hide();
}

function menuHideCh(){
	for (var c in this.links){
		curLink = this.links[c];
		if (curLink.submenu) curLink.submenu.hide();
	}
}

function menuCreate(path){
	var res = "<div class=\"menu-sh\" onmouseout=\"menuHideTimerSet()\" onmouseover=\"menuHideTimerReset()\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tab-menu-sh\">";
	var curLink;
	var newPath;
	if (path == null) path = "mSheets[" + this.id + "]";
	for (var c in this.links){
		curLink = this.links[c];
		res += "<tr><td class=\"blk-menu-sh";
		if (curLink.submenu) res += " blk-menu-arr";
		res += "\" onmouseover=\"setClass(this, 'blk-menu-sh-act";
		if (curLink.submenu) res += " blk-menu-arr-act";
		res += "'); ";
		res += path + ".hideCh()";
		if (curLink.submenu){
			newPath = path + ".links[" + c + "].submenu";
			res += "; " + newPath + ".show(getLeftPos(this) + this.offsetWidth, getTopPos(this) - 2)";
			curLink.submenu.create(newPath);
		}
		res += "\" onmouseout=\"setClass(this, 'blk-menu-sh";
		if (curLink.submenu) res += " blk-menu-arr";
		res += "')\" onclick=\"gotoURL('"+curLink.action+"')\" nowrap=\"nowrap\">" + curLink.text + "</td></tr>";
	}
	res += "</table></div>";
	this.block.innerHTML = res;
	this.block.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=90)";
}

function MenuSheet(parentObj){
	this.links = new Array();
	this.addLink = menuAddLink;
	this.addSubmenu = menuAddSubmenu;
	this.create = menuCreate;
	this.show = menuShow;
	this.hide = menuHide;
	this.flip = menuFlip;
	this.hideCh = menuHideCh;
	this.id = lastMenuId++;
	this.parent = parentObj; 
	this.block = document.createElement("DIV");
	this.block.className = "blk-menu";
	this.block.style.position = "absolute";
	this.block.style.display = "none";
	this.block.style.zIndex = currZ++;
	this.block.id = "ms" + this.id;
	document.body.appendChild(this.block);
}

function showMenu(objVal, numVal){
	if (mReady){
		menuHideAll();
		objVal.className = "item act";
		mSheets[numVal].show(getLeftPos(objVal), getTopPos(objVal) + 4);
		menuHideTimerReset();
	}
}

function hideMenu(objVal, numVal){
	if (mReady){
		menuHideTimerSet();
		objVal.className = "item";
	}
}

MenuSheet.prototype._createHider = function ()
{
	var f=null;
	var filter = 'filter:progid:DXImageTransform.Microsoft.alpha(style=0,opacity=0);';
	var id = 'dynarch-menu-hider-' + (this.id);
	window.self.document.body.insertAdjacentHTML('beforeEnd', '<iframe id="'+id+'" scroll="no" frameborder="0" '+'style="position:absolute;visibility:hidden;'+filter+'border:0;top:0;left:0;width:0;height:0;" '+'src="javascript:false;"></iframe>');
	f = window.self.document.getElementById(id);
	return f;
};
MenuSheet.prototype._setupHider = function (f, x, y, w, h)
{
	if (f)
	{
		var s=f.style;
		s.left=x+"px";
		s.top=y+"px";
		s.width=w+"px";
		s.height=h+"px";
		s.visibility="visible";
	}
};
MenuSheet.prototype._closeHider = function (f)
{
	if (f)
		f.style.visibility="hidden";
};

function createMenus()
{
// sample menu creation
	for (c = 0; c < 2; c++) mSheets[c] = new MenuSheet(); 
 
	mSheets[0].addLink("test", "/");

	mSheets[1].addLink("test 2", "/");
	mSheets[1].addSubmenu("test 3", "/");
		mSheets[1].links[1].submenu.addLink("test 4", "/");

	for (var c in mSheets) mSheets[c].create();

	mReady = true;
}

