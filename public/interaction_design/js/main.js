var panel_1_minwidth	= 300;
var panel_1_minheight	= 200;
// var panel_x_minwidth	= 200;
// var panel_x_minheight = 100;
var resizehandle_width	= 5;
var resizehandle_height	= 5;

window.addEvent('domready', function() {
    updateLayout();

    // Fix voor IE om wrapping te stoppen van labels van formulierelementen
	if(window.ie) {
	    document.getElements('form.fullwidth th').each(function(element){
	    	var label = element.getText();
	    	label = label.replace(/ /g, '&nbsp;');
	    	label = label.replace(/-/g, '&nbsp;');
	    	element.setHTML(label);
	    });
	}
  
});


window.addEvent('resize', function() {
    
	updateLayout();

    var el = $('modalbox');
    if(el.getStyle('display') != 'none') {
        updateModalbox();
    }
});

function toggleFullmode() {

	if($('breadcrumb').getStyle('display') != 'none') {
		$('breadcrumb').setStyle('display', 'none');
		$('login_data').setStyle('display', 'none');
		$('tabs').setStyle('display', 'none');
		$('header').setStyle('top', '-50px');
		$('content').setStyle('top', '70px');
	} else {
		$('breadcrumb').setStyle('display', '');
		$('login_data').setStyle('display', '');
		$('tabs').setStyle('display', '');
		$('header').setStyle('top', '0px');
		$('content').setStyle('top', '120px');
	}

}

function setLayout(newLayout) {
	
	layout = newLayout;
	// alert('Setting layout: ' + layout);
	updateLayout();
	
}


function updateLayout() {
	
	var totalheight = $('content').getStyle('height').toInt();
    var totalwidth = $('content').getStyle('width').toInt();

    switch(layout) {
    
    	case 'a':
        	
    		$('panel_1').setStyles({
        		top: 0,
        		left: 0,
        		width: '100%',
        		height: '100%'
    		});
        	$('panel_2').setStyle('display', 'none');
        	$('panel_3').setStyle('display', 'none');
            $('panel_4').setStyle('display', 'none');
            $('panel_5').setStyle('display', 'none');

            break;

        case 'b':

        	$('panel_1').setStyles({
        		top: 0,
        		left: 0,
        		width: panel_1_minwidth,
        		height: '100%'
    		});
        	$('panel_2').setStyles({
        		top: 0,
        		left: panel_1_minwidth + resizehandle_width,
        		right: 0,
        		height: '100%'
    		});
        	$('panel_3').setStyle('display', 'none');
            $('panel_4').setStyle('display', 'none');
            $('panel_5').setStyle('display', 'none');
            $('resizehandle_1').setStyles({
        		top: 0,
        		left: panel_1_minwidth,
        		width: resizehandle_width,
        		height: '100%',
        		cursor: 'e-resize'
    		});

            break;


        case 'e':

        	$('panel_1').setStyles({
        		top: 0,
        		left: 0,
        		width: panel_1_minwidth,
        		height: panel_1_minheight
    		});
        	$('panel_2').setStyles({
        		top: panel_1_minheight + resizehandle_height,
        		left: 0,
        		width: panel_1_minwidth,
        		bottom: 0
    		});
        	$('panel_3').setStyles({
        		top: 0,
        		left: panel_1_minwidth + resizehandle_width,
        		width: totalwidth - panel_1_minwidth - resizehandle_width - panel_1_minwidth - resizehandle_width,
        		height: '100%',
        		// backgroundColor: '#ccffcc'
    		});
        	
        	$('panel_4').setStyles({
        		top: 0,
        		left: totalwidth - panel_1_minwidth,
        		width: panel_1_minwidth,
        		height: '100%'
    		});
        	$('panel_5').setStyle('display', 'none');
        	$('resizehandle_1').setStyles({
        		top: panel_1_minheight,
        		left: 0,
        		height: resizehandle_height,
        		width: panel_1_minwidth,
        		// cursor: 's-resize'
    		});
        	$('resizehandle_2').setStyles({
        		top: 0,
        		left: panel_1_minwidth,
        		width: resizehandle_width,
        		height: '100%',
        		// cursor: 'e-resize'
    		});
        	$('resizehandle_3').setStyles({
        		top: 0,
        		left: totalwidth - panel_1_minwidth - resizehandle_width,
        		width: resizehandle_width,
        		height: '100%',
        		// cursor: 'e-resize'
    		});

    }
    
    // makePanelsResizable();





    


}

