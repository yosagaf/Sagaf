$( document ).ready(function() {

// Reorganize images
var sections = document.getElementsByClassName("reorganize");
if(sections){
	var length = sections.length;
	if(length%2 != 0){
		var section = sections[length - 1];
		var section_bis = section.cloneNode(true);
		var number_child = section.lastElementChild.childElementCount;
		for(var i = number_child; i > Math.round(number_child/2); i-- ){
			section.lastElementChild.removeChild(section.lastElementChild.lastElementChild);
			section_bis.lastElementChild.removeChild(section_bis.lastElementChild.firstElementChild);
		}
		insertAfter(section_bis, section);
	} else {
    var section = sections[length - 2];
		var section_bis = sections[length - 1];
    var number_child = section.lastElementChild.childElementCount;
    var number_child_bis = section_bis.lastElementChild.childElementCount;
    var j = 1;
		for(var i = number_child; i > Math.round(number_child/2); i-- ){
      if(j + number_child_bis <= 8){
        insertAfter(section.lastElementChild.lastElementChild.cloneNode(true), section_bis.lastElementChild);
        section.lastElementChild.removeChild(section.lastElementChild.lastElementChild);
      }
      j++;
		}
  }
}

function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

// JavaScript Document

$('#subscribeform').submit(function(){



	var action = $(this).attr('action');



		$("#mesaj").slideUp(750,function() {







		$('#mesaj').hide();



	$('#subsubmit')







			.after('')







			.attr('disabled','disabled');



	$.post(action, {







			email: $('#subemail').val()







		},







			function(data){







				document.getElementById('mesaj').innerHTML = data;







				$('#mesaj').slideDown('slow');







				$('#subscribeform img.subscribe-loader').fadeOut('slow',function(){$(this).remove()});







				$('#subsubmit').removeAttr('disabled');







				if(data.match('success') != null) $('#subscribeform').slideUp('slow');



			}







		);







		});







		return false;







	});

});