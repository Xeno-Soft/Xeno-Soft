'use strict';$(()=>{$('.remove-if-drag').remove();$('.hidden-if-drag').hide();$('.widgets, .sortable-delete').addClass('if-drag');$('.connected, .sortable-delete').sortable({tolerance:'move',cursor:'move',axis:'y',dropOnEmpty:true,handle:'.widget-name',placeholder:'ui-sortable-placeholder',items:'li:not(.sortable-delete-placeholder,.empty-widgets)',connectWith:'.connected, .sortable-delete',start(event,ui){ui.item.css('left',ui.item.position().left+20);},update(event,ui){const ul=$(this);const widget=ui.item;const field=ul.parents('.widgets');ui.item.css('left','auto');ui.item.css('width','auto');ui.item.css('height','auto');if(ul.find('li:not(.empty-widgets)').length==0){ul.find('li.empty-widgets').show();field.find('ul.sortable-delete').hide();}else{ul.find('li.empty-widgets').hide();field.find('ul.sortable-delete').show();}
if(widget.parents('ul').is('.sortable-delete')){widget.hide('slow',function(){$(this).remove();});}
dotclear.reorder(ul);if(widget.find('.details-cmd').length==0){dotclear.widgetExpander(widget);dotclear.viewWidgetContent(widget,'close');}},});$('#widgets-ref > li').draggable({tolerance:'move',cursor:'move',connectToSortable:'.connected',helper:'clone',revert:'invalid',start(event,ui){ui.helper.css({width:$('#widgets-ref > li').css('width'),});},stop(event,ui){if(!dotclear.widget_noeditor){ui.helper.find('textarea:not(.noeditor)').each(function(){if(typeof jsToolBar==='function'){const tbWidgetText=new jsToolBar(this);tbWidgetText.draw('xhtml');}});}},});$('li.ui-draggable, ul.ui-sortable li').not('ul.sortable-delete li, li.empty-widgets').css({cursor:'move',});});