/*
function makePanelsResizable() {
	
	/// First remove old events!!!!!!!!!! Lijkt niet te werken!
	///   $('panel_1').removeEvents();
	///   $('panel_2').removeEvents();
	///   $('panel_3').removeEvents();
	///   $('resizehandle_1').removeEvents();
	///   $('resizehandle_2').removeEvents();
	
	// alert('Make panels resizable (layout ' + layout + ')');

	var totalheight = $('content').getStyle('height').toInt();
    var totalwidth = $('content').getStyle('width').toInt();
	
    switch(layout) {
	    
	   	case 'a':
	   		
	   		// Nothing to resize!
	   		break;
	
	   	case 'b':

			$('resizehandle_1').makeResizable({
			    modifiers: {x: 'left', y: false},
			    limit: {x: [panel_1_minwidth, totalwidth - panel_x_minwidth - resizehandle_width]}
			});  
			$('panel_1').makeResizable({
			    handle: $('resizehandle_1'),
			    modifiers: {x: 'width', y: false},
			    limit: {x: [panel_1_minwidth, totalwidth - panel_x_minwidth - resizehandle_width]}
			});
			$('panel_2').makeResizable({
			    handle: $('resizehandle_1'),
			    modifiers: {x: 'left', y: false},
			    limit: {x: [panel_1_minwidth + resizehandle_width, totalwidth - panel_x_minwidth]}
			});
			
			break;
	
	
	   	case 'e':
	   		$('resizehandle_1').makeResizable({
               modifiers: {x: false, y: 'top'},
               limit: {y: [panel_1_minheight, totalheight - resizehandle_height]},
               onComplete: function(){
               	// makePanelsResizable();
               	// alert('UPDATE lAYOUT!');
               }
           });

	   }

}
*/

function goTo(n1, n2, n3, sub) {
    
    if(!n1) n1 = '';
    if(!n2) n2 = '';
    if(!n3) n3 = '';
    if(!sub) sub = '';
    document.location = '?n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
    return false;
    
}


function setContent(prefix, key) {
    
    var containerId = 'content_' + prefix;
    currentId = containerId + '_' + key;
    $(containerId).getElements('div[id^=' + containerId + '_]').each(function (div, i) {
        if(div.id == currentId) {
            div.setStyle('display', 'block');
        } else {
            div.setStyle('display', 'none');
        }
    });
    saveToSession('content', prefix, key);

}


function setPaneltab(prefix, key) {
    
    var containerId = 'paneltab_' + prefix;
    currentTabId = containerId + '_tab_' + key;
    $(containerId).getElements('li[id^=' + containerId + '_tab_]').each(function (li, i) {
        if(li.id == currentTabId) {
            li.addClass('huidig');
        } else {
            li.removeClass('huidig');
        }
    });
    currentContentId = containerId + '_content_' + key;
    $(containerId).getElements('div[id^=' + containerId + '_content_]').each(function (div, i) {
        if(div.id == currentContentId) {
            div.setStyle('display', 'block');
        } else {
            div.setStyle('display', 'none');
        }
    });
    saveToSession('paneltab', prefix, key);

}


function setGridmodus(prefix, key) {

    var containerId = 'grid_' + prefix;
    currentGridId = containerId + '_' + key;
    $(containerId).getElements('div[id^=' + containerId + '_]').each(function (div, i) {
        if(div.id == currentGridId) {
            // alert(currentGridId + ' ::: ' + div.id + ' ZELFDE');
            div.setStyle('display', 'block');
        } else {
            // alert(currentGridId + ' ::: ' + div.id + ' NIET ZELFDE');
            div.setStyle('display', 'none');
        }
    });
    saveToSession('grid', prefix, key);

}











function initAdvancedFilter(prefix) {
	
	var formElement		= $('filter_' + prefix + '_advanced');
	var tbodyElements	= formElement.getElements('tbody.set');
	
	if(tbodyElements.length <= 1) {
		addFilterSet(prefix);
	}

}


function toggleFilterMode(prefix) {

	var formElementSimple	= $('filter_' + prefix + '_simple');
	var formElementAdvanced	= $('filter_' + prefix + '_advanced');
	
	var newMode = (formElementAdvanced.getStyle('display') == 'none') ? 'advanced' : 'simple';
	if(newMode == 'advanced') {
		formElementSimple.setStyle('display', 'none');
		formElementAdvanced.setStyle('display', '');
	} else {
		formElementSimple.setStyle('display', '');
		formElementAdvanced.setStyle('display', 'none');
	}
	saveToSession('filter', prefix, newMode);

}


function applySimpleFilter(prefix) {
	
	var formElement		= $('filter_' + prefix + '_simple');
	var inputElement	= formElement.getElement('input.filter_waarde');
	var aElement		= formElement.getElement('a.filter_verwijderen');

	if(inputElement.getProperty('value') == '') {
		alert('Specificeer een waarde waarop u wilt filteren.');
	} else {
		aElement.removeClass('disabled');
		aElement.getFirst().setProperty('src', 'images/icons/verwijder_clickable.gif');
		aElement.removeEvents('click');
		aElement.addEvent('click', function() {
			removeSimpleFilter(prefix);
	    });
	}
	inputElement.blur();
	
}


