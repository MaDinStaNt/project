/*
	direction: 	1 - left=>right
				2 - right=>left
*/

function MoveItems( left_id, right_id, direction ){
	var left_list = document.getElementById( left_id );
	var right_list = document.getElementById( right_id );
	
	switch ( direction ){
		case 1:{
			var from_list = left_list;
			var to_list = right_list;
			break;
		}
		case 2:{
			var from_list = right_list;
			var to_list = left_list;
			break;
		}
	}
	
	while( from_list.selectedIndex != -1){
		var value = from_list.options[from_list.selectedIndex].value;
		var text = from_list.options[from_list.selectedIndex].text;
		to_list.options[to_list.options.length] = new Option(text, value, '', '');
		from_list.options[from_list.selectedIndex] = null;
	}
	
	var left_idx = document.getElementById(left_id+'_idx');
	var right_idx = document.getElementById(right_id+'_idx');
	left_idx.value = "";
	right_idx.value = "";
	for ( var i=0; i<left_list.options.length; i++ )
		left_idx.value = left_idx.value + left_list.options[i].value + ";";
	for ( var i=0; i<right_list.options.length; i++ )
		right_idx.value = right_idx.value + right_list.options[i].value + ";";
	
}

