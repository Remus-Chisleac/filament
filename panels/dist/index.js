(()=>{var Z=Object.create,q=Object.defineProperty,ee=Object.getPrototypeOf,re=Object.prototype.hasOwnProperty,te=Object.getOwnPropertyNames,ne=Object.getOwnPropertyDescriptor,ae=n=>q(n,"__esModule",{value:!0}),ie=(n,f)=>()=>(f||(f={exports:{}},n(f.exports,f)),f.exports),se=(n,f,_)=>{if(f&&typeof f=="object"||typeof f=="function")for(let c of te(f))!re.call(n,c)&&c!=="default"&&q(n,c,{get:()=>f[c],enumerable:!(_=ne(f,c))||_.enumerable});return n},oe=n=>se(ae(q(n!=null?Z(ee(n)):{},"default",n&&n.__esModule&&"default"in n?{get:()=>n.default,enumerable:!0}:{value:n,enumerable:!0})),n),fe=ie((n,f)=>{(function(_,c,A){if(!_)return;for(var d={8:"backspace",9:"tab",13:"enter",16:"shift",17:"ctrl",18:"alt",20:"capslock",27:"esc",32:"space",33:"pageup",34:"pagedown",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",45:"ins",46:"del",91:"meta",93:"meta",224:"meta"},y={106:"*",107:"+",109:"-",110:".",111:"/",186:";",187:"=",188:",",189:"-",190:".",191:"/",192:"`",219:"[",220:"\\",221:"]",222:"'"},g={"~":"`","!":"1","@":"2","#":"3",$:"4","%":"5","^":"6","&":"7","*":"8","(":"9",")":"0",_:"-","+":"=",":":";",'"':"'","<":",",">":".","?":"/","|":"\\"},L={option:"alt",command:"meta",return:"enter",escape:"esc",plus:"+",mod:/Mac|iPod|iPhone|iPad/.test(navigator.platform)?"meta":"ctrl"},C,w=1;w<20;++w)d[111+w]="f"+w;for(w=0;w<=9;++w)d[w+96]=w.toString();function S(e,r,a){if(e.addEventListener){e.addEventListener(r,a,!1);return}e.attachEvent("on"+r,a)}function T(e){if(e.type=="keypress"){var r=String.fromCharCode(e.which);return e.shiftKey||(r=r.toLowerCase()),r}return d[e.which]?d[e.which]:y[e.which]?y[e.which]:String.fromCharCode(e.which).toLowerCase()}function $(e,r){return e.sort().join(",")===r.sort().join(",")}function B(e){var r=[];return e.shiftKey&&r.push("shift"),e.altKey&&r.push("alt"),e.ctrlKey&&r.push("ctrl"),e.metaKey&&r.push("meta"),r}function V(e){if(e.preventDefault){e.preventDefault();return}e.returnValue=!1}function H(e){if(e.stopPropagation){e.stopPropagation();return}e.cancelBubble=!0}function K(e){return e=="shift"||e=="ctrl"||e=="alt"||e=="meta"}function J(){if(!C){C={};for(var e in d)e>95&&e<112||d.hasOwnProperty(e)&&(C[d[e]]=e)}return C}function U(e,r,a){return a||(a=J()[e]?"keydown":"keypress"),a=="keypress"&&r.length&&(a="keydown"),a}function X(e){return e==="+"?["+"]:(e=e.replace(/\+{2}/g,"+plus"),e.split("+"))}function D(e,r){var a,p,k,M=[];for(a=X(e),k=0;k<a.length;++k)p=a[k],L[p]&&(p=L[p]),r&&r!="keypress"&&g[p]&&(p=g[p],M.push("shift")),K(p)&&M.push(p);return r=U(p,M,r),{key:p,modifiers:M,action:r}}function I(e,r){return e===null||e===c?!1:e===r?!0:I(e.parentNode,r)}function v(e){var r=this;if(e=e||c,!(r instanceof v))return new v(e);r.target=e,r._callbacks={},r._directMap={};var a={},p,k=!1,M=!1,E=!1;function m(t){t=t||{};var s=!1,l;for(l in a){if(t[l]){s=!0;continue}a[l]=0}s||(E=!1)}function j(t,s,l,i,u,b){var o,h,O=[],P=l.type;if(!r._callbacks[t])return[];for(P=="keyup"&&K(t)&&(s=[t]),o=0;o<r._callbacks[t].length;++o)if(h=r._callbacks[t][o],!(!i&&h.seq&&a[h.seq]!=h.level)&&P==h.action&&(P=="keypress"&&!l.metaKey&&!l.ctrlKey||$(s,h.modifiers))){var Q=!i&&h.combo==u,W=i&&h.seq==i&&h.level==b;(Q||W)&&r._callbacks[t].splice(o,1),O.push(h)}return O}function x(t,s,l,i){r.stopCallback(s,s.target||s.srcElement,l,i)||t(s,l)===!1&&(V(s),H(s))}r._handleKey=function(t,s,l){var i=j(t,s,l),u,b={},o=0,h=!1;for(u=0;u<i.length;++u)i[u].seq&&(o=Math.max(o,i[u].level));for(u=0;u<i.length;++u){if(i[u].seq){if(i[u].level!=o)continue;h=!0,b[i[u].seq]=1,x(i[u].callback,l,i[u].combo,i[u].seq);continue}h||x(i[u].callback,l,i[u].combo)}var O=l.type=="keypress"&&M;l.type==E&&!K(t)&&!O&&m(b),M=h&&l.type=="keydown"};function G(t){typeof t.which!="number"&&(t.which=t.keyCode);var s=T(t);if(s){if(t.type=="keyup"&&k===s){k=!1;return}r.handleKey(s,B(t),t)}}function Y(){clearTimeout(p),p=setTimeout(m,1e3)}function z(t,s,l,i){a[t]=0;function u(P){return function(){E=P,++a[t],Y()}}function b(P){x(l,P,t),i!=="keyup"&&(k=T(P)),setTimeout(m,10)}for(var o=0;o<s.length;++o){var h=o+1===s.length,O=h?b:u(i||D(s[o+1]).action);N(s[o],O,i,t,o)}}function N(t,s,l,i,u){r._directMap[t+":"+l]=s,t=t.replace(/\s+/g," ");var b=t.split(" "),o;if(b.length>1){z(t,b,s,l);return}o=D(t,l),r._callbacks[o.key]=r._callbacks[o.key]||[],j(o.key,o.modifiers,{type:o.action},i,t,u),r._callbacks[o.key][i?"unshift":"push"]({callback:s,modifiers:o.modifiers,action:o.action,seq:i,level:u,combo:t})}r._bindMultiple=function(t,s,l){for(var i=0;i<t.length;++i)N(t[i],s,l)},S(e,"keypress",G),S(e,"keydown",G),S(e,"keyup",G)}v.prototype.bind=function(e,r,a){var p=this;return e=e instanceof Array?e:[e],p._bindMultiple.call(p,e,r,a),p},v.prototype.unbind=function(e,r){var a=this;return a.bind.call(a,e,function(){},r)},v.prototype.trigger=function(e,r){var a=this;return a._directMap[e+":"+r]&&a._directMap[e+":"+r]({},e),a},v.prototype.reset=function(){var e=this;return e._callbacks={},e._directMap={},e},v.prototype.stopCallback=function(e,r){var a=this;if((" "+r.className+" ").indexOf(" mousetrap ")>-1||I(r,a.target))return!1;if("composedPath"in e&&typeof e.composedPath=="function"){var p=e.composedPath()[0];p!==e.target&&(r=p)}return r.tagName=="INPUT"||r.tagName=="SELECT"||r.tagName=="TEXTAREA"||r.isContentEditable},v.prototype.handleKey=function(){var e=this;return e._handleKey.apply(e,arguments)},v.addKeycodes=function(e){for(var r in e)e.hasOwnProperty(r)&&(d[r]=e[r]);C=null},v.init=function(){var e=v(c);for(var r in e)r.charAt(0)!=="_"&&(v[r]=function(a){return function(){return e[a].apply(e,arguments)}}(r))},v.init(),_.Mousetrap=v,typeof f<"u"&&f.exports&&(f.exports=v),typeof define=="function"&&define.amd&&define(function(){return v})})(typeof window<"u"?window:null,typeof window<"u"?document:null)}),R=oe(fe());(function(n){if(n){var f={},_=n.prototype.stopCallback;n.prototype.stopCallback=function(c,A,d,y){var g=this;return g.paused?!0:f[d]||f[y]?!1:_.call(g,c,A,d)},n.prototype.bindGlobal=function(c,A,d){var y=this;if(y.bind(c,A,d),c instanceof Array){for(var g=0;g<c.length;g++)f[c[g]]=!0;return}f[c]=!0},n.init()}})(typeof Mousetrap<"u"?Mousetrap:void 0);var le=n=>{n.directive("mousetrap",(f,{modifiers:_,expression:c},{evaluate:A})=>{let d=()=>c?A(c):f.click();_=_.map(y=>y.replace("-","+")),_.includes("global")&&(_=_.filter(y=>y!=="global"),R.default.bindGlobal(_,y=>{y.preventDefault(),d()})),R.default.bind(_,y=>{y.preventDefault(),d()})})},F=le;document.addEventListener("alpine:init",()=>{window.Alpine.plugin(F),window.Alpine.store("sidebar",{isOpen:window.Alpine.$persist(!0).as("isOpen"),collapsedGroups:window.Alpine.$persist(null).as("collapsedGroups"),groupIsCollapsed:function(n){return this.collapsedGroups.includes(n)},collapseGroup:function(n){this.collapsedGroups.includes(n)||(this.collapsedGroups=this.collapsedGroups.concat(n))},toggleCollapsedGroup:function(n){this.collapsedGroups=this.collapsedGroups.includes(n)?this.collapsedGroups.filter(f=>f!==n):this.collapsedGroups.concat(n)},close:function(){this.isOpen=!1},open:function(){this.isOpen=!0}}),window.Alpine.store("theme",window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"),window.addEventListener("theme-changed",n=>{window.Alpine.store("theme",n.detail)}),window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change",n=>{window.Alpine.store("theme",n.matches?"dark":"light")})});})();