function removeSimpleFilter(prefix) {
	
	var formElement		= $('filter_' + prefix + '_simple');
	var inputElement	= formElement.getElement('input.filter_waarde');
	var aElement		= formElement.getElement('a.filter_verwijderen');
	
	aElement.addClass('disabled');
	aElement.getFirst().setProperty('src', 'images/icons/verwijder.gif');
	aElement.removeEvents('click');
	
	inputElement.value = '';
	inputElement.focus();

}


function openFilter(prefix) {
	
	openModalbox('?n1=collectiebeheer&n2=verzamelingen&sub=modalbox_filteropenen&prefix=' + prefix, 'single');
			
}


function applyOpenedFilter(prefix) {

	closeModalbox();
	
}


function saveFilter(prefix) {

	openModalbox('?n1=collectiebeheer&n2=verzamelingen&sub=modalbox_filteropslaan', 'single');
	
}


function addFilterSet(prefix) {

	var formElement			= $('filter_' + prefix + '_advanced');
	var tbodyElements		= formElement.getElements('tbody.set');
	var tbodyElementCloned	= tbodyElements[0].clone();
	var tableElement		= tbodyElements[0].getParent();
	var newSetNumber		= tbodyElements.length;

	// alert(tbodyElements.length);
	if(tbodyElements.length > 1) {
		var rowElement = tbodyElementCloned.getElement('tr.set_mode');
		rowElement.setStyle('display', '');
	}
	
	tbodyElementCloned.getElement('th').setText('Set ' + newSetNumber);
	
	tbodyElementCloned.setStyle('display', '');
	// console.log(tableElement);
	tbodyElementCloned.injectInside(tableElement);

}


function removeFilterSet(prefix, aElement) {

	var aElement = new Element(aElement);
	var formElement		= $('filter_' + prefix + '_advanced');
	var tbodyElement	= aElement.getParent().getParent().getParent();
	
	tbodyElement.remove();

	// console.log(tbodyElement);


}



function addFilterRule(prefix, aElement) {
	
//	var tbodyElements		= formElement.getElements('tbody.set');
//	var tbodyElementCloned	= tbodyElements[0].clone();
	
	var aElement = new Element(aElement);
	var rowElement			= aElement.getParent().getParent();
	var rowElementCloned	= rowElement.clone();
	var selectElement		= aElement.getParent().getParent().getParent().getElements('select')[1];

	rowElementCloned.getElement('td').setText(selectElement.value);
	rowElementCloned.injectAfter(rowElement);
	
}


function removeFilterRule(prefix, aElement) {

	var aElement = new Element(aElement);
	var rowElement	= aElement.getParent().getParent();
	rowElement.remove();

}


function updateFilterCombineMethod(prefix, selectElement) {
	
	selectElement = new Element(selectElement);

	var rowElements = selectElement.getParent().getParent().getParent().getElements('tr.set_rule');
	rowElements.each(function(rowElement, index) {
		if(index == 0) {
			var text = '';
		} else {
			var text = selectElement.value;
		}
		rowElement.getElement('td').setText(text);
   });
	
	
	
}



function updateFilterRuleType(prefix, selectElement) {
	
	selectElement = new Element(selectElement);
	
	var tdElementCondition	= selectElement.getParent().getNext();
	var tdElementValue		= selectElement.getParent().getNext().getNext();
	
	switch(selectElement.value) {
	
		case 'veld2':
			tdElementCondition.getElements('select')[0].setStyle('display', 'none');
			tdElementCondition.getElements('select')[1].setStyle('display', '');
			tdElementValue.getElements('input')[0].setStyle('display', 'none');
			tdElementValue.getElements('input')[1].setStyle('display', '');
			break;
			
		default:
			tdElementCondition.getElements('select')[1].setStyle('display', 'none');
			tdElementCondition.getElements('select')[0].setStyle('display', '');
			tdElementValue.getElements('input')[1].setStyle('display', 'none');
			tdElementValue.getElements('input')[0].setStyle('display', '');

	}

	
	
	

}


























