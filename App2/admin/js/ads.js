'use strict';$(()=>{const ads_id='WJxYFNKPMRlS';dotclear.adblockCheck=(msg)=>{if(dotclear.adblocker_check){const ads=document.getElementById(ads_id);let adblocker_on=false;if(ads===null){adblocker_on=true;}else if(window.getComputedStyle(ads).display==='none'){adblocker_on=true;}
if(msg&&adblocker_on){window.alert(msg);}
if(ads!==null){ads.remove();}
return adblocker_on;}};const e=document.createElement('div');e.id=ads_id;e.classList.add('adsbygoogle');e.classList.add('adsbox');e.innerHTML='&nbsp;';document.body.appendChild(e);});