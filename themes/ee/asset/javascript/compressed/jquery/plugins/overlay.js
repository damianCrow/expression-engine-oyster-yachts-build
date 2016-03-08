/**
 * @license 
 * jQuery Tools 1.2.3 Overlay - Overlay base. Extend it.
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://flowplayer.org/tools/overlay/
 *
 * Since: March 2008
 * Date:    Mon Jun 7 13:43:53 2010 +0000 
 */
!function(e){function n(n,i){
// private variables
var r,l,c,s=this,a=n.add(s),f=e(window),d=e.tools.expose&&(i.mask||i.expose),u=Math.random().toString().slice(10);
// mask configuration
d&&("string"==typeof d&&(d={color:d}),d.closeOnClick=d.closeOnEsc=!1);
// get overlay and triggerr
var g=i.target||n.attr("rel");
// overlay not found. cannot continue
if(l=g?e(g):null||n,!l.length)throw"Could not find Overlay: "+g;
// trigger's click event
n&&-1==n.index(l)&&n.click(function(e){return s.load(e),e.preventDefault()}),
// API methods  
e.extend(s,{load:function(n){
// can be opened only once
if(s.isOpened())return s;
// find the effect
var r=o[i.effect];if(!r)throw'Overlay: cannot find effect : "'+i.effect+'"';if(
// close other instances?
i.oneInstance&&e.each(t,function(){this.close(n)}),
// onBeforeLoad
n=n||e.Event(),n.type="onBeforeLoad",a.trigger(n),n.isDefaultPrevented())return s;
// opened
c=!0,
// possible mask effect
d&&e(l).expose(d);
// position & dimensions 
var g=i.top,h=i.left,p=l.outerWidth({margin:!0}),v=l.outerHeight({margin:!0});
// load effect  		 		
// mask.click closes overlay
// when window is clicked outside overlay, we close
// keyboard::escape
// one callback is enough if multiple instances are loaded simultaneously
return"string"==typeof g&&(g="center"==g?Math.max((f.height()-v)/2,0):parseInt(g,10)/100*f.height()),"center"==h&&(h=Math.max((f.width()-p)/2,0)),r[0].call(s,{top:g,left:h},function(){c&&(n.type="onLoad",a.trigger(n))}),d&&i.closeOnClick&&e.mask.getMask().one("click",s.close),i.closeOnClick&&e(document).bind("click."+u,function(n){e(n.target).parents(l).length||s.close(n)}),i.closeOnEsc&&e(document).bind("keydown."+u,function(e){27==e.keyCode&&s.close(e)}),s},close:function(n){
// close effect
// unbind the keyboard / clicking actions
return s.isOpened()?(n=n||e.Event(),n.type="onBeforeClose",a.trigger(n),n.isDefaultPrevented()?void 0:(c=!1,o[i.effect][1].call(s,function(){n.type="onClose",a.trigger(n)}),e(document).unbind("click."+u).unbind("keydown."+u),d&&e.mask.close(),s)):s},getOverlay:function(){return l},getTrigger:function(){return n},getClosers:function(){return r},isOpened:function(){return c},
// manipulate start, finish and speeds
getConf:function(){return i}}),
// callbacks	
e.each("onBeforeLoad,onStart,onLoad,onBeforeClose,onClose".split(","),function(n,t){
// configuration
e.isFunction(i[t])&&e(s).bind(t,i[t]),
// API
s[t]=function(n){return e(s).bind(t,n),s}}),
// close button
r=l.find(i.close||".close"),r.length||i.close||(r=e('<a class="close"></a>'),l.prepend(r)),r.click(function(e){s.close(e)}),
// autoload
i.load&&s.load()}
// static constructs
e.tools=e.tools||{version:"1.2.3"},e.tools.overlay={addEffect:function(e,n,t){o[e]=[n,t]},conf:{close:null,closeOnClick:!0,closeOnEsc:!0,closeSpeed:"fast",effect:"default",
// since 1.2. fixed positioning not supported by IE6
fixed:!e.browser.msie||e.browser.version>6,left:"center",load:!1,// 1.2
mask:null,oneInstance:!0,speed:"normal",target:null,// target element to be overlayed. by default taken from [rel]  
top:"10%"}};var t=[],o={};
// the default effect. nice and easy!
e.tools.overlay.addEffect("default",/* 
			onLoad/onClose functions must be called otherwise none of the 
			user supplied callback methods won't be called
		*/
function(n,t){var o=this.getConf(),i=e(window);o.fixed||(n.top+=i.scrollTop(),n.left+=i.scrollLeft()),n.position=o.fixed?"fixed":"absolute",this.getOverlay().css(n).fadeIn(o.speed,t)},function(e){this.getOverlay().fadeOut(this.getConf().closeSpeed,e)}),
// jQuery plugin initialization
e.fn.overlay=function(o){
// already constructed --> return API
var i=this.data("overlay");return i?i:(e.isFunction(o)&&(o={onBeforeLoad:o}),o=e.extend(!0,{},e.tools.overlay.conf,o),this.each(function(){i=new n(e(this),o),t.push(i),e(this).data("overlay",i)}),o.api?i:this)}}(jQuery);