/*


function applyFilter(prefix) {
	
	var formElement = $('filter_' + prefix);

	if(!filterSpecified(prefix)) {
		alert('Specificeer een waarde waarop u wilt filteren of voeg een filterregel toe.');
		var inputElement = formElement.getElement('input');
		inputElement.focus();
	} else {
		enableFilterActions(prefix);
	}

}


function filterSpecified(prefix) {
	
	var formElement		= $('filter_' + prefix);
	var tbodyElement	= formElement.getElement('tbody');
	var inputElement	= tbodyElement.getElement('input'); 
	var rowElements		= tbodyElement.getChildren();

	return (inputElement.getProperty('value') != '' || rowElements.length > 2);

}


function enableFilterActions(prefix) {
	
	var formElement = $('filter_' + prefix);
	
	var aElement = formElement.getElement('a.filter_verwijderen');
	aElement.removeClass('disabled');
	aElement.getFirst().setProperty('src', 'images/icons/verwijder_clickable.gif');
	aElement.removeEvents('click');
	aElement.addEvent('click', function() {
    	removeFilter(prefix);
    });

	var aElement = formElement.getElement('a.filter_opslaan');
	aElement.removeClass('disabled');
	aElement.getFirst().setProperty('src', 'images/icons/opslaan_clickable.gif');
	aElement.removeEvents('click');
	aElement.addEvent('click', function() {
		saveFilter(prefix);
    });

}


function disableFilterActions(prefix) {
	
	var formElement = $('filter_' + prefix);
	
	var aElement = formElement.getElement('a.filter_verwijderen');
	aElement.addClass('disabled');
	aElement.getFirst().setProperty('src', 'images/icons/verwijder.gif');
	aElement.removeEvents('click');
	
	var aElement = formElement.getElement('a.filter_opslaan');
	aElement.addClass('disabled');
	aElement.getFirst().setProperty('src', 'images/icons/opslaan.gif');
	aElement.removeEvents('click');

}


function removeFilter(prefix) {
	
	var formElement		= $('filter_' + prefix);
	var tbodyElement	= formElement.getElement('tbody');
	var inputElement	= formElement.getElement('input');
	var tdElement		= formElement.getElement('td.rule_combine');
	
	disableFilterActions(prefix);

	var rowElements = tbodyElement.getChildren();
	rowElements.each(function(rowElement, index) {
         if(index > 1) {
        	 rowElement.remove();
         }
    });

	tdElement.setStyle('display', 'none');
	
	inputElement.setProperty('value', '');
	inputElement.focus();
	
}


function removeFilterRule(prefix, aElement) {

    var aElement = new Element(aElement);
    var trElement = aElement.getParent().getParent();
    trElement.remove();
	
	var formElement		= $('filter_' + prefix);
	var tbodyElement	= formElement.getElement('tbody');
	var rowElements		= tbodyElement.getChildren();
	if(rowElements.length < 3) {
		var tdElement = formElement.getElement('td.rule_combine');
		tdElement.setStyle('display', 'none');
		if(!filterSpecified(prefix)) {
			disableFilterActions(prefix);
			var inputElement = tbodyElement.getElement('input');
			inputElement.focus();
		}
	}
    
}


function addFilterRule(prefix, value, field, condition) {

	var formElement			= $('filter_' + prefix);
	var tbodyElement		= formElement.getElement('tbody');
	var rowElementToClone	= tbodyElement.getChildren()[1];
	var rowElementCloned	= rowElementToClone.clone();
	var inputElement		= rowElementCloned.getElement('input');
	
	rowElementCloned.injectInside(tbodyElement);

	if(arguments.length == 4) { // Fill rule with sample data, do not make visible yet!
		inputElement.setProperty('value', value);
		var selectElements = rowElementCloned.getElements('select');
		selectElements[0].selectedIndex = field;
		selectElements[1].selectedIndex = condition;
	} else { // Make visible and set focus to the first input!
		rowElementCloned.setStyle('display', '');
		enableFilterActions(prefix);
		inputElement.focus();
	}
	
	var tdElement = formElement.getElement('td.rule_combine');
	tdElement.setStyle('display', '');

}


function openFilter(prefix) {
	
	openModalbox('?n1=collectiebeheer&n2=verzamelingen&sub=modalbox_filteropenen&prefix=' + prefix, 'single');
			
}


function applyOpenedFilter(prefix) {

	removeFilter(prefix);
	
	addFilterRule(prefix, 'controle', 1, 0);
	addFilterRule(prefix, 'intern', 2, 1);
	addFilterRule(prefix, '1.0', 2, 2);
	addFilterRule(prefix, 'tijdelijk', 3, 1);

	var formElement = $('filter_' + prefix);
	var tbodyElement = formElement.getElement('tbody');
	
	var inputElement = tbodyElement.getElement('input');
	inputElement.setProperty('value', 'kerk');
	
	var inputElement = $(prefix + '_voorwaarde_en');
	inputElement.setProperty('checked', 'checked');
	
	var rowElements = tbodyElement.getChildren();
	rowElements.each(function(rowElement, index) {
         if(index > 1) {
        	 rowElement.setStyle('display', '');
         }
    });
	
	enableFilterActions(prefix);

	closeModalbox();
	
}




*/








   
function toggleTree(el) {

    var img = new Element(el);
    var li = img.getParent();
    if(!li.hasClass('expanded')) {
        li.removeClass('collapsed');
        li.addClass('expanded');
        img.setProperty('src', 'images/icons/inklappen_clickable.gif');
        saveToSession('tree', li.id, 'expanded');
    } else {
        li.removeClass('expanded');
        li.addClass('collapsed');
        img.setProperty('src', 'images/icons/uitklappen_clickable.gif');
        saveToSession('tree', li.id, 'collapsed');
    }
    
}

