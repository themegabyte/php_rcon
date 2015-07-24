<?php
require ('init.inc.php');
header('Content-type: application/javascript; charset=UTF-8');
?>
var defrefreshinterval=<?php echo ((is_numeric($refresh_rate) && ($refresh_rate > 5))?$refresh_rate:'60'); ?>;
var selected_obj;
var waiting_for_data=0;
var queued_suggest=false;
var refreshinterval = defrefreshinterval;
var starttime;
var nowtime;
var reloadseconds=0;
var secondssinceloaded=0;
var stopping=false;
var result_is_error=false;



function iPostData(a, b, c, d)
	{
    var http_request = false;
    if ((a==null) || (a.length < 1))
        {return;}
    var request = "a="+escape(a);
    if ((b!=null) && (b.length > 0)) {request += "&b="+escape(b);
		if ((c!=null) && (c.length > 0)) {request += "&c="+escape(c);
			if ((d!=null) && (d.length > 0)) {request += "&d="+escape(d);
			}}}

	if (a=="cmd")
	    {
		var elem = document.getElementById("res");
		if (elem != null)
			elem.innerHTML = "";
		}

    waiting_for_data++;
    WorkingIndicator();

	// AJAX
    if (window.XMLHttpRequest) {
        http_request = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
          http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (eror) {
          http_request = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }

    http_request.onreadystatechange = function() { iIncomingData(http_request); };
    http_request.open("POST", "action.php?server=<?php echo $server_id; ?>", true);
    http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http_request.send(request);
   }

function EncloseToResultTable(content)
	{
	return '<table style="padding-bottom: 13px;"><tr class="even"><td style="text-align:center" width="<?php echo $colwidth.'">'.$lang['result']; ?>:<br>'
		+'<a href="#" class="rconbtn rb_x" onclick="DeleteResults(); return false" title="Hotkey: X">X</a></td><td>'
		+content;
		+'</td></tr></table>';
	}

function EncloseToSuggestTable(content)
	{
	var rval = '';
	var str = content.split("\n");
	for(i=0; i < str.length - 1; i++)
		{
		var o = str[i].indexOf(' ');
		s = str[i].substring(0,str[i].length-1);
		if (o < 0)
			{
			var n = s;
			var p = '';
			} else {
			var n = s.substring(0,o);
			var p = s.substring(o);
			}

		var suggest = '<table class="suggest_link">';
		suggest += '<tr><td onclick="javascript:SuggestClick(\''+n+'\');" style="text-align:left;" width="40%">' + n + '</td>';
		if (p.length > 0)
		    {
			suggest += '<td onclick="javascript:SuggestClick(\''+n+p+'\')" style="text-align:right;" style="font-size: xx-small;">' + p + '</td>';
		    }
		suggest += '</tr></table>';
		rval += suggest;
		}
	if (rval != '')
	    {
		rval = '<table class="suggest_link"><tr><td onclick="javascript:DeleteSuggests();" style="text-align:right;"><b>X</b></td></tr></table>'+rval;
		} else
		{
        DeleteSuggests();
		}

	$('#suggest').html(rval).attr('class','suggest');
    FixWindowResize();
	}

function iIncomingData(http_request)
	{
	if (http_request.readyState == 4) {
	    waiting_for_data--;
		WorkingIndicator();

	    var first = http_request.responseText.substring(0,1);
		switch (first)
		    {
		 	case "~":
	     	    {
				target = "plist";
				var res = http_request.responseText.substring(1);
				break;
				}
		 	case "=":
	     	    {
				target = "suggest";
				queued_suggest = false;
				var res = http_request.responseText.substring(1);
				EncloseToSuggestTable(res);
				return;
				}
			default:
			    {
				target = "res";
				result_is_error = ((http_request.status != 200) || (http_request.responseText.substring(0,4) == '<h2>'));
				var res = EncloseToResultTable(http_request.responseText);
				}
			}

		var elem = document.getElementById(target);
		if (elem != null)
			{
			if (http_request.status == 200)
				{
				if (result_is_error)
				    {DeleteResults();}

                elem.innerHTML = res;
				} else {

                elem.innerHTML = EncloseToResultTable("<h2>HTTP error "+http_request.status+": "+((http_request.status == 0)?"<?php echo $lang['connection_error']; ?>":http_request.statusText)+"</h2>"
					+' <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">List of HTTP status codes</a>');
				}
            ShowIcons(elem);
			}
		}
	}

function removec(s, t)
	{
	i = s.indexOf(t);
	r = "";
	if (i == -1) return s;
	r += s.substring(0,i) + removec(s.substring(i + t.length), t);
	return r;
	}

function StartTimer()
	{
	starttime=new Date();
	starttime=starttime.getTime();
	CountdownTick();
	}

function StartOrStopTimer()
	{
	if (stopping != true)
	    {stopping = true;}
		else
	        {
			stopping = false;
            refreshinterval = reloadseconds;
			StartTimer();
			}
	}

function CountdownTick()
	{
	if (stopping == true) return;
	nowtime= new Date();
	nowtime=nowtime.getTime();
	secondssinceloaded=(nowtime-starttime)/1000;
	reloadseconds=Math.round(refreshinterval-secondssinceloaded);
	if (refreshinterval>=secondssinceloaded) {
		var timer=setTimeout("CountdownTick()", 1000);
		var elem = document.getElementById("refreshinfo");
		if (elem != null)
			{elem.innerHTML = reloadseconds+" s";}
		}
	else {
	    RefreshNow();
		}
	}

function SwitchDiv(theDiv)
	{
	$('#'+theDiv).slideToggle(200);
	}

function HandleEnter()
	{
	$('#'+selected_obj).click();
	return false;
	}

function WorkingIndicator()
	{
	$('#working').html(((waiting_for_data > 0)?' &nbsp; &nbsp; &nbsp; &nbsp;<img src="graphics/ajax-loader.gif" alt="...">':''));
	}

function RefreshNow()
	{
	iPostData("plist","status","1");
	refreshinterval = defrefreshinterval;
	StartTimer();
	}

function CmdMsg(q,c,n,UserInfoObj)
	{
	StartOrStopTimer();
	text = prompt(q+ ((n!='')?" "+$(UserInfoObj).parents('tr').attr('username'):'') ,"");
	if (text)
		{
		text = removec(text,'"');
		c = c.replace(/%m/g, text);
		if (n!='')
			{c = c.replace(/%n/g, n);}
		var elem = document.getElementById('cmdbox');
        elem.value = c;
		SubmitCustomCmd(UserInfoObj);
		}
    StartOrStopTimer();
	}

function CustomCmd(n,UserInfoObj)
	{
	var elem = document.getElementById('cmdbox');
	elem.value = n;
	SubmitCustomCmd(UserInfoObj);
	}

function SubmitCustomCmd(UserInfoObj)
	{
	if (window.event != null)
		{var evtobj = window.event;}
		else if (window.e != null)
		{var evtobj = window.e;}
	if ((evtobj == null) || (! evtobj.shiftKey))
		{
		var elem = document.getElementById("cmdbox");
        col = (($('#colors:checked').length>0)?'1':'0');

		DeleteSuggests();
		if (UserInfoObj != null)
			{
			UserInfoObj = $(UserInfoObj).parents('tr');
			var usercc = UserInfoObj.attr('usercc');
			var extraLog = "Username=" + UserInfoObj.attr('username') + " IP=" + UserInfoObj.attr('userip')+((usercc!='')?' ('+usercc+')':'');
			} else {
			var extraLog = "";
			}
		iPostData("cmd", elem.value, col, extraLog);
		return false;
	    }
	}

function SubmitChangeMapOrGametype(what)
	{
	if (what == 0)
	    {
        CustomCmd("g_gametype");
		} else if ((what == 1) || (what == 2))
		{
		var s = $('select[name=gtype]').val();
		if (s != "")
		    {
			CustomCmd("g_gametype "+s);
			if (what == 2)
				{setTimeout('CustomCmd("map_restart")', 3000);}
			}
		} else if (what == 3)
		{
		var s = $('select[name=map]').val();
		if ((s != "") && (s != "restart"))
		    {CustomCmd("map "+s);}
		       else {CustomCmd("map_restart");}
		}
	}

function SubmitChangeWeapon(weap, what)
	{
	if (what == -1)
	    {
        CustomCmd(weap);
		} else if ((what == 0) || (what == 1))
		{
		CustomCmd(weap+" "+what);
		}
	}

function SubmitChangePass()
	{
    var s = $("#public_password").val();
    iPostData("cmd", 'g_password "'+s+'"', 1)
	}

function MapImgShow(friendly,what)
	{
	var show = (what!="");
	var mapimg = $("#mapimg");

	if (show)
		{
		mapimg.html('<h2>'+friendly+'</h2><div style="background-image: url(maps/'+what+'.jpg);"></div>');
		mapimg.css({opacity:0}).show().animate({opacity: 1}, 200);
		} else {
		mapimg.animate({opacity: 0}, 350,function(){$(this).hide();});
		}

	}

function SwitchMapVisibility()
	{
	if ($('#mapimg:visible').length>0)
		{
		MapImgShow('','');
		} else {
		$('#mapimgbtn').click();
		}
	}

function DeleteResults()
	{
	$('#res').html('');
	}

function DeleteSuggests()
	{
	$('#suggest').html('').attr('class','suggest_inv');
	}

function SuggestInit()
	{
	if (queued_suggest == true)
	    {return;}

	if (window.event != null)
		{var evtobj = window.event;}
		else if (window.e != null)
		{var evtobj = window.e;}

//     document.getElementById('cmdbox').value += ' ' + evtobj.keyCode;

	if ((evtobj != null) &&		// only a-z, 0-9, _, backspace, delete
	    (! (((evtobj.keyCode >= 65) && (evtobj.keyCode <= 90)) ||
	    ((evtobj.keyCode >= 48) && (evtobj.keyCode <= 57)) ||
		 (evtobj.keyCode ==  8) || (evtobj.keyCode == 45) || (evtobj.keyCode == 46) ) ) )
	    return;

    var str = document.getElementById('cmdbox').value;

    if ((str.length > 0) && (str.indexOf(' ') < 0))
	    {
	    iPostData("suggest", escape(str));
	    queued_suggest = true;
		} else {
        DeleteSuggests();
		}

	}

function SuggestClick(val)
	{
	$('#cmdbox').val(val);
	DeleteSuggests();
	}

function ShowIcons(parent)
	{
	<?php
	if (! $disable_icons)
		{
	?>
	$(parent).find('.rconbtn').each(function(){
		var t = $(this);
		if (t.hasClass('img'))
			{return;}
		var title = t.attr('title');
		t.attr('title',t.text()+((title!='')?' ('+title+')':'')).addClass('img');
		if (! t.hasClass('ro_text'))
			{t.text('');}
		})
	<?php
		}
	?>
	}

function ShowHideLog()
	{
	var l = $('#log:first');
	if ($('#log:visible').length==0)
		{
		// load and show the log
		$.get("log.php?ajax=1",function(h){
			l.html(h).slideDown(200).animate({scrollTop: l.find('pre:first').outerHeight()},200);
			});
		} else {
		l.slideUp(200, function(){
            $(this).html('');
			});
		}
	}

function ShowHideScreenshots()
	{
	var l = $('#screenshots:first');
	if ($('#screenshots:visible').length==0)
		{
		$.get("screenshots.php?server=<?php echo $server_id; ?>&ajax=1",function(h){
			l.html(h).slideDown(200).animate({scrollTop: l.find('table:first').outerHeight()},200);
			});
		} else {
		l.slideUp(200, function(){
            $(this).html('');
			$('#screenshot_img').html('');
			});
		}
	}

function ShowScreenshot(fn)
	{
	$('#screenshot_img').html('<img src="screenshots.php?server=<?php echo $server_id; ?>&img='+fn+'&ac='+(Math.random())+'" alt="'+fn+'" onclick="$(this).remove();" />');
	return false;
	}

function FixWindowResize()
	{
	$("#phprcon .suggest").css('width',$("#phprcon #cmdbox").outerWidth()+"px");
	}

// Hotkeys
$.hotkeys.add('g',{type: 'keydown', propagate: false, disableInInput: true}, function(){SubmitChangeMapOrGametype(0);});
$.hotkeys.add('m',{type: 'keydown', propagate: false, disableInInput: true}, function(){SwitchMapVisibility();});
$.hotkeys.add('r',{type: 'keydown', propagate: false, disableInInput: true}, function(){RefreshNow();});
$.hotkeys.add('s',{type: 'keydown', propagate: false, disableInInput: true}, function(){StartOrStopTimer();});
$.hotkeys.add('x',{type: 'keydown', propagate: false, disableInInput: true}, function(){DeleteResults();});


// init on DOM ready
$(function() {
	ShowIcons(document);
	RefreshNow();
	$(window).resize(function(){
		FixWindowResize();
		});
	FixWindowResize();
	});
