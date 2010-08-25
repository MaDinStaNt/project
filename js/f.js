// javascript functions

function htmlencode(str)
{
	str = '' + str;
	var re = /\&/gi;
	str = str.replace(re, '&amp;');
	re = /"/gi;
	str = str.replace(re, '&quot;');
	re = /</gi;
	str = str.replace(re, '&lt;');
	re = />/gi;
	str = str.replace(re, '&gt;');
	return str;
}

function trim(str)
{
	str = '' + str;
	var re = /^ */;
	var res = str.replace(re, '');
	re = / *$/;
	return(res.replace(re, ''));
}

function ShowAlert(sAlertString, hForm, FormElement)
{
	alert(sAlertString);
	if ( (!hForm.elements[FormElement].disabled) && (!hForm.elements[FormElement].length) )
	{
		if (hForm.elements[FormElement].type != 'hidden')
		{
			hForm.elements[FormElement].focus();
			if (!hForm.elements[FormElement].options) // select
				hForm.elements[FormElement].select();
		}
	}
	return false;
}

function parse_int(str)
{
	str = '' + str;
	var re = /^0*/;
	var res = str.replace(re, '');
	return parseInt(res);
}

function GetValue(el)
{
	if ( (el.length) && (!el.options) )
	{
		for (var i=0; i<el.length; i++)
			if (el[i].checked)
				return el[i].value;
		return '';
	}
	else
		return el.value;
}

function is_leap_year(year)
{
	return ( (year%4 == 0) && !( (year%100 == 0) && (year%400 != 0) ) );
}

function isValidEmail(mailstr)
{
  var re = /^ *([a-z0-9_-]+\.)*[a-z0-9_-]+@([a-z0-9-]+\.)+.*$/;
  return (re.test(mailstr.toLowerCase()));
}

function isValidPhone(phonestr) // only for US
{
	var re = /^\(?[0-9]{3}\)?[\-\. ]*[0-9]{3}[\-\. ]*[0-9]{4}$/;
	return re.test(phonestr);
}

function isValidUrlHTTP(url)
{
	var re = /^(http(s)?:\/\/){1}([a-z,0-9]*([-][a-z,0-9]+)*\.)+[a-z]+(:[0-9]+)?(\/.*)?$/i;
	return re.test(url);
}

function isValidNumber(num_str) // valid positive float number
{
	var re = /^[0-9]+(\.?[0-9]+)?$/;
	return re.test(num_str);
}

function isValidDate(str) 
{
	var re = /^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/;
	return re.test(str);
}

function isValidDateTime(str) 
{
	var re = /^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/;
	return re.test(str);
}

function isValidCC(s)
{
	var re = /[^0123456789]/g;
	s = (''+s).replace(re, '');
	if (s=='') return false;
	var si = '';
	for (var i=s.length-1; i>=0; i--)
		si += s.substr(i, 1);
	var sum = 0;
	var digits = '';
	for (var i=0; i<si.length; i++)
	{
		c = parseInt(si.substr(i, 1), 10);
		digits += '' + ( ( (i%2)!=0 )?(c * 2):(c) );
	}
	for (var i=0; i<digits.length; i++)
		sum += parseInt(digits.substr(i, 1), 10);
	return ((sum % 10 ) == 0);
}

function checkRE(s, re_s)
{
	eval('var re='+re_s+';');
	return re.test(s);
}

function getNumberFromString(s)
{
	s = '' + s;
	if (s == '') return s;
	var re = /^0*/;
	s = parseInt(s.replace(re, ''), 10);
	return s;
}

function testDate(str, check_time){
    var ty = 0; var tm = 0; var td = 0;
	var th = 0; var ti = 0; var ts = 0;    
	var xd = ''; var xt = ''; var xdt = '';
	var xdt = str.split(' '); 
	if (xdt.length>0) {
		var xd = xdt[0].split('-');
		if (xd.length>0) ty = xd[0]; 
		if (xd.length>1) tm = xd[1]; 
		if (xd.length>2) td = xd[2];
	}
    var months = new Array(); 
    months[1] = 31; 
    if (is_leap_year(ty)) months[2] = 29; else months[2] = 28; 
    months[3] = 31; months[4] = 30; months[5] = 31; months[6] = 30; months[7] = 31; months[8] = 31; months[9] = 30; months[10] = 31; months[11] = 30; months[12] = 31;
    
	ty = getNumberFromString(ty);
	tm = getNumberFromString(tm);
	td = getNumberFromString(td);
	
    if ( ty=='' || isNaN(ty) || (ty<1900)) return 'y'; 
    if ( tm=='' || isNaN(tm) || (tm<1) || (tm>12) ) return 'm';
    if ( td=='' || isNaN(td) || (td<1) || (td>months[parseInt(tm)]) ) return 'd';
    
    if (check_time != true) {

	    var re = /^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/;
	    if (!re.test(str)) return '_';

    	return '';
    }

	if (xdt.length>1) {
		var xt = xdt[1].split(':');
		if (xt.length>0) th = xt[0]; 
		if (xt.length>1) ti = xt[1]; 
		if (xt.length>2) ts = xt[2];
	}
	
	th = getNumberFromString(th);
	ti = getNumberFromString(ti);
	ts = getNumberFromString(ts);

    if ( th=='' || isNaN(th) || (th<0) || (th>23) ) return 'h';
    if ( ti=='' || isNaN(ti) || (ti<0) || (ti>59) ) return 'i';
    if ( ts=='' || isNaN(ts) || (ts<0) || (ts>59) ) return 's';

	var re = /^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/;
	if (!re.test(str)) return '_';

	return '';
}