function toggleAudittrail(el) {

    var a = new Element(el);
    var img = a.getFirst();
    //alert( a.getParent().getParent().getParent().getStyle('display'));
    var tbody = a.getParent().getParent().getParent().getNext();
    if(tbody.getStyle('display') == 'none') {
        //li.removeClass('collapsed');
        //li.addClass('expanded');
        tbody.setStyle('display', '')
        img.setProperty('src', 'images/icons/inrollen_clickable.gif');
        saveToSession('audittrail', '1', 'table-row-group');
    } else {
        //li.removeClass('expanded');
        //li.addClass('collapsed');
        tbody.setStyle('display', 'none')
        img.setProperty('src', 'images/icons/uitrollen_clickable.gif');
        saveToSession('audittrail', '1', 'none');
    }
    
}

function saveToSession(type, key, value) {

    // Note: 'page' is a global variable, which is used as a unique id for the current page.
    // When a page is loaded into the modal box, 'page' is set to that page.  
    var o = {type : type, page : page, key : key, value : value};
    var url = 'savetosession.php?' + Object.toQueryString(o);
    var myAjax = new Ajax(url, { method: 'post'}).request();
    
    return;

}


function loadInModalBox(url, size) {
	
	// size can be 'single', 'double', 'triple' or 'fill'
	// if the size is different than the current size: empty and resize (morph) first!
	if(typeof(size) == 'undefined') {
		
		
	    // Set global 'page'-variable.
	    var n1 = $get('n1', url);
	    var n2 = $get('n2', url);
	    var n3 = $get('n3', url);
	    var sub = $get('sub', url);
	    var pageModal = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
	    page = escape(pageModal); // Required to make a valid name
	    
	    // Load content, then upadte position and size
	    new Ajax(url, {
	        method: 'get',
	        update: $('modalbox')
	    }).request();
		
	} else {
		
		if(size == modalboxSize) {

		    // Set global 'page'-variable.
		    var n1 = $get('n1', url);
		    var n2 = $get('n2', url);
		    var n3 = $get('n3', url);
		    var sub = $get('sub', url);
		    var pageModal = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
		    page = escape(pageModal); // Required to make a valid name
		    
		    // Load content, then upadte position and size
		    new Ajax(url, {
		        method: 'get',
		        update: $('modalbox')
		    }).request();
			
		} else {
			
			$('modalbox').empty();
			modalboxSize = size;
		    switch(modalboxSize) {
		    
			    case 'fill':
			        var totalMarginX = 350;
			        var totalMarginY = 150;
			        var marginX      = totalMarginX / 2;
			        var marginY      = totalMarginY / 2;
			        var left	     = marginX + window.getScrollLeft();
			        var top		     = marginY + window.getScrollTop();
			        var width	     = window.getWidth() - totalMarginX;
			        var height	     = window.getHeight() - totalMarginY;
			        break;
			        
			    case 'single':
			    case 'double':
			    case 'triple':
			    	switch(modalboxSize) {
			    		case 'single':
			    			var width = 600;
			    			break;
			    		case 'double':
			    			var width = 800;
			    			break;
			    		case 'triple':
			    			var width = 1000;
			    			break;
			    	}
			        var totalMarginY = 150;
			        var marginY      = totalMarginY / 2;
			        var left	     = window.getWidth()/2 - width/2;
			        var top		     = marginY + window.getScrollTop();
			        var width	     = width;
			        var height	     = window.getHeight() - totalMarginY;
			        break;
		        
		    }
		    
			var myEffects = new Fx.Styles($('modalbox'), {
				duration: 500,
				transition: Fx.Transitions.Quad.easeInOut,
		        onComplete: function() {
				
				
				    // Set global 'page'-variable.
				    var n1 = $get('n1', url);
				    var n2 = $get('n2', url);
				    var n3 = $get('n3', url);
				    var sub = $get('sub', url);
				    var pageModal = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
				    page = escape(pageModal); // Required to make a valid name
				    
				    // Load content, then upadte position and size
				    new Ajax(url, {
				        method: 'get',
				        update: $('modalbox')
				    }).request();
				
				
				
		        } 
			});
			myEffects.start({
			    'left': left,
			    'top': top,
			    'width': width,
			    'height': height
			});
		    
		    
		}
	}
	
	

	
}




