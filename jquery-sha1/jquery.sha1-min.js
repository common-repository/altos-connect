(function(d){var c=function(g,f){return(g<<f)|(g>>>(32-f))};var b=function(j){var f="";var g;var k;var h;for(g=0;g<=6;g+=2){k=(j>>>(g*4+4))&15;h=(j>>>(g*4))&15;f+=k.toString(16)+h.toString(16)}return f};var a=function(j){var g="";var h;var f;for(h=7;h>=0;h--){f=(j>>>(h*4))&15;g+=f.toString(16)}return g};var e=function(g){g=g.replace(/\x0d\x0a/g,"\x0a");var f="";for(var i=0;i<g.length;i++){var h=g.charCodeAt(i);if(h<128){f+=String.fromCharCode(h)}else{if((h>127)&&(h<2048)){f+=String.fromCharCode((h>>6)|192);f+=String.fromCharCode((h&63)|128)}else{f+=String.fromCharCode((h>>12)|224);f+=String.fromCharCode(((h>>6)&63)|128);f+=String.fromCharCode((h&63)|128)}}}return f};d.extend({sha1:function(f){var l;var x,w;var g=new Array(80);var o=1732584193;var n=4023233417;var m=2562383102;var k=271733878;var h=3285377520;var v,t,s,r,q;var y;f=e(f);var p=f.length;var u=new Array();for(x=0;x<p-3;x+=4){w=f.charCodeAt(x)<<24|f.charCodeAt(x+1)<<16|f.charCodeAt(x+2)<<8|f.charCodeAt(x+3);u.push(w)}switch(p%4){case 0:x=2147483648;break;case 1:x=f.charCodeAt(p-1)<<24|8388608;break;case 2:x=f.charCodeAt(p-2)<<24|f.charCodeAt(p-1)<<16|32768;break;case 3:x=f.charCodeAt(p-3)<<24|f.charCodeAt(p-2)<<16|f.charCodeAt(p-1)<<8|128;break}u.push(x);while((u.length%16)!=14){u.push(0)}u.push(p>>>29);u.push((p<<3)&4294967295);for(l=0;l<u.length;l+=16){for(x=0;x<16;x++){g[x]=u[l+x]}for(x=16;x<=79;x++){g[x]=c(g[x-3]^g[x-8]^g[x-14]^g[x-16],1)}v=o;t=n;s=m;r=k;q=h;for(x=0;x<=19;x++){y=(c(v,5)+((t&s)|(~t&r))+q+g[x]+1518500249)&4294967295;q=r;r=s;s=c(t,30);t=v;v=y}for(x=20;x<=39;x++){y=(c(v,5)+(t^s^r)+q+g[x]+1859775393)&4294967295;q=r;r=s;s=c(t,30);t=v;v=y}for(x=40;x<=59;x++){y=(c(v,5)+((t&s)|(t&r)|(s&r))+q+g[x]+2400959708)&4294967295;q=r;r=s;s=c(t,30);t=v;v=y}for(x=60;x<=79;x++){y=(c(v,5)+(t^s^r)+q+g[x]+3395469782)&4294967295;q=r;r=s;s=c(t,30);t=v;v=y}o=(o+v)&4294967295;n=(n+t)&4294967295;m=(m+s)&4294967295;k=(k+r)&4294967295;h=(h+q)&4294967295}var y=a(o)+a(n)+a(m)+a(k)+a(h);return y.toLowerCase()}})})(jQuery);jQuery(document).ready(function(a){a("form#altos-connect-submit").submit(function(){var b=a.trim(a("input#EMAIL1").val()),c=a.trim(a("select#cityzip").val());a("input#altos-connect-captcha").val(a.sha1(b+c))})});