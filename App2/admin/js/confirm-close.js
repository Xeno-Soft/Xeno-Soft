'use strict';dotclear.confirmClose=class{constructor(){this.prompt='You have unsaved changes.';this.forms_id=[];this.forms=[];this.form_submit=false;if(arguments.length>0){for(const argument of arguments){this.forms_id.push(argument);}}}
getCurrentForms(){const eltRef=(e)=>(e.id!=undefined&&e.id!=''?e.id:e.name);const formsInPage=this.getForms();this.forms=[];for(const f of formsInPage){const tmpForm=[];for(let j=0;j<f.elements.length;j++){const e=this.getFormElementValue(f[j]);if(e!==undefined){tmpForm[eltRef(f[j])]=e;}}
const j=f.getElementsByTagName('iframe');if(j!==undefined){for(const k of j){if(k.contentDocument.body.id!==undefined&&k.contentDocument.body.id!==''){tmpForm[k.contentDocument.body.id]=k.contentDocument.body.innerHTML;}}}
this.forms.push(tmpForm);f.addEventListener('submit',()=>(this.form_submit=true));}}
compareForms(){if(this.forms.length==0){return true;}
const formMatch=(current,source)=>Object.keys(current).every((key)=>!source.hasOwnProperty(key)||(source.hasOwnProperty(key)&&source[key]===current[key]),);const eltRef=(e)=>(e.id!=undefined&&e.id!=''?e.id:e.name);const formFirstDiff=(current,source)=>{let diff='<none>';Object.keys(current).every((key)=>{if(source.hasOwnProperty(key)&&current[key]!==source[key]){diff=`Key = [${key}] - Original = [${source[key]}] - Current = [${current[key]}]`;return false;}
return true;});return diff;};const formsInPage=this.getForms();for(let i=0;i<formsInPage.length;i++){const f=formsInPage[i];const tmpForm=[];for(let j=0;j<f.elements.length;j++){const e=this.getFormElementValue(f[j]);if(e!==undefined){tmpForm[eltRef(f[j])]=e;}}
const j=f.getElementsByTagName('iframe');if(j!==undefined){for(const k of j){if(k.contentDocument.body.id!==undefined&&k.contentDocument.body.id!==''){tmpForm[k.contentDocument.body.id]=k.contentDocument.body.innerHTML;}}}
if(!formMatch(tmpForm,this.forms[i])){if(dotclear.debug){console.log('Input data modified:');console.log('Current form',tmpForm);console.log('Saved form',this.forms[i]);console.log('First difference:',formFirstDiff(tmpForm,this.forms[i]));}
return false;}}
return true;}
getForms(){if(!document.getElementsByTagName||!document.getElementById){return[];}
if(this.forms_id.length>0){const res=[];for(const form_id of this.forms_id){const f=document.getElementById(form_id);if(f!=undefined){res.push(f);}}
return res;}
return document.getElementsByTagName('form');}
getFormElementValue(e){if(e===undefined||((e.id===undefined||e.id==='')&&(e.name===undefined||e.name===''))||(e.type!==undefined&&e.type==='button')||(e.type!==undefined&&e.type==='submit')||e.hasAttribute('readonly')||e.classList.contains('meta-helper')||e.classList.contains('checkbox-helper')){return undefined;}
if(e.type!==undefined&&(e.type==='radio'||e.type==='checkbox')){return e.checked?e.value:null;}
if(e.type!==undefined&&e.type==='password'){return null;}
return e.value!==undefined?e.value:null;}};window.addEventListener('load',()=>{const confirm_close=dotclear.getData('confirm_close');dotclear.confirmClosePage=new dotclear.confirmClose(...confirm_close.forms);dotclear.confirmClosePage.prompt=confirm_close.prompt;dotclear.confirmClosePage.getCurrentForms();});window.addEventListener('beforeunload',(event)=>{if(event==undefined&&window.event){event=window.event;}
if(dotclear.confirmClosePage!==undefined&&!dotclear.confirmClosePage.form_submit&&!dotclear.confirmClosePage.compareForms()){if(dotclear.debug){console.log('Confirmation before exiting is required.');}
event.preventDefault();event.returnValue='';}});