function openModalbox(url, size) {

	// size can be 'single', 'double', 'triple' or 'fill'
	if(typeof(size) == 'undefined') {
		modalboxSize = 'fill';
	} else {
		modalboxSize = size;
	}
	// console.log('size = ' + size);
	
    // Set global 'page'-variable.
    var n1 = $get('n1', url);
    var n2 = $get('n2', url);
    var n3 = $get('n3', url);
    var sub = $get('sub', url);
    var pageModal = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
    page = escape(pageModal); // Required to make a valid name
    
    

    // Load content, then update position and size
    new Ajax(url, {
        method: 'get',
        update: $('modalbox'),
        evalScripts: true,
        onComplete: function() {
            updateModalbox();
        }
    }).request();

}


function updateModalbox() {

    var el = $('modalbox_overlay');
    el.setStyle('height', window.getScrollHeight())
    el.setStyle('display', 'block');
    
    var el = $('modalbox');
    switch(modalboxSize) {
    
    	case 'fill':
	        var totalMarginX = 350;
	        var totalMarginY = 150;
	        var marginX = totalMarginX / 2;
	        var marginY = totalMarginY / 2;
	        el.setStyle('left', marginX + window.getScrollLeft());
	        el.setStyle('top', marginY + window.getScrollTop());
	        el.setStyle('width', window.getWidth() - totalMarginX);
	        el.setStyle('height', window.getHeight() - totalMarginY);
	        break;
	        
	    case 'single':
	    case 'double':
	    case 'triple':
	    	switch(modalboxSize) {
    		case 'single':
    			var width = 600;
    			break;
    		case 'double':
    			var width = 800;
    			break;
    		case 'triple':
    			var width = 1000;
    			break;
	    	}
	        var totalMarginY = 150;
	        var marginY      = totalMarginY / 2;
	        el.setStyle('left', window.getWidth()/2 - width/2);
	        el.setStyle('top', marginY + window.getScrollTop());
	        el.setStyle('width', width);
	        el.setStyle('height', window.getHeight() - totalMarginY);
	        break;

    }
    el.setStyle('display', 'block');

}


function closeModalbox() {

    // Set global 'page'-variable.
    var n1 = $get('n1');
    var n2 = $get('n2');
    var n3 = $get('n3');
    var sub = $get('sub');
    var pageMain = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
    page = escape(pageMain); // Required to make a valid name

    var el = $('modalbox');
    el.setStyle('display', 'none');
    var el = $('modalbox_overlay');
    el.setStyle('display', 'none');

}

function showSpinner() {

	$('spinner').setStyle('display', 'block');
	
}


function hideSpinner() {
	
	$('spinner').setStyle('display', 'none');
	
}

	

function inArray(needle, haystack) {

	var key;
	for (key in haystack) {
		if (haystack[key] == needle) {
			return true;
		}
	}
    return false;

}

function showMessage(title, body) {

	
	
	$('message').empty();

	var html = '';
	if(title) {
		html += '<h2>' + title + '</h2>';
	}
	if(body) {
		html += body;	
	}
	$('message').setHTML(html);

	
	var slideIn = new Fx.Style($('message'), 'right', {
		duration: 750,
		transition: Fx.Transitions.Quad.easeInOut,
        onComplete: function() {
			setTimeout(function(){
				slideOut.start(-300);	
			}, 5000);
        }
	});
	
	var slideOut = new Fx.Style($('message'), 'right', {
		duration: 750,
		transition: Fx.Transitions.Quad.easeInOut
	});	
	
	slideIn.start(0);

	
}


