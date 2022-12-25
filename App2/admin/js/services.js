'use strict';dotclear.getEntryContent=(postId,callback,options)=>{let res='';const opt=$.extend({type:'',alert:true,clean:false,length:-1,},options);if(typeof callback!=='function'){return;}
$.get('services.php',{f:'getPostById',id:postId,post_type:opt.type,xd_check:dotclear.nonce,}).done((data)=>{const rsp=$(data).children('rsp')[0];if(rsp.attributes[0].value=='ok'){let excerpt=$(rsp).find('post_display_excerpt').text();let content=$(rsp).find('post_display_content').text();if(excerpt||content){if(opt.clean){const text=document.createElement('textarea');if(excerpt){text.textContent=excerpt;excerpt=text.innerHTML;}
if(content){text.textContent=content;content=text.innerHTML;}}
if(!opt.clean){content=(excerpt?`${excerpt}<hr />`:'')+content;}
if(opt.length>-1){content=dotclear.trimHtml(content,{limit:opt.length}).html;}
if(opt.clean&&content){content=`<pre>${content}</pre>`;}
res=content;}}else if(opt.alert){window.alert($(rsp).find('message').text());}}).fail((jqXHR,textStatus,errorThrown)=>{window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);if(opt.alert){window.alert('Server error');}}).always(()=>{callback(res);});};dotclear.getCommentContent=(commentId,callback,options)=>{let res='';const opt=$.extend({metadata:true,ip:true,alert:true,clean:false,length:-1,},options);if(typeof callback!=='function'){return;}
$.get('services.php',{f:'getCommentById',id:commentId,xd_check:dotclear.nonce,}).done((data)=>{const rsp=$(data).children('rsp')[0];if(rsp.attributes[0].value=='ok'){let content=$(rsp).find('comment_display_content').text();if(content){if(opt.clean){const text=document.createElement('textarea');text.textContent=content;content=text.innerHTML;}
if(opt.length>-1){content=dotclear.trimHtml(content,{limit:opt.length}).html;}
if(opt.clean&&content){content=`<pre>${content}</pre>`;}
if(opt.metadata){const comment_email=$(rsp).find('comment_email').text();const comment_site=$(rsp).find('comment_site').text();const comment_ip=$(rsp).find('comment_ip').text();const comment_spam_disp=$(rsp).find('comment_spam_disp').text();content+=`<p>
              <strong>${dotclear.msg.website}</strong> ${comment_site}<br />
              <strong>${dotclear.msg.email}</strong> ${comment_email}`;if(opt.ip&&dotclear.data.showIp){content+=`<br />
                <strong>${dotclear.msg.ip_address}</strong> <a href="comments.php?ip=${comment_ip}">${comment_ip}</a>`;}
content+=`</p>${comment_spam_disp}`;}
res=content;}}else if(opt.alert){window.alert($(rsp).find('message').text());}}).fail((jqXHR,textStatus,errorThrown)=>{window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);if(opt.alert){window.alert('Server error');}}).always(()=>{callback(res);});};