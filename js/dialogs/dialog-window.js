LLADialog = function(){
	
	var resizable = false;
	this.Result = '';
	this.obj = null;
	
	this.show = function(Url, DlgInfo, Width, Height ){
		if ( window.showModalDialog ){
			this.Result = window.showModalDialog(Url,DlgInfo, "dialogWidth:" + Width + "px;dialogHeight:" + Height + "px;help:no;scroll:no;status:no");
			this.obj.Result=this.Result;
			this.obj.ie = true;
			//ColorDlg.Color = window.showModalDialog(Url,DlgInfo, "dialogWidth:" + Width + "px;dialogHeight:" + Height + "px;help:no;scroll:no;status:no");
		}
		else if(document.addEventListener){
			this.obj.ie = false;
			var iTop  = (screen.height - Height) / 2 -100 ;
			var iLeft = (screen.width  - Width)  / 2 ;
			var sOption  = "location=no,menubar=no,toolbar=no,dependent=yes,dialog=yes,minimizable=no,modal=yes,alwaysRaised=yes" +
				",resizable="  + ( resizable ? 'yes' : 'no' ) +
				",width="  + Width +
				",height=" + Height +
				",top="  + iTop +
				",left=" + iLeft ;
			parentWindow = window ;
			var oWindow;
			oWindow = parentWindow.open( Url, 'Dialog', sOption, true ) ;
			if ( !oWindow )
			{
				alert( 'Blocked' ) ;
				return ;
			}
			oWindow.moveTo( iLeft, iTop ) ;
			oWindow.resizeTo( Width, Height ) ;
			oWindow.focus() ;
			oWindow.location.href = Url ;
			oWindow.dialogArguments = DlgInfo ;
			// On some Gecko browsers (probably over slow connections) the 
			// "dialogArguments" are not set to the target window so we must
			// put it in the opener window so it can be used by the target one.
			parentWindow.FCKLastDialogInfo = DlgInfo ;
			this.Window = oWindow ;
			// Try/Catch must be used to avoit an error when using a frameset 
			// on a different domain: 
			// "Permission denied to get property Window.releaseEvents".
			try
			{
				window.top.parent.Color='#ffffff';
				window.top.captureEvents( Event.CLICK | Event.MOUSEDOWN | Event.MOUSEUP | Event.FOCUS ) ;
				/*AddEventHandler(window.top.parent, 'mousedown', this.CheckFocus);
				AddEventHandler(window.top.parent, 'mouseup', this.CheckFocus);
				AddEventHandler(window.top.parent, 'click', this.CheckFocus);
				AddEventHandler(window.top.parent, 'focus', this.CheckFocus);*/
				window.top.parent.addEventListener( 'mousedown', this.CheckFocus, true ) ;
				window.top.parent.addEventListener( 'mouseup', this.CheckFocus, true ) ;
				window.top.parent.addEventListener( 'click', this.CheckFocus, true ) ;
				window.top.parent.addEventListener( 'focus', this.CheckFocus, true ) ;
			}
			catch (e)
			{}
		}
	}
	
	this.CheckFocus = function(){
	// It is strange, but we have to check the FCKDialog existence to avoid a 
	// random error: "FCKDialog is not defined".
		if ( typeof( Dialog ) != "object" )
			return false ;
	
		if ( Dialog.Window && !Dialog.Window.closed )
			Dialog.Window.focus() ;
		else
		{
		// Try/Catch must be used to avoit an error when using a frameset 
		// on a different domain: 
		// "Permission denied to get property Window.releaseEvents".
			try
			{
				window.top.parent.addEventListener( 'mousedown', this.CheckFocus, true ) ;
				window.top.parent.addEventListener( 'mouseup', this.CheckFocus, true ) ;
				window.top.parent.addEventListener( 'click', this.CheckFocus, true ) ;
				window.top.parent.addEventListener( 'focus', this.CheckFocus, true ) ;

			}
			catch (e)
			{}
		}
		return false ;
	}
}



Dialog = new LLADialog();

//Dialog.show('http://kelbasv:85','asdfsa',200,200);

