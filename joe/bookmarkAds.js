/*returnes the whole value of the specified cookie name
*/
function getTagValue(nametag){
	var allcookies = document.cookie;
  cookiearray  = allcookies.split(';');
  for(var i=0; i<cookiearray.length; i++){
     name = cookiearray[i].split('=')[0];
     value = cookiearray[i].split('=')[1];
     name = name.split(' ').join('');//remove spaces
     if(name == nametag){
     	return value;
     }
  }
  return "";
}

/*returnes true if the adid is existing in the cookie
*/
function isExistAdCookie(nametag,valuetag,delimit)
{
  var valueexists = false;
  var value = getTagValue(nametag);
  valuearray = value.split(delimit);
  for(var i=0; i<valuearray.length; i++){
  	if(valuearray[i] == valuetag){
  		valueexists = true;
  	}
  }
  return valueexists;
}

/*when the mouse is moving over the ad
*/
function setHover(adid){
	var idelement = 'bookmarkad'+adid;
	var curState = document.getElementById(idelement).style.backgroundPosition;
	if( curState == "" || curState == "0px 0px" || curState == "0px -20px" ){//not saved
		document.getElementById(idelement).style.backgroundPosition = "0px -40px";
		document.getElementById('bookmarkadspan'+adid).innerHTML = "Save";
	}else if( curState == "0px -60px" ){//saved
		document.getElementById(idelement).style.backgroundPosition = "0px -80px";
		document.getElementById('bookmarkadspan'+adid).innerHTML = "Remove";
	}
}

/*when the mouse is moving out of the ad
*/
function setHout(adid){
	var idelement = 'bookmarkad'+adid;
	var curState = document.getElementById(idelement).style.backgroundPosition;
	if( curState == "0px -40px" || curState == "0px -20px" ){//not saved
		document.getElementById(idelement).style.backgroundPosition = "0px 0px";
		document.getElementById('bookmarkadspan'+adid).innerHTML = "";
	}else if( curState == "0px -80px" || curState == "0px -60px" ){//saved
		document.getElementById(idelement).style.backgroundPosition = "0px -60px";
		document.getElementById('bookmarkadspan'+adid).innerHTML = "";
	}
}

/*gets the value of the control - the adid
* idel - bookmarkad234 or bookmarkad1 ...
*/
function getValueFromId(idel){
	var atpos = idel.length;
	if (atpos > -1) {
		var adid = idel.substring(10, atpos);
		return adid;
	}
	return "";
}

/*On loading the page sets checked/unchecked the existing ads for latest.inc.php
*/
function setCheckedSelectedBookmarksLatestAds(){
	if( typeof(document.frmLatestAds) != "undefined" ){
		if( typeof(document.frmLatestAds.bookmarkad) != "undefined" ){
			var adscount = document.frmLatestAds.bookmarkad.length;
			for (i=0; i<adscount; i++){
				if(isExistAdCookie('bookmark', getValueFromId(document.frmLatestAds.bookmarkad[i].id), '.')){
					document.frmLatestAds.bookmarkad[i].checked = true;
					document.getElementById(document.frmLatestAds.bookmarkad[i].id).style.backgroundPosition = "0px -60px";
				}
			}
		}
	}
	totalAdCookies();
}

/*On loading the page sets checked/unchecked the existing ads for ads.php
*/
function setCheckedSelectedBookmarksAds(){
	if(typeof(document.getElementsByName('bookmarkad')) != "undefined"){
		var adscount = document.getElementsByName('bookmarkad').length;
		for (i=0; i<adscount; i++){
			if(isExistAdCookie('bookmark', getValueFromId(document.getElementsByName('bookmarkad')[i].id), '.')){
				document.getElementsByName('bookmarkad')[i].checked = true;
				document.getElementById(document.getElementsByName('bookmarkad')[i].id).style.backgroundPosition = "0px -60px";
			}
		}
	}
	totalAdCookies();
}

/*On loading the page sets checked/unchecked the existing ads for bookmarkAds.php
*/
function setCheckedSelectedBookmarksTotal(){
	if( typeof(document.frmBookmarks.bookmarkad)=="undefined" || typeof(document.frmBookmarks.bookmarkad.length)=="undefined" ){//if there only one ad row => the value is undefined
		if( typeof(document.frmBookmarks.bookmarkad)!="undefined" && typeof(document.frmBookmarks.bookmarkad.id) != "undefined" ){
			if(isExistAdCookie('bookmark', getValueFromId(document.frmBookmarks.bookmarkad.id), '.')){
				document.frmBookmarks.bookmarkad.checked = true;
				document.getElementById(document.frmBookmarks.bookmarkad.id).style.backgroundPosition = "0px -60px";
			}
		}
		totalAdCookies();
		return;
	}
	var adscount = document.frmBookmarks.bookmarkad.length;
	for (i=0; i<adscount; i++){
		if(isExistAdCookie('bookmark', getValueFromId(document.frmBookmarks.bookmarkad[i].id), '.')){
			document.frmBookmarks.bookmarkad[i].checked = true;
			document.getElementById(document.frmBookmarks.bookmarkad[i].id).style.backgroundPosition = "0px -60px";
		}
	}
	totalAdCookies();
}

/*Adds a new advert or removes it if it is already there
*/
function writeCookie(nametag,valuetag,delimit)
{
	var valueexists = false;
	var value = getTagValue(nametag);
	wholenametag = "";
	
	valuearray = value.split(delimit);
  for(var j=0; j<valuearray.length; j++){
   if(valuearray[j] == valuetag){
    valueexists = true;
   }else{
    if(valuearray[j].length != 0){
     	wholenametag += valuearray[j] + delimit;
     }
   }
  }
  var ExpireDate = new Date ();
  var expiredays = 1;
  ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));
  var idelement = 'bookmarkad' + valuetag;
  if( valueexists ){
   document.cookie=nametag + "=" + wholenametag.substring(0, (wholenametag.length-1))+delimit + "; expires="+ExpireDate.toGMTString() + "; path=/; ";
   document.getElementById(idelement).style.backgroundPosition = "0px -20px";
   document.getElementById('bookmarkadspan'+valuetag).innerHTML = "Removed";
  }else{
  	document.cookie=nametag + "=" + wholenametag + valuetag +"; expires="+ExpireDate.toGMTString() + "; path=/; ";
  	document.getElementById(idelement).style.backgroundPosition = "0px -60px";
  	document.getElementById('bookmarkadspan'+valuetag).innerHTML = "Saved";
  }
  totalAdCookie(nametag,delimit);
}   

function totalAdCookie(nametag,delimit){
  var totalcookies = 0;
  var value = getTagValue(nametag);
  valuearray = value.split(delimit);
  for(var i=0; i<valuearray.length; i++){
  	if(valuearray[i].length != 0){
  		totalcookies++;
  	}
  }
  document.getElementById('totalbookmarks').innerHTML = totalcookies;
  //alert(document.cookie);
}

/*Counts total ads for all the ads
*/
function totalAdCookies(){
	totalAdCookie('bookmark','.');
}


