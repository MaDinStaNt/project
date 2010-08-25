//var Timers = [];
function TimerUpdate( name ){
	if( GLOBALS['Timers'][name]['id'] ) {
    	clearTimeout( GLOBALS['Timers'][name]['id'] );
    }
	GLOBALS['Timers'][name]['id'] = setTimeout("TimerUpdate('" + name + "')", GLOBALS['Timers'][name]['interval']);
	eval(GLOBALS['Timers'][name]['ehandler']);
}

CTimer = function( name, d ){
	this.interval = d ;
	this.Name = name;
	this.ontimer = "";
	GLOBALS['Timers'][this.Name] = [];
	GLOBALS['Timers'][this.Name]['vars'] = [];
	this.Start = function(){
		
		GLOBALS['Timers'][this.Name]['ehandler'] = this.ontimer;
		GLOBALS['Timers'][this.Name]['interval'] = this.interval;
		GLOBALS['Timers'][this.Name]['id'] = setTimeout( "TimerUpdate('" + this.Name + "')", this.interval );
	}
	
	this.Stop = function (){
		clearTimeout( GLOBALS['Timers'][this.Name]['id'] );
	}
	
	this.SetVar = function( VarName, Value ){
		GLOBALS['Timers'][this.Name]['vars'][VarName] = Value;
	}
	
	this.GetVar = function ( VarName ){
		return GLOBALS['Timers'][this.Name]['vars'][VarName];
	}
}

/*---Example------
		
var timer = new CTimer('test', 3000 );
timer.ontimer = function(){ alert('new handler')};
timer.Start();
*/
