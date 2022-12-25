'use strict';dotclear.passwordStrength=(opts)=>{const computeMeter=(e)=>{const password=e.value;let score=0;const symbols='[!,@,#,$,%,^,&,*,?,_,~]';const check=new RegExp(`(${symbols})`);const doublecheck=new RegExp(`(.*${symbols}.*${symbols})`);const checkRepetition=(rLen,str)=>{let res='';let repeated=false;for(let i=0;i<str.length;i++){repeated=true;let j;for(j=0;j<rLen&&j+i+rLen<str.length;j++){repeated=repeated&&str.charAt(j+i)===str.charAt(j+i+rLen);}
if(j<rLen){repeated=false;}
if(repeated){i+=rLen-1;repeated=false;}else{res+=str.charAt(i);}}
return res;};score+=password.length*4;score+=checkRepetition(1,password).length-password.length;score+=checkRepetition(2,password).length-password.length;score+=checkRepetition(3,password).length-password.length;score+=checkRepetition(4,password).length-password.length;if(password.match(/(.*[0-9].*[0-9].*[0-9])/)){score+=5;}
if(password.match(doublecheck)){score+=5;}
if(password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){score+=10;}
if(password.match(/([a-zA-Z])/)&&password.match(/([0-9])/)){score+=15;}
if(password.match(check)&&password.match(/([0-9])/)){score+=15;}
if(password.match(check)&&password.match(/([a-zA-Z])/)){score+=15;}
if(password.match(/^\w+$/)||password.match(/^\d+$/)){score-=10;}
return Math.max(Math.min(score,99),0);};const updateMeter=(e)=>{e.preventDefault();const password=e.currentTarget;let meter=password.nextElementSibling;while(meter){if(meter.matches('.pw-strength-meter'))break;meter=meter.nextElementSibling;}
if(!meter){return;}
const meterValue=computeMeter(password);let meterContent='';if(meterValue>=meter.getAttribute('high')){meterContent=options.max;}else if(meterValue<=meter.getAttribute('low')){meterContent=options.min;}else{meterContent=options.avg;}
meter.setAttribute('value',meterValue);meter.setAttribute('title',meterContent);meter.innerHTML=meterContent;};const meterTemplate=new DOMParser().parseFromString(`<meter aria-live="polite" class="pw-strength-meter" value="" title="" min="0" max="99" optimum="99" low="33" high="66"></meter>`,'text/html').body.firstChild;const options=opts||{min:'-',avg:'~',max:'+',};const passwordFields=document.querySelectorAll('input[type=password].pw-strength');for(const passwordField of passwordFields){const meter=meterTemplate.cloneNode(true);const sibling=passwordField.nextElementSibling;if(sibling&&(sibling.classList.contains('pw-show')||sibling.classList.contains('pw-hide'))){sibling.after(meter);}else{passwordField.after(meter);}
meter.style.width=window.getComputedStyle(passwordField).getPropertyValue('width');passwordField.addEventListener('keyup',updateMeter);}};