function pasAan(selectElement) {
	
	selectElement = new Element(selectElement);
	
	var rowElement				= selectElement.getParent().getParent();
	var selectElementMetadata	= rowElement.getElements('td')[2].getElement('select');
	var spanElementOnefield		= rowElement.getElements('td')[2].getElement('span.onefield');
	var spanElementTwofields	= rowElement.getElements('td')[2].getElement('span.twofields');
	
	switch(selectElement.value) {
	

		case 'vervangpatroon':
		case 'vervangpatroonregexp':

			selectElementMetadata.setStyle('display', 'none');
			spanElementOnefield.setStyle('display', 'none');
			spanElementTwofields.setStyle('display', '');
			
			rowElement.getElementsBySelector('input,select,textarea').each(function(element){
				if(!element.id.contains('pasaan_') && !element.hasClass('skip')) {
					element.value = '';
					element.setAttribute('disabled', 'disabled');
				}
			});
			
			break;
	
		case 'voegtoevoor':
		case 'voegtoena':
		case 'voegarraywaardetoe':
		case 'voegskoswaardetoe':
			
			selectElementMetadata.setStyle('display', 'none');
			spanElementOnefield.setStyle('display', '');
			spanElementTwofields.setStyle('display', 'none');
			
			rowElement.getElementsBySelector('input,select,textarea').each(function(element){
				if(!element.id.contains('pasaan_') && !element.hasClass('skip')) {
					element.value = '';
					element.setAttribute('disabled', 'disabled');
				}
			});
			
			break;
	
		case 'vervang':
			
			selectElementMetadata.setStyle('display', '');
			spanElementOnefield.setStyle('display', 'none');
			spanElementTwofields.setStyle('display', 'none');
			
			rowElement.getElementsBySelector('input,select,textarea').each(function(element){
				if(!element.id.contains('pasaan_') && !element.hasClass('skip')) {
					element.removeAttribute('disabled');
				}
			});
			
			break;
			
			
		case 'negeer':
		default:
			
			selectElementMetadata.setStyle('display', '');
			spanElementOnefield.setStyle('display', 'none');
			spanElementTwofields.setStyle('display', 'none');
			
			rowElement.getElementsBySelector('input,select,textarea').each(function(element){
				if(!element.id.contains('pasaan_') && !element.hasClass('skip')) {
					element.value = '';
					element.setAttribute('disabled', 'disabled');
				}
			});
		
	}
	
	
	
	
	
	/*
	var rowElement = checkbox.getParent().getParent();
	rowElement.getElementsBySelector('input,select,textarea').each(function(element){
		if(!element.id.contains('pasaan_')) {
			if(checkbox.checked) {
				element.removeAttribute('disabled');
			} else {
				element.value = '';
				element.setAttribute('disabled', 'disabled');
			}
		}
	});
	*/
}

/*

function pasAan(checkbox) {
	
	var rowElement = checkbox.getParent().getParent();
	rowElement.getElementsBySelector('input,select,textarea').each(function(element){
		if(!element.id.contains('pasaan_')) {
			if(checkbox.checked) {
				element.removeAttribute('disabled');
			} else {
				element.value = '';
				element.setAttribute('disabled', 'disabled');
			}
		}
	});
	
}

*/






/*
function maakLeeg(checkbox) {
	
	var rowElement = checkbox.getParent().getParent();
	rowElement.getElementsBySelector('input,select,textarea').each(function(element){
		if(!element.id.contains('maakleeg_')) {
			if(checkbox.checked) {
				element.value = '';
				element.setAttribute('disabled', 'disabled');
			} else {
				element.removeAttribute('disabled');
			}
		}
	});
	
}
*/

/*
function saveState(el) {
    
    if(!el.id) {
        alert('Geen ID gespecificeerd voor ' + el.tagName + '-element');
        return false;
    } 
    var id = el.id;
    var n1 = $get('n1');
    var n2 = $get('n2');
    var n3 = $get('n3');
    var sub = $get('sub');
    var page = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
    page = escape(page); // Required to make a valid cookie name

    var stateCookie = new Hash.Cookie(page, {duration: 3600});
    
    // Save class
    var key = id + ':::class';
    stateCookie.set(key, el.getProperty('class'));
    
    // Save the style 'display' 
    var key = id + ':::display';
    stateCookie.set(key, el.getStyle('display'));

    // Save the style 'backgroundImage' 
    var key = id + ':::backgroundImage';
    stateCookie.set(key, el.getStyle('backgroundImage'));


}


function loadStates() {

    var n1 = $get('n1');
    var n2 = $get('n2');
    var n3 = $get('n3');
    var sub = $get('sub');
    var page = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
    page = escape(page); // Required to make a valid cookie name

    var stateCookie = new Hash.Cookie(page, {duration: 3600});
    var keys = stateCookie.keys();
    stateCookie.each(loadState);
    //console.log('loading states... (' + stateCookie.length + ')');
}

function loadState(value, key) {



//console.log('currentPage: ' + currentPage + ':::' + key);
//console.log('  savedPage: ' + savedPage   + ':::' + key );

	var id = key.split(':::')[0];
	var what = key.split(':::')[1]; 
	switch(what) {
	    case 'class':
	        $(id).setProperty('class', value);
	        break;
	    case 'display':
	        $(id).setStyle('display', value);
	        break;
	    case 'backgroundImage':
	        $(id).setStyle('backgroundImage', value);
	        break;
	}

}


function saveState(el) {
    
    if(!el.id) {
        alert('Geen ID gespecificeerd voor ' + el.tagName + '-element');
        return false;
    } 
    var id = el.id;
    var n1 = $get('n1');
    var n2 = $get('n2');
    var n3 = $get('n3');
    var sub = $get('sub');
    var page = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;

    var stateCookie = new Hash.Cookie('stateCookie', {duration: 3600});
    
    // Save class
    var key = page + ':::' + id + ':::class';
    stateCookie.set(key, el.getProperty('class'));
    
    // Save the style 'display' 
    var key = page + ':::' + id + ':::display';
    stateCookie.set(key, el.getStyle('display'));

    // Save the style 'backgroundImage' 
    var key = page + ':::' + id + ':::backgroundImage';
    stateCookie.set(key, el.getStyle('backgroundImage'));

    console.log(' savingPage: ' + page + ':::' + key);

}


function loadStates() {

    var stateCookie = new Hash.Cookie('stateCookie', {duration: 3600});
    var keys = stateCookie.keys();
    stateCookie.each(loadState);
    console.log('loading states... (' + stateCookie.length + ')');
}

function loadState(value, key) {



    var n1 = $get('n1');
    var n2 = $get('n2');
    var n3 = $get('n3');
    var sub = $get('sub');
    var currentPage = 'n1=' + n1 + '&n2=' + n2 + '&n3=' + n3 + '&sub=' + sub;
    var savedPage = key.split(':::')[0];

    if(savedPage == currentPage) {

console.log('currentPage: ' + currentPage + ':::' + key);
console.log('  savedPage: ' + savedPage   + ':::' + key );

        var id = key.split(':::')[1];
        var what = key.split(':::')[2]; 
        switch(what) {
            case 'class':
                $(id).setProperty('class', value);
                break;
            case 'display':
                $(id).setStyle('display', value);
                break;
            case 'backgroundImage':
                $(id).setStyle('backgroundImage', value);
                break;
        }
    }

}
*/

