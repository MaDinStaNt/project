	function Query( method, url, data ){
		try{
			xmlhttp = new XMLHttpRequest();
		}
		catch (e){ 	xmlhttp = null	}
		if ( xmlhttp == null ){
			try{
			 	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e){ 	xmlhttp = null	}
		}
		if ( xmlhttp == null ){
			try{
		 		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e){ 	xmlhttp = null	}
		}
		if ( xmlhttp == null){
			alert('Your browser not support');
			return;
		}
	
		xmlhttp.open( method, url,false);
		xmlhttp.send(data);
		return xmlhttp.responseXML;
	}