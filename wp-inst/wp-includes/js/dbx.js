// DBX2.01 :: Docking Boxes (dbx)
// *****************************************************
// DOM scripting by brothercake -- http://www.brothercake.com/
// GNU Lesser General Public License -- http://www.gnu.org/licenses/lgpl.html
//******************************************************
var dbx;function dbxManager(sid){dbx = this;if(!/^[-_a-z0-9]+$/i.test(sid)) { alert('Error from dbxManager:\n"' + sid + '" is an invalid session ID'); return; }this.supported = !(document.getElementsByTagName('*').length == 0 || (navigator.vendor == 'KDE' && typeof window.sidebar == 'undefined'));if(!this.supported) { return; }this.etype = typeof document.addEventListener != 'undefined' ? 'addEventListener' : typeof document.attachEvent != 'undefined' ? 'attachEvent' : 'none';this.eprefix = (this.etype == 'attachEvent' ? 'on' : '');if(typeof window.opera != 'undefined' && parseFloat(navigator.userAgent.toLowerCase().split(/opera[\/ ]/)[1].split(' ')[0], 10) < 7.5){this.etype = 'none';}if(this.etype == 'none') { this.supported = false; return; }this.running = 0;this.sid = sid;this.savedata = {};this.cookiestate = this.getCookieState();};dbxManager.prototype.setCookieState = function(){var now = new Date();now.setTime(now.getTime() + (365*24*60*60*1000));var str = '';for(j in this.savedata){if(typeof this.savedata[j]!='function'){str += j + '=' + this.savedata[j] + '&'}}this.state = str.replace(/^(.+)&$/, '$1');if(typeof this.onstatechange == 'undefined' || this.onstatechange()){document.cookie = 'dbx-' + this.sid + '='+ this.state+ '; expires=' + now.toGMTString()+ '; path=/';}};dbxManager.prototype.getCookieState = function(){this.cookiestate = null;if(document.cookie){if(document.cookie.indexOf('dbx-' + this.sid)!=-1){this.cookie = document.cookie.split('dbx-' + this.sid + '=')[1].split('&');for(var i in this.cookie){if(typeof this.cookie[i]!='function'){this.cookie[i] = this.cookie[i].split('=');this.cookie[i][1] = this.cookie[i][1].split(',');}}this.cookiestate = {};for(i in this.cookie){if(typeof this.cookie[i]!='function'){this.cookiestate[this.cookie[i][0]] = this.cookie[i][1];}}}}return this.cookiestate;};dbxManager.prototype.addDataMember = function(gid, order){this.savedata[gid] = order;};dbxManager.prototype.createElement = function(tag){return typeof document.createElementNS != 'undefined' ? document.createElementNS('http://www.w3.org/1999/xhtml', tag) : document.createElement(tag);};dbxManager.prototype.getTarget = function(e, pattern, node){if(typeof node != 'undefined'){var target = node;}else{target = typeof e.target != 'undefined' ? e.target : e.srcElement;}var regex = new RegExp(pattern, '');while(!regex.test(target.className)){target = target.parentNode;}return target;};function dbxGroup(gid, dir, thresh, fix, ani, togs, def, open, close, move, toggle, kmove, ktoggle, syntax){if(!/^[-_a-z0-9]+$/i.test(gid)) { alert('Error from dbxGroup:\n"' + gid + '" is an invalid container ID'); return; }this.container = document.getElementById(gid);if(this.container == null || !dbx.supported) { return; }var self = this;this.gid = gid;this.dragok = false;this.box = null;this.vertical = dir == 'vertical';this.threshold = parseInt(thresh, 10);this.restrict = fix == 'yes';this.resolution = parseInt(ani, 10);this.toggles = togs == 'yes';this.defopen = def != 'closed';this.vocab = {'open' : open,'close' : close,'move' : move,'toggle' : toggle,'kmove' : kmove,'ktoggle' : ktoggle,'syntax' : syntax};this.container.style.position = 'relative';this.container.style.display = 'block';if(typeof window.opera != 'undefined'){this.container.style.display = 'run-in';}this.boxes = [];this.buttons = [];this.order = [];this.eles = this.container.getElementsByTagName('*');for(var i=0; i<this.eles.length; i++){if(/dbx\-box/i.test(this.eles[i].className) && !/dbx\-dummy/i.test(this.eles[i].className)){this.eles[i].style.position = 'relative';this.eles[i].style.display = 'block';this.boxes.push(this.eles[i]);this.eles[i].className += ' dbx-box-open';this.eles[i].className += ' dbxid' + this.order.length;this.order.push(this.order.length.toString() + '+');this.eles[i][dbx.etype](dbx.eprefix + 'mousedown', function(e){if(!e) { e = window.event; }self.mousedown(e, dbx.getTarget(e, 'dbx\-box'));}, false);}if(/dbx\-handle/i.test(this.eles[i].className)){this.eles[i].style.position = 'relative';this.eles[i].style.display = 'block';this.eles[i].className += ' dbx-handle-cursor';this.eles[i].setAttribute('title', this.eles[i].getAttribute('title') == null || this.eles[i].title == '' ? this.vocab.move : this.vocab.syntax.replace('%mytitle%', this.eles[i].title).replace('%dbxtitle%', this.vocab.move));if(this.toggles){this.buttons.push(this.addToggleBehavior(this.eles[i]));}else{this.eles[i][dbx.etype](dbx.eprefix + 'key' + (typeof document.uniqueID != 'undefined' || navigator.vendor == 'Apple Computer, Inc.' ? 'down' : 'press'), function(e){if(!e) { e = window.event; }return self.keypress(e, dbx.getTarget(e, 'dbx\-handle'));}, false);this.eles[i][dbx.etype](dbx.eprefix + 'focus', function(e){if(!e) { e = window.event; }self.createTooltip(null, dbx.getTarget(e, 'dbx\-handle'));}, false);this.eles[i][dbx.etype](dbx.eprefix + 'blur', function(){self.removeTooltip();}, false);}}}dbx.addDataMember(this.gid, this.order.join(','));var dummy = this.container.appendChild(dbx.createElement('span'));dummy.className = 'dbx-box dbx-dummy';dummy.style.display = 'block';dummy.style.width = '0';dummy.style.height = '0';dummy.style.overflow = 'hidden';if(this.vertical) { dummy.className += ' dbx-offdummy'; }this.boxes.push(dummy);if(dbx.cookiestate != null && typeof dbx.cookiestate[this.gid] != 'undefined'){var num = dbx.cookiestate[this.gid].length;if(num == this.boxes.length - 1){for(i=0; i<num; i++){var index = parseInt(dbx.cookiestate[this.gid][i], 10);this.container.insertBefore(this.boxes[index], dummy);if(this.toggles && dbx.cookiestate[this.gid][i].charAt(1) == '-'){this.toggleBoxState(this.buttons[index], false);}}this.getBoxOrder();}}else if(!this.defopen && this.toggles){var len = this.buttons.length;for(i=0; i<len; i++){this.toggleBoxState(this.buttons[i], true);}}document[dbx.etype](dbx.eprefix + 'mouseout', function(e){if(typeof e.target == 'undefined') { e = window.event; e.relatedTarget = e.toElement; }if(e.relatedTarget == null){self.mouseup(e);}}, false);document[dbx.etype](dbx.eprefix + 'mousemove', function(e){self.mousemove(e);}, false);document[dbx.etype](dbx.eprefix + 'mouseup', function(e){self.mouseup(e);}, false);this.keydown = false;document[dbx.etype](dbx.eprefix + 'keydown', function(){self.keydown = true;}, false);document[dbx.etype](dbx.eprefix + 'keyup', function(){self.keydown = false;}, false);};dbxGroup.prototype.addToggleBehavior = function(){var self = this;var button = arguments[0].appendChild(dbx.createElement('a'));button.appendChild(document.createTextNode('\u00a0'));button.style.cursor = 'pointer';button.href = 'javascript:void(null)';button.className = 'dbx-toggle dbx-toggle-open';button.setAttribute('title', this.vocab.toggle.replace('%toggle%', this.vocab.close));button.hasfocus = typeof window.opera != 'undefined' || navigator.vendor == 'Apple Computer, Inc.' ? null : false;this.tooltip = null;button.onclick = function(){if(this.hasfocus === true || this.hasfocus === null){self.removeTooltip();self.toggleBoxState(this, true);}};button['onkey' + (typeof document.uniqueID != 'undefined' || navigator.vendor == 'Apple Computer, Inc.' ? 'down' : 'press')] = function(e){if(!e) { e = window.event; }return self.keypress(e, this);};button.onfocus = function(){var len = self.buttons.length;for(var i=0; i<len; i++){self.buttons[i].className = self.buttons[i].className.replace(/[ ](dbx\-toggle\-hilite\-)(open|closed)/, '');}var isopen = (/dbx\-toggle\-open/.test(this.className));this.className += ' dbx-toggle-hilite-' + (isopen ? 'open' : 'closed');self.createTooltip(isopen, this);this.isactive = true;if(this.hasfocus !== null) { this.hasfocus = true; }};button.onblur = function(){this.className = this.className.replace(/[ ](dbx\-toggle\-hilite\-)(open|closed)/, '');self.removeTooltip();if(this.hasfocus !== null) { this.hasfocus = false; }};return button;};dbxGroup.prototype.toggleBoxState = function(button, regen){var isopen = (/dbx\-toggle\-open/.test(button.className));var parent = dbx.getTarget(null, 'dbx\-box', button);dbx.box = parent;dbx.toggle = button;if(typeof dbx.container == 'undefined'){dbx.group = dbx.getTarget(null, 'dbx\-group', parent);}else { dbx.group = dbx.container; }if((!isopen && (typeof dbx.onboxopen == 'undefined' || dbx.onboxopen()))||(isopen && (typeof dbx.onboxclose == 'undefined' || dbx.onboxclose()))){button.className = 'dbx-toggle dbx-toggle-' + (isopen ? 'closed' : 'open');button.title = this.vocab.toggle.replace('%toggle%', isopen ? this.vocab.open : this.vocab.close);if(typeof button.isactive != 'undefined'){button.className += ' dbx-toggle-hilite-' + (isopen ? 'closed' : 'open')}parent.className = parent.className.replace(/[ ](dbx-box-)(open|closed)/, ' $1' + (isopen ? 'closed' : 'open'));if(regen) { this.getBoxOrder(); }}};dbxGroup.prototype.shiftBoxPosition = function(e, anchor, positive){var parent = dbx.getTarget(null, 'dbx\-box', anchor);dbx.group = this.container;dbx.box = parent;dbx.event = e;if(typeof dbx.onboxdrag == 'undefined' || dbx.onboxdrag()){var positions = [];var len = this.boxes.length;for(var i=0; i<len; i++){positions[i] = [i, this.boxes[i][this.vertical ? 'offsetTop' : 'offsetLeft']];if(parent == this.boxes[i]) { this.idref = i; }}positions.sort(this.compare);for(i=0; i<len; i++){if(positions[i][0] == this.idref){if((positive && i < len - 2) || (!positive && i > 0)){var sibling = this.boxes[positions[i + (positive ? 1 : -1)][0]];if(this.resolution > 0){var visipos = { 'x' : parent.offsetLeft, 'y' : parent.offsetTop };var siblingpos = { 'x' : sibling.offsetLeft, 'y' : sibling.offsetTop };}var obj = { 'insert' : (positive ? sibling : parent), 'before' : (positive ? parent : sibling) };this.container.insertBefore(obj.insert, obj.before);if(this.resolution > 0){var animators ={'sibling' : new dbxAnimator(this, sibling, siblingpos, this.resolution, true, anchor),'parent' : new dbxAnimator(this, parent, visipos, this.resolution, true, anchor)};}else{anchor.focus();}break;}}}this.getBoxOrder();}};dbxGroup.prototype.compare = function(a, b){return a[1] - b[1];};dbxGroup.prototype.createTooltip = function(isopen, anchor){if(this.keydown){this.tooltip = this.container.appendChild(dbx.createElement('span'));this.tooltip.style.visibility = 'hidden';this.tooltip.className = 'dbx-tooltip';if(isopen != null){this.tooltip.appendChild(document.createTextNode(this.vocab.kmove + this.vocab.ktoggle.replace('%toggle%', isopen ? this.vocab.close : this.vocab.open)));}else{this.tooltip.appendChild(document.createTextNode(this.vocab.kmove));}var parent = dbx.getTarget(null, 'dbx\-box', anchor);this.tooltip.style.left = parent.offsetLeft + 'px';this.tooltip.style.top = parent.offsetTop + 'px';var tooltip = this.tooltip;window.setTimeout(function(){if(tooltip != null) { tooltip.style.visibility = 'visible'; }}, 500);}};dbxGroup.prototype.removeTooltip = function(){if(this.tooltip != null){this.tooltip.parentNode.removeChild(this.tooltip);this.tooltip = null;}};dbxGroup.prototype.mousedown = function(e, box){var node = typeof e.target != 'undefined' ? e.target : e.srcElement;if(node.nodeName == '#text') { node = node.parentNode; }if(!/dbx\-(toggle|box|group)/i.test(node.className)){while(!/dbx\-(handle|box|group)/i.test(node.className)){node = node.parentNode;}}if(/dbx\-handle/i.test(node.className)){this.removeTooltip();this.released = false;this.initial = { 'x' : e.clientX, 'y' : e.clientY };this.current = { 'x' : 0, 'y' : 0 };this.createCloneBox(box);if(typeof e.preventDefault != 'undefined' ) { e.preventDefault(); }if(typeof document.onselectstart != 'undefined'){document.onselectstart = function() { return false; }}}};dbxGroup.prototype.mousemove = function(e){if(this.dragok && this.box != null){this.positive = this.vertical ? (e.clientY > this.current.y ? true : false) : (e.clientX > this.current.x ? true : false);this.current = { 'x' : e.clientX, 'y' : e.clientY };var overall = { 'x' : this.current.x - this.initial.x, 'y' : this.current.y - this.initial.y };if(((overall.x >= 0 && overall.x <= this.threshold) || (overall.x <= 0 && overall.x >= 0 - this.threshold))&&((overall.y >= 0 && overall.y <= this.threshold) || (overall.y <= 0 && overall.y >= 0 - this.threshold))){this.current.x -= overall.x;this.current.y -= overall.y;}if(this.released || overall.x > this.threshold || overall.x < (0 - this.threshold) || overall.y > this.threshold || overall.y < (0 - this.threshold)){dbx.group = this.container;dbx.box = this.box;dbx.event = e;if(typeof dbx.onboxdrag == 'undefined' || dbx.onboxdrag()){this.released = true;if(!this.restrict || !this.vertical) { this.boxclone.style.left = (this.current.x - this.difference.x) + 'px'; }if(!this.restrict || this.vertical) { this.boxclone.style.top = (this.current.y - this.difference.y) + 'px'; }this.moveOriginalToPosition(this.current.x, this.current.y);if(typeof e.preventDefault != 'undefined' ) { e.preventDefault(); }}}}return true;};dbxGroup.prototype.mouseup = function(e){if(this.box != null){this.moveOriginalToPosition(e.clientX, e.clientY);this.removeCloneBox();this.getBoxOrder();if(typeof document.onselectstart != 'undefined'){document.onselectstart = function() { return true; }}}this.dragok = false;};dbxGroup.prototype.keypress = function(e, anchor){if(/^(3[7-9])|(40)$/.test(e.keyCode)){this.removeTooltip();if((this.vertical && /^(38|40)$/.test(e.keyCode)) || (!this.vertical && /^(37|39)$/.test(e.keyCode))){this.shiftBoxPosition(e, anchor, /^[3][78]$/.test(e.keyCode) ? false : true);if(typeof e.preventDefault != 'undefined') { e.preventDefault(); }else { return false; }typeof e.stopPropagation != 'undefined' ? e.stopPropagation() : e.cancelBubble = true;this.keydown = false;}}return true;};dbxGroup.prototype.getBoxOrder = function(){this.order = [];var len = this.eles.length;for(var j=0; j<len; j++){if(/dbx\-box/i.test(this.eles[j].className) && !/dbx\-(clone|dummy)/i.test(this.eles[j].className)){this.order.push(this.eles[j].className.split('dbxid')[1] + (/dbx\-box\-open/i.test(this.eles[j].className) ? '+' : '-'));}}dbx.savedata[this.gid] = this.order.join(',');dbx.setCookieState();};dbxGroup.prototype.createClone = function(){var clone = this.container.appendChild(arguments[0].cloneNode(true));clone.className += ' dbx-clone';clone.style.position = 'absolute';clone.style.visibility = 'hidden';clone.style.zIndex = arguments[1];clone.style.left = arguments[2].x + 'px';clone.style.top = arguments[2].y + 'px';clone.style.width = arguments[0].offsetWidth + 'px';clone.style.height = arguments[0].offsetHeight + 'px';return clone;};dbxGroup.prototype.createCloneBox = function(box){this.box = box;this.position = { 'x' : this.box.offsetLeft, 'y' : this.box.offsetTop };this.difference = { 'x' : (this.initial.x - this.position.x), 'y' : (this.initial.y - this.position.y) };this.boxclone = this.createClone(this.box, 30000, this.position);this.boxclone.style.cursor = 'move';this.dragok = true;};dbxGroup.prototype.removeCloneBox = function(){this.container.removeChild(this.boxclone);this.box.style.visibility = 'visible';this.box = null;};dbxGroup.prototype.moveOriginalToPosition = function(clientX, clientY){var cloneprops = {'xy' : this.vertical ? clientY - this.difference.y : clientX - this.difference.x,'wh' : this.vertical ? this.boxclone.offsetHeight : this.boxclone.offsetWidth};this.box.style.visibility = 'hidden';this.boxclone.style.visibility = 'visible';var len = this.boxes.length;for(var i=0; i<len; i++){var boxprops = {'xy' : this.vertical ? this.boxes[i].offsetTop : this.boxes[i].offsetLeft,'wh' : this.vertical ? this.boxes[i].offsetHeight : this.boxes[i].offsetWidth};if((this.positive && cloneprops.xy + cloneprops.wh > boxprops.xy && cloneprops.xy < boxprops.xy)||(!this.positive && cloneprops.xy < boxprops.xy && cloneprops.xy + cloneprops.wh > boxprops.xy)){if(this.boxes[i] == this.box) { return; }var sibling = this.box.nextSibling;while(sibling.className == null || !/dbx\-box/.test(sibling.className)){sibling = sibling.nextSibling;}if(this.boxes[i] == sibling) { return; }if(this.resolution > 0){if(this.box[this.vertical ? 'offsetTop' : 'offsetLeft'] < boxprops.xy){var visibox = this.boxes[i].previousSibling;while(visibox.className == null || !/dbx\-box/.test(visibox.className)){visibox = visibox.previousSibling;}}else{visibox = this.boxes[i];}var visipos = { 'x' : visibox.offsetLeft, 'y' : visibox.offsetTop };}var prepos = { 'x' : this.box.offsetLeft, 'y' : this.box.offsetTop };this.container.insertBefore(this.box, this.boxes[i]);this.initial.x += (this.box.offsetLeft - prepos.x);this.initial.y += (this.box.offsetTop - prepos.y);if(this.resolution > 0 && visibox != this.box){var animator = new dbxAnimator(this, visibox, visipos, this.resolution, false, null);}else{}break;}}};function dbxAnimator(caller, box, pos, res, kbd, anchor){this.caller = caller;this.box = box;this.timer = null;var before = pos[this.caller.vertical ? 'y' : 'x'];var after = this.box[this.caller.vertical ? 'offsetTop' : 'offsetLeft'];if(before != after){if(dbx.running > this.caller.boxes.length - 1) { return; }var clone = this.caller.createClone(this.box, 29999, arguments[2]);clone.style.visibility = 'visible';this.box.style.visibility = 'hidden';this.animateClone(clone,before,after > before ? after - before : 0 - (before - after),this.caller.vertical ? 'top' : 'left',res,kbd,anchor);}};dbxAnimator.prototype.animateClone = function(clone, current, change, dir, res, kbd, anchor){var self = this;var count = 0;dbx.running ++;this.timer = window.setInterval(function(){count ++;current += change / res;clone.style[dir] = current + 'px';if(count == res){window.clearTimeout(self.timer);self.timer = null;dbx.running --;self.caller.container.removeChild(clone);self.box.style.visibility = 'visible';if(kbd){if(anchor != null && anchor.parentNode.style.visibility != 'hidden'){anchor.focus();}else if(self.caller.toggles){var button = self.caller.buttons[parseInt(self.box.className.split('dbxid')[1],10)];if(button != null && typeof button.isactive != 'undefined'){button.focus();}}}}}, 20);};if(typeof window.attachEvent != 'undefined'){window.attachEvent('onunload', function(){var ev = ['mousedown', 'mousemove', 'mouseup', 'mouseout', 'click', 'keydown', 'keyup', 'focus', 'blur', 'selectstart', 'statechange', 'boxdrag', 'boxopen', 'boxclose'];var el = ev.length;var dl = document.all.length;for(var i=0; i<dl; i++){for(var j=0; j<el; j++){document.all[i]['on' + ev[j]] = null;}}});}