/*
Function: $get 
    This function provides access to the "get" variable scope + the element anchor 
 
Version: 1.3 
 
Arguments: 
    key - string; optional; the parameter key to search for in the url's query string (can also be "#" for the element anchor) 
    url - url; optional; the url to check for "key" in, location.href is default 
 
Example: 
    >$get("foo","http://example.com/?foo=bar"); //returns "bar" 
    >$get("foo"); //returns the value of the "foo" variable if it's present in the current url(location.href) 
    >$get("#","http://example.com/#moo"); //returns "moo" 
    >$get("#"); //returns the element anchor if any, but from the current url (location.href) 
    >$get(,"http://example.com/?foo=bar&bar=foo"); //returns {foo:'bar',bar:'foo'} 
    >$get(,"http://example.com/?foo=bar&bar=foo#moo"); //returns {foo:'bar',bar:'foo',hash:'moo'} 
    >$get(); //returns same as above, but from the current url (location.href) 
    >$get("?"); //returns the query string (without ? and element anchor) from the current url (location.href) 
 
Returns: 
    Returns the value of the variable form the provided key, or an object with the current GET variables plus the element anchor (if any) 
    Returns "" if the variable is not present in the given query string 
 
Credits: 
        Regex from [url=http://www.netlobo.com/url_query_string_javascript.html]http://www.netlobo.com/url_query_string_javascript.html[/url] 
        Function by Jens Anders Bakke, webfreak.no 
*/  
function $get(key,url){  
    if(arguments.length < 2) url =location.href;  
    if(arguments.length > 0 && key != ""){  
        if(key == "#"){  
            var regex = new RegExp("[#]([^$]*)");  
        } else if(key == "?"){  
            var regex = new RegExp("[?]([^#$]*)");  
        } else {  
            var regex = new RegExp("[?&]"+key+"=([^&#]*)");  
        }  
        var results = regex.exec(url);  
        return (results == null )? "" : results[1];  
    } else {  
        url = url.split("?");  
        var results = {};  
            if(url.length > 1){  
                url = url[1].split("#");  
                if(url.length > 1) results["hash"] = url[1];  
                url[0].split("&").each(function(item,index){  
                    item = item.split("=");  
                    results[item[0]] = item[1];  
                });  
            }  
        return results;  
    }  
}

function prepareDownload(){    
    var currentTime = new Date()
    var str_current_time = currentTime.getMonth() + '-' + currentTime.getDate() + '-' + currentTime.getFullYear() + ' ' + currentTime.getHours() + ':' + currentTime.getMinutes();
    $('grid_grids_lijst').getElement('.gridtable tbody td.action').getPrevious().getPrevious().getPrevious().setHTML('<img src=\'images/icons/spinner.gif\' /> Bezig met klaarzetten bestand').getNext().setHTML(str_current_time);    
}
