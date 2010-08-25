function init_threads(){
	for( var i = 0; i < GLOBALS['Threads'].length; i++ )
		GLOBALS['Threads'][i].init();
}
AddEventHandler( window, 'load', init_threads );

CThreadControl = function( Id ){	
	this.id = Id;
	this.redirect = '';
	this.timer = new CTimer( 'p' + this.id + '_timer', 2000 );
	this.timer.SetVar( 'pid', this.id );
	this.show_details = false;
	this.button_area = document.getElementById( 'p' + this.id + '_button_area');
	var len = GLOBALS['Threads'].length;
	GLOBALS['Threads'][len] = this;
	
	this.set_text = function( text ){
		if ( this.show_details ){
			var area =	document.getElementById( 'p' + this.id + '_progress_details_area');
			area.value = text + '\n' + area.value ;
		}
	}
	
	this.set_status = function( status ){
		if ( ( status == 4 ) || ( status == 5 ) ){
			this.timer.Stop();
			this.button_area.innerHTML = '';
			Query('GET', host + 'connectors/thread.php?command=Free&pid=' + this.id , '' );	
			if ( this.redirect != '' )
				location.href = this.redirect;
		}
	}
	
	this.set_progress = function( percent ){
		document.getElementById( 'p' + this.id + '_text' ).innerHTML = percent + '%';
		var width = document.getElementById( 'p' + this.id + '_progress_bar' ).width;
		var img = document.getElementById( 'p' + this.id + '_progress' );
		if ( percent == 0 ){
			img.width = 1;
		}
		else if( percent == 100 ){
			img.width = width;
			/*this.timer.Stop();
			this.button_area.innerHTML = '';
			Query('GET', host + 'connectors/thread.php?command=Free&pid=' + this.id , '' );	
			if ( this.redirect != '' )
				location.href = this.redirect;*/
		}
		else{
			img.width = Math.round( (width / 100 * percent) );
		}
	}
	
	this.onkill = function(){}
	
	this.Stop = function(){
		if ( confirm( 'Are you shure to cancel this task?' ) ){
			//this.timer.Stop();
			Query('GET', host + 'connectors/thread.php?command=Kill&pid=' + this.id , '' );	
			this.button_area.innerHTML = '';
		}
	}
	
	
	this.update_data = "var XML = Query('GET', host + 'connectors/thread.php?command=GetInfo&pid=" + this.id +"');	GLOBALS['Threads']["+len+"].parse_info(XML);	";
	this.timer.ontimer = this.update_data;
	
	this.parse_info = function(XML){ 
		var errors = XML.getElementsByTagName('error');
		if ( errors.length > 0 ){
			alert( 'error' )
		}
		else{
			var progress = XML.getElementsByTagName('progress')[0];
			this.set_progress( progress.firstChild.nodeValue );
			var status = XML.getElementsByTagName('status')[0];
			this.set_status( status.firstChild.nodeValue );
		}
		var messages = XML.getElementsByTagName('msg');
		for ( var i = 0; i < messages.length; i++ )
			this.set_text( messages[i].firstChild.nodeValue );	
		
	}
	
	this.init = function(){
		this.set_progress(0);
		XML = Query('GET', host + 'connectors/thread.php?command=Init&pid=' + this.id , '' );	
		var errors = XML.getElementsByTagName('error');
		if ( errors.length > 0 ){
			alert( errors[0].firstChild.nodeValue )
			this.set_status(5);
		}
		else{
			var progress = XML.getElementsByTagName('progress')[0];
			this.set_progress( progress.firstChild.nodeValue );
			
			var messages = XML.getElementsByTagName('msg');
			for ( var i = 0; i < messages.length; i++ )
				this.set_text( messages[i].firstChild.nodeValue );	
			options = XML.getElementsByTagName('options')[0];
			var s = '';
			var f = false;
			if(options.getAttribute('pause') == 1){
				s = '&nbsp;<input onclick="t' + this.id + '.Pause();" id="p" + this.id + "_button" type="button" value="Pause" name="stop" class="butt inact" onmouseover="this.className=' + "'butt act'"+ ';" onmouseout="this.className='+"'butt inact'"+';" />&nbsp;';
				f = true;
			}
			if(options.getAttribute('resume') == 1)
				s = '&nbsp;<input onclick="t' + this.id + '.Resume();" id="p" + this.id + "_button" type="button" value="Resume" name="stop" class="butt inact" onmouseover="this.className=' + "'butt act'"+ ';" onmouseout="this.className='+"'butt inact'"+';" />&nbsp;';	
			if(options.getAttribute('stop') == 1){
				s = s + '&nbsp;<input onclick="t' + this.id + '.Stop();" id="p" + this.id + "_button" type="button" value="Cancel" name="stop" class="butt inact" onmouseover="this.className=' + "'butt act'"+ ';" onmouseout="this.className='+"'butt inact'"+';" />';
				f = true;
			}
			this.button_area.innerHTML = s;
			if ( f )
				this.timer.Start();
		}
	}
	
	
	
	
	this.Pause = function(){
		alert('pause');
	}
	
	this.Resume = function(){
		alert('resume');
	}
	
	
	
	
	
	
	

}
