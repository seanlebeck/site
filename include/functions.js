// JavaScript Document

function CheckAdminReply(){
	var topic_name, topic_email, topic_title, topic_text, isOk = true;
	var message = "";
	
	topic_name	= document.form.topic_name.value;
	topic_email	= document.form.topic_email.value;
	topic_title = document.form.topic_title.value;
	topic_text	= document.form.topic_text.value;
	
	if (topic_name.length==0){
		message += "\n -  Name is missing";
		isOk=false;
	}
	if (topic_email.length==0){
		message += "\n -  Email is missing";
		isOk=false;
	}
	if (topic_title.length==0){
		message += "\n -  Title is missing";
		isOk=false;
	}
	if (topic_text.length==0){
		message += "\n -  Message is missing";
		isOk=false;
	}
	if (!isOk){
	   alert(message);
	}
	return isOk;
}

function searchdescr(svalue) {
	if(svalue == 'enter part of topic title') { document.form.search.value = ''; }
}

function searchdescr1(svalue) {
	if(svalue == 'enter poster Name or Email') { document.form.search.value = ''; }
}
