!function(a){a.cookit=function(o){var e=a.extend({backgroundColor:"#1c1c1c",messageColor:"#fff",linkColor:"#fad04c",buttonColor:"#fad04c",buttonTextColor:"#00000",messageText:"<b>Do you hungry ?</b> 🍪 Pursuing navigation on this site, you accept the use of cookies.",linkText:"Learn more",linkUrl:"https://www.cookiesandyou.com",buttonText:"I accept",lifetime:365},o);const t=a("body"),n=a("<div id='cookit'></div>"),c=a("<div id='cookit-container'></div>"),i=a("<p id='cookit-message'>"+e.messageText+"</p>"),r=a("<a id='cookit-link' href='"+e.linkUrl+"' target='_blank'>"+e.linkText+"</a>"),s=a("<a id='cookit-button' href='#'>"+e.buttonText+"</a>");!function(t){const o=decodeURIComponent(document.cookie),n=o.split(";");t+="=";for(let e=0;e<n.length;e++){let o=n[e];for(;" "===o.charAt(0);)o=o.substring(1);if(0===o.indexOf(t))return o.substring(t.length,o.length)}}("cookie-consent")&&(t.append(n),n.append(c).css({"background-color":e.backgroundColor}),c.append(i.css({color:e.messageColor})).append(r.css({color:e.linkColor})).append(s.css({"background-color":e.buttonColor,color:e.buttonTextColor}))),s.on("click",o=>{o.preventDefault(),n.remove(),function(o,e,t){const n=new Date;n.setTime(n.getTime()+24*t*60*60*1e3);t="expires="+n.toUTCString();document.cookie=o+"="+e+";"+t+";path=/;Secure"}("cookie-consent",1,e.lifetime)})}}(jQuery);

$(document).ready(function() {
	$.cookit({
		messageText: "This game requires the use of cookies, to find out how we use them please see our ", 
		linkText: "Privacy Policy.",
		linkUrl: "?page=privacy"
	});
});