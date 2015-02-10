/* CodeMirror - Minified & Bundled
 Generated on 6/9/2014 with http://codemirror.net/doc/compress.html
 Version: HEAD

 CodeMirror Library:
 - codemirror.js
 Modes:
 - clike.js
 - css.js
 - htmlembedded.js
 - htmlmixed.js
 - http.js
 - javascript.js
 - perl.js
 - php.js
 - sass.js
 - sql.js
 - xml.js
 Add-ons:
 - closebrackets.js
 - closetag.js
 - fullscreen.js
 - indent-fold.js
 - mark-selection.js
 - match-highlighter.js
 - matchbrackets.js
 - matchtags.js
 - search.js
 - searchcursor.js
 */

!function (a) {
	if ("object" == typeof exports && "object" == typeof module)module.exports = a(); else {
		if ("function" == typeof define && define.amd)return define([], a);
		this.CodeMirror = a()
	}
}(function () {
	"use strict";
	function w(a, b) {
		if (!(this instanceof w))return new w(a, b);
		this.options = b = b || {}, Dg(Yd, b, !1), K(b);
		var c = b.value;
		"string" == typeof c && (c = new xf(c, b.mode)), this.doc = c;
		var f = this.display = new x(a, c);
		f.wrapper.CodeMirror = this, G(this), E(this), b.lineWrapping && (this.display.wrapper.className += " CodeMirror-wrap"), b.autofocus && !o && Pc(this), this.state = {
			keyMaps: [],
			overlays: [],
			modeGen: 0,
			overwrite: !1,
			focused: !1,
			suppressEdits: !1,
			pasteIncoming: !1,
			cutIncoming: !1,
			draggingText: !1,
			highlight: new tg
		}, d && 11 > e && setTimeout(Eg(Oc, this, !0), 20), Sc(this), Xg();
		var g = this;
		yc(this, function () {
			g.curOp.forceUpdate = !0, Bf(g, c), b.autofocus && !o || Qg() == f.input ? setTimeout(Eg(ud, g), 20) : vd(g);
			for (var a in Zd)Zd.hasOwnProperty(a) && Zd[a](g, b[a], _d);
			for (var d = 0; d < de.length; ++d)de[d](g)
		})
	}

	function x(a, b) {
		var c = this, g = c.input = Lg("textarea", null, null, "position: absolute; padding: 0; width: 1px; height: 1em; outline: none");
		f ? g.style.width = "1000px" : g.setAttribute("wrap", "off"), n && (g.style.border = "1px solid black"), g.setAttribute("autocorrect", "off"), g.setAttribute("autocapitalize", "off"), g.setAttribute("spellcheck", "false"), c.inputDiv = Lg("div", [g], null, "overflow: hidden; position: relative; width: 3px; height: 0px;"), c.scrollbarH = Lg("div", [Lg("div", null, null, "height: 100%; min-height: 1px")], "CodeMirror-hscrollbar"), c.scrollbarV = Lg("div", [Lg("div", null, null, "min-width: 1px")], "CodeMirror-vscrollbar"), c.scrollbarFiller = Lg("div", null, "CodeMirror-scrollbar-filler"), c.gutterFiller = Lg("div", null, "CodeMirror-gutter-filler"), c.lineDiv = Lg("div", null, "CodeMirror-code"), c.selectionDiv = Lg("div", null, null, "position: relative; z-index: 1"), c.cursorDiv = Lg("div", null, "CodeMirror-cursors"), c.measure = Lg("div", null, "CodeMirror-measure"), c.lineMeasure = Lg("div", null, "CodeMirror-measure"), c.lineSpace = Lg("div", [c.measure, c.lineMeasure, c.selectionDiv, c.cursorDiv, c.lineDiv], null, "position: relative; outline: none"), c.mover = Lg("div", [Lg("div", [c.lineSpace], "CodeMirror-lines")], null, "position: relative"), c.sizer = Lg("div", [c.mover], "CodeMirror-sizer"), c.heightForcer = Lg("div", null, null, "position: absolute; height: " + og + "px; width: 1px;"), c.gutters = Lg("div", null, "CodeMirror-gutters"), c.lineGutter = null, c.scroller = Lg("div", [c.sizer, c.heightForcer, c.gutters], "CodeMirror-scroll"), c.scroller.setAttribute("tabIndex", "-1"), c.wrapper = Lg("div", [c.inputDiv, c.scrollbarH, c.scrollbarV, c.scrollbarFiller, c.gutterFiller, c.scroller], "CodeMirror"), d && 8 > e && (c.gutters.style.zIndex = -1, c.scroller.style.paddingRight = 0), n && (g.style.width = "0px"), f || (c.scroller.draggable = !0), k && (c.inputDiv.style.height = "1px", c.inputDiv.style.position = "absolute"), d && 8 > e && (c.scrollbarH.style.minHeight = c.scrollbarV.style.minWidth = "18px"), a.appendChild ? a.appendChild(c.wrapper) : a(c.wrapper), c.viewFrom = c.viewTo = b.first, c.view = [], c.externalMeasured = null, c.viewOffset = 0, c.lastSizeC = 0, c.updateLineNumbers = null, c.lineNumWidth = c.lineNumInnerWidth = c.lineNumChars = null, c.prevInput = "", c.alignWidgets = !1, c.pollingFast = !1, c.poll = new tg, c.cachedCharWidth = c.cachedTextHeight = c.cachedPaddingH = null, c.inaccurateSelection = !1, c.maxLine = null, c.maxLineLength = 0, c.maxLineChanged = !1, c.wheelDX = c.wheelDY = c.wheelStartX = c.wheelStartY = null, c.shift = !1, c.selForContextMenu = null
	}

	function y(a) {
		a.doc.mode = w.getMode(a.options, a.doc.modeOption), z(a)
	}

	function z(a) {
		a.doc.iter(function (a) {
			a.stateAfter && (a.stateAfter = null), a.styles && (a.styles = null)
		}), a.doc.frontier = a.doc.first, Rb(a, 100), a.state.modeGen++, a.curOp && Ec(a)
	}

	function A(a) {
		a.options.lineWrapping ? (Tg(a.display.wrapper, "CodeMirror-wrap"), a.display.sizer.style.minWidth = "") : (Sg(a.display.wrapper, "CodeMirror-wrap"), J(a)), C(a), Ec(a), hc(a), setTimeout(function () {
			N(a)
		}, 100)
	}

	function B(a) {
		var b = tc(a.display), c = a.options.lineWrapping, d = c && Math.max(5, a.display.scroller.clientWidth / uc(a.display) - 3);
		return function (e) {
			if (Te(a.doc, e))return 0;
			var f = 0;
			if (e.widgets)for (var g = 0; g < e.widgets.length; g++)e.widgets[g].height && (f += e.widgets[g].height);
			return c ? f + (Math.ceil(e.text.length / d) || 1) * b : f + b
		}
	}

	function C(a) {
		var b = a.doc, c = B(a);
		b.iter(function (a) {
			var b = c(a);
			b != a.height && Ff(a, b)
		})
	}

	function D(a) {
		var b = ie[a.options.keyMap], c = b.style;
		a.display.wrapper.className = a.display.wrapper.className.replace(/\s*cm-keymap-\S+/g, "") + (c ? " cm-keymap-" + c : "")
	}

	function E(a) {
		a.display.wrapper.className = a.display.wrapper.className.replace(/\s*cm-s-\S+/g, "") + a.options.theme.replace(/(^|\s)\s*/g, " cm-s-"), hc(a)
	}

	function F(a) {
		G(a), Ec(a), setTimeout(function () {
			P(a)
		}, 20)
	}

	function G(a) {
		var b = a.display.gutters, c = a.options.gutters;
		Ng(b);
		for (var d = 0; d < c.length; ++d) {
			var e = c[d], f = b.appendChild(Lg("div", null, "CodeMirror-gutter " + e));
			"CodeMirror-linenumbers" == e && (a.display.lineGutter = f, f.style.width = (a.display.lineNumWidth || 1) + "px")
		}
		b.style.display = d ? "" : "none", H(a)
	}

	function H(a) {
		var b = a.display.gutters.offsetWidth;
		a.display.sizer.style.marginLeft = b + "px", a.display.scrollbarH.style.left = a.options.fixedGutter ? b + "px" : 0
	}

	function I(a) {
		if (0 == a.height)return 0;
		for (var c, b = a.text.length, d = a; c = Me(d);) {
			var e = c.find(0, !0);
			d = e.from.line, b += e.from.ch - e.to.ch
		}
		for (d = a; c = Ne(d);) {
			var e = c.find(0, !0);
			b -= d.text.length - e.from.ch, d = e.to.line, b += d.text.length - e.to.ch
		}
		return b
	}

	function J(a) {
		var b = a.display, c = a.doc;
		b.maxLine = Cf(c, c.first), b.maxLineLength = I(b.maxLine), b.maxLineChanged = !0, c.iter(function (a) {
			var c = I(a);
			c > b.maxLineLength && (b.maxLineLength = c, b.maxLine = a)
		})
	}

	function K(a) {
		var b = Ag(a.gutters, "CodeMirror-linenumbers");
		-1 == b && a.lineNumbers ? a.gutters = a.gutters.concat(["CodeMirror-linenumbers"]) : b > -1 && !a.lineNumbers && (a.gutters = a.gutters.slice(0), a.gutters.splice(b, 1))
	}

	function L(a) {
		return a.display.scroller.clientHeight - a.display.wrapper.clientHeight < og - 3
	}

	function M(a) {
		var b = a.display.scroller;
		return {
			clientHeight:         b.clientHeight,
			barHeight:            a.display.scrollbarV.clientHeight,
			scrollWidth:          b.scrollWidth,
			clientWidth:          b.clientWidth,
			hScrollbarTakesSpace: L(a),
			barWidth:             a.display.scrollbarH.clientWidth,
			docHeight:            Math.round(a.doc.height + Wb(a.display))
		}
	}

	function N(a, b) {
		b || (b = M(a));
		var c = a.display, d = _g(c.measure), e = b.docHeight + og, f = b.scrollWidth > b.clientWidth;
		f && b.scrollWidth <= b.clientWidth + 1 && d > 0 && !b.hScrollbarTakesSpace && (f = !1);
		var g = e > b.clientHeight;
		if (g ? (c.scrollbarV.style.display = "block", c.scrollbarV.style.bottom = f ? d + "px" : "0", c.scrollbarV.firstChild.style.height = Math.max(0, e - b.clientHeight + (b.barHeight || c.scrollbarV.clientHeight)) + "px") : (c.scrollbarV.style.display = "", c.scrollbarV.firstChild.style.height = "0"), f ? (c.scrollbarH.style.display = "block", c.scrollbarH.style.right = g ? d + "px" : "0", c.scrollbarH.firstChild.style.width = b.scrollWidth - b.clientWidth + (b.barWidth || c.scrollbarH.clientWidth) + "px") : (c.scrollbarH.style.display = "", c.scrollbarH.firstChild.style.width = "0"), f && g ? (c.scrollbarFiller.style.display = "block", c.scrollbarFiller.style.height = c.scrollbarFiller.style.width = d + "px") : c.scrollbarFiller.style.display = "", f && a.options.coverGutterNextToScrollbar && a.options.fixedGutter ? (c.gutterFiller.style.display = "block", c.gutterFiller.style.height = d + "px", c.gutterFiller.style.width = c.gutters.offsetWidth + "px") : c.gutterFiller.style.display = "", !a.state.checkedOverlayScrollbar && b.clientHeight > 0) {
			if (0 === d) {
				var h = p && !l ? "12px" : "18px";
				c.scrollbarV.style.minWidth = c.scrollbarH.style.minHeight = h;
				var i = function (b) {
					bg(b) != c.scrollbarV && bg(b) != c.scrollbarH && zc(a, Wc)(b)
				};
				dg(c.scrollbarV, "mousedown", i), dg(c.scrollbarH, "mousedown", i)
			}
			a.state.checkedOverlayScrollbar = !0
		}
	}

	function O(a, b, c) {
		var d = c && null != c.top ? Math.max(0, c.top) : a.scroller.scrollTop;
		d = Math.floor(d - Vb(a));
		var e = c && null != c.bottom ? c.bottom : d + a.wrapper.clientHeight, f = Hf(b, d), g = Hf(b, e);
		if (c && c.ensure) {
			var h = c.ensure.from.line, i = c.ensure.to.line;
			if (f > h)return {from: h, to: Hf(b, If(Cf(b, h)) + a.wrapper.clientHeight)};
			if (Math.min(i, b.lastLine()) >= g)return {from: Hf(b, If(Cf(b, i)) - a.wrapper.clientHeight), to: i}
		}
		return {from: f, to: Math.max(g, f + 1)}
	}

	function P(a) {
		var b = a.display, c = b.view;
		if (b.alignWidgets || b.gutters.firstChild && a.options.fixedGutter) {
			for (var d = S(b) - b.scroller.scrollLeft + a.doc.scrollLeft, e = b.gutters.offsetWidth, f = d + "px", g = 0; g < c.length; g++)if (!c[g].hidden) {
				a.options.fixedGutter && c[g].gutter && (c[g].gutter.style.left = f);
				var h = c[g].alignable;
				if (h)for (var i = 0; i < h.length; i++)h[i].style.left = f
			}
			a.options.fixedGutter && (b.gutters.style.left = d + e + "px")
		}
	}

	function Q(a) {
		if (!a.options.lineNumbers)return !1;
		var b = a.doc, c = R(a.options, b.first + b.size - 1), d = a.display;
		if (c.length != d.lineNumChars) {
			var e = d.measure.appendChild(Lg("div", [Lg("div", c)], "CodeMirror-linenumber CodeMirror-gutter-elt")), f = e.firstChild.offsetWidth, g = e.offsetWidth - f;
			return d.lineGutter.style.width = "", d.lineNumInnerWidth = Math.max(f, d.lineGutter.offsetWidth - g), d.lineNumWidth = d.lineNumInnerWidth + g, d.lineNumChars = d.lineNumInnerWidth ? c.length : -1, d.lineGutter.style.width = d.lineNumWidth + "px", H(a), !0
		}
		return !1
	}

	function R(a, b) {
		return String(a.lineNumberFormatter(b + a.firstLineNumber))
	}

	function S(a) {
		return a.scroller.getBoundingClientRect().left - a.sizer.getBoundingClientRect().left
	}

	function T(a, b, c) {
		for (var g, d = a.display.viewFrom, e = a.display.viewTo, h = O(a.display, a.doc, b), i = !0; ; i = !1) {
			var j = a.display.scroller.clientWidth;
			if (!U(a, h, c))break;
			g = !0, a.display.maxLineChanged && !a.options.lineWrapping && V(a);
			var k = M(a);
			if (Nb(a), W(a, k), N(a, k), f && a.options.lineWrapping && X(a, k), f && k.scrollWidth > k.clientWidth && k.scrollWidth < k.clientWidth + 1 && !L(a) && N(a), i && a.options.lineWrapping && j != a.display.scroller.clientWidth)c = !0; else if (c = !1, b && null != b.top && (b = {top: Math.min(k.docHeight - og - k.clientHeight, b.top)}), h = O(a.display, a.doc, b), h.from >= a.display.viewFrom && h.to <= a.display.viewTo)break
		}
		return a.display.updateLineNumbers = null, g && (ig(a, "update", a), (a.display.viewFrom != d || a.display.viewTo != e) && ig(a, "viewportChange", a, a.display.viewFrom, a.display.viewTo)), g
	}

	function U(a, b, c) {
		var d = a.display, e = a.doc;
		if (!d.wrapper.offsetWidth)return Gc(a), void 0;
		if (!(!c && b.from >= d.viewFrom && b.to <= d.viewTo && 0 == Kc(a))) {
			Q(a) && Gc(a);
			var f = $(a), g = e.first + e.size, h = Math.max(b.from - a.options.viewportMargin, e.first), i = Math.min(g, b.to + a.options.viewportMargin);
			d.viewFrom < h && h - d.viewFrom < 20 && (h = Math.max(e.first, d.viewFrom)), d.viewTo > i && d.viewTo - i < 20 && (i = Math.min(g, d.viewTo)), v && (h = Re(a.doc, h), i = Se(a.doc, i));
			var j = h != d.viewFrom || i != d.viewTo || d.lastSizeC != d.wrapper.clientHeight;
			Jc(a, h, i), d.viewOffset = If(Cf(a.doc, d.viewFrom)), a.display.mover.style.top = d.viewOffset + "px";
			var k = Kc(a);
			if (j || 0 != k || c) {
				var l = Qg();
				return k > 4 && (d.lineDiv.style.display = "none"), _(a, d.updateLineNumbers, f), k > 4 && (d.lineDiv.style.display = ""), l && Qg() != l && l.offsetHeight && l.focus(), Ng(d.cursorDiv), Ng(d.selectionDiv), j && (d.lastSizeC = d.wrapper.clientHeight, Rb(a, 400)), Y(a), !0
			}
		}
	}

	function V(a) {
		var b = a.display, c = _b(a, b.maxLine, b.maxLine.text.length).left;
		b.maxLineChanged = !1;
		var d = Math.max(0, c + 3), e = Math.max(0, b.sizer.offsetLeft + d + og - b.scroller.clientWidth);
		b.sizer.style.minWidth = d + "px", e < a.doc.scrollLeft && gd(a, Math.min(b.scroller.scrollLeft, e), !0)
	}

	function W(a, b) {
		a.display.sizer.style.minHeight = a.display.heightForcer.style.top = b.docHeight + "px", a.display.gutters.style.height = Math.max(b.docHeight, b.clientHeight - og) + "px"
	}

	function X(a, b) {
		a.display.sizer.offsetWidth + a.display.gutters.offsetWidth < a.display.scroller.clientWidth - 1 && (a.display.sizer.style.minHeight = a.display.heightForcer.style.top = "0px", a.display.gutters.style.height = b.docHeight + "px")
	}

	function Y(a) {
		for (var b = a.display, c = b.lineDiv.offsetTop, f = 0; f < b.view.length; f++) {
			var h, g = b.view[f];
			if (!g.hidden) {
				if (d && 8 > e) {
					var i = g.node.offsetTop + g.node.offsetHeight;
					h = i - c, c = i
				} else {
					var j = g.node.getBoundingClientRect();
					h = j.bottom - j.top
				}
				var k = g.line.height - h;
				if (2 > h && (h = tc(b)), (k > .001 || -.001 > k) && (Ff(g.line, h), Z(g.line), g.rest))for (var l = 0; l < g.rest.length; l++)Z(g.rest[l])
			}
		}
	}

	function Z(a) {
		if (a.widgets)for (var b = 0; b < a.widgets.length; ++b)a.widgets[b].height = a.widgets[b].node.offsetHeight
	}

	function $(a) {
		for (var b = a.display, c = {}, d = {}, e = b.gutters.firstChild, f = 0; e; e = e.nextSibling, ++f)c[a.options.gutters[f]] = e.offsetLeft, d[a.options.gutters[f]] = e.offsetWidth;
		return {
			fixedPos:         S(b),
			gutterTotalWidth: b.gutters.offsetWidth,
			gutterLeft:       c,
			gutterWidth:      d,
			wrapperWidth:     b.wrapper.clientWidth
		}
	}

	function _(a, b, c) {
		function i(b) {
			var c = b.nextSibling;
			return f && p && a.display.currentWheelTarget == b ? b.style.display = "none" : b.parentNode.removeChild(b), c
		}

		for (var d = a.display, e = a.options.lineNumbers, g = d.lineDiv, h = g.firstChild, j = d.view, k = d.viewFrom, l = 0; l < j.length; l++) {
			var m = j[l];
			if (m.hidden); else if (m.node) {
				for (; h != m.node;)h = i(h);
				var o = e && null != b && k >= b && m.lineNumber;
				m.changes && (Ag(m.changes, "gutter") > -1 && (o = !1), ab(a, m, k, c)), o && (Ng(m.lineNumber), m.lineNumber.appendChild(document.createTextNode(R(a.options, k)))), h = m.node.nextSibling
			} else {
				var n = ib(a, m, k, c);
				g.insertBefore(n, h)
			}
			k += m.size
		}
		for (; h;)h = i(h)
	}

	function ab(a, b, c, d) {
		for (var e = 0; e < b.changes.length; e++) {
			var f = b.changes[e];
			"text" == f ? eb(a, b) : "gutter" == f ? gb(a, b, c, d) : "class" == f ? fb(b) : "widget" == f && hb(b, d)
		}
		b.changes = null
	}

	function bb(a) {
		return a.node == a.text && (a.node = Lg("div", null, null, "position: relative"), a.text.parentNode && a.text.parentNode.replaceChild(a.node, a.text), a.node.appendChild(a.text), d && 8 > e && (a.node.style.zIndex = 2)), a.node
	}

	function cb(a) {
		var b = a.bgClass ? a.bgClass + " " + (a.line.bgClass || "") : a.line.bgClass;
		if (b && (b += " CodeMirror-linebackground"), a.background)b ? a.background.className = b : (a.background.parentNode.removeChild(a.background), a.background = null); else if (b) {
			var c = bb(a);
			a.background = c.insertBefore(Lg("div", null, b), c.firstChild)
		}
	}

	function db(a, b) {
		var c = a.display.externalMeasured;
		return c && c.line == b.line ? (a.display.externalMeasured = null, b.measure = c.measure, c.built) : lf(a, b)
	}

	function eb(a, b) {
		var c = b.text.className, d = db(a, b);
		b.text == b.node && (b.node = d.pre), b.text.parentNode.replaceChild(d.pre, b.text), b.text = d.pre, d.bgClass != b.bgClass || d.textClass != b.textClass ? (b.bgClass = d.bgClass, b.textClass = d.textClass, fb(b)) : c && (b.text.className = c)
	}

	function fb(a) {
		cb(a), a.line.wrapClass ? bb(a).className = a.line.wrapClass : a.node != a.text && (a.node.className = "");
		var b = a.textClass ? a.textClass + " " + (a.line.textClass || "") : a.line.textClass;
		a.text.className = b || ""
	}

	function gb(a, b, c, d) {
		b.gutter && (b.node.removeChild(b.gutter), b.gutter = null);
		var e = b.line.gutterMarkers;
		if (a.options.lineNumbers || e) {
			var f = bb(b), g = b.gutter = f.insertBefore(Lg("div", null, "CodeMirror-gutter-wrapper", "position: absolute; left: " + (a.options.fixedGutter ? d.fixedPos : -d.gutterTotalWidth) + "px"), b.text);
			if (!a.options.lineNumbers || e && e["CodeMirror-linenumbers"] || (b.lineNumber = g.appendChild(Lg("div", R(a.options, c), "CodeMirror-linenumber CodeMirror-gutter-elt", "left: " + d.gutterLeft["CodeMirror-linenumbers"] + "px; width: " + a.display.lineNumInnerWidth + "px"))), e)for (var h = 0; h < a.options.gutters.length; ++h) {
				var i = a.options.gutters[h], j = e.hasOwnProperty(i) && e[i];
				j && g.appendChild(Lg("div", [j], "CodeMirror-gutter-elt", "left: " + d.gutterLeft[i] + "px; width: " + d.gutterWidth[i] + "px"))
			}
		}
	}

	function hb(a, b) {
		a.alignable && (a.alignable = null);
		for (var d, c = a.node.firstChild; c; c = d) {
			var d = c.nextSibling;
			"CodeMirror-linewidget" == c.className && a.node.removeChild(c)
		}
		jb(a, b)
	}

	function ib(a, b, c, d) {
		var e = db(a, b);
		return b.text = b.node = e.pre, e.bgClass && (b.bgClass = e.bgClass), e.textClass && (b.textClass = e.textClass), fb(b), gb(a, b, c, d), jb(b, d), b.node
	}

	function jb(a, b) {
		if (kb(a.line, a, b, !0), a.rest)for (var c = 0; c < a.rest.length; c++)kb(a.rest[c], a, b, !1)
	}

	function kb(a, b, c, d) {
		if (a.widgets)for (var e = bb(b), f = 0, g = a.widgets; f < g.length; ++f) {
			var h = g[f], i = Lg("div", [h.node], "CodeMirror-linewidget");
			h.handleMouseEvents || (i.ignoreEvents = !0), lb(h, i, b, c), d && h.above ? e.insertBefore(i, b.gutter || b.text) : e.appendChild(i), ig(h, "redraw")
		}
	}

	function lb(a, b, c, d) {
		if (a.noHScroll) {
			(c.alignable || (c.alignable = [])).push(b);
			var e = d.wrapperWidth;
			b.style.left = d.fixedPos + "px", a.coverGutter || (e -= d.gutterTotalWidth, b.style.paddingLeft = d.gutterTotalWidth + "px"), b.style.width = e + "px"
		}
		a.coverGutter && (b.style.zIndex = 5, b.style.position = "relative", a.noHScroll || (b.style.marginLeft = -d.gutterTotalWidth + "px"))
	}

	function ob(a) {
		return mb(a.line, a.ch)
	}

	function pb(a, b) {
		return nb(a, b) < 0 ? b : a
	}

	function qb(a, b) {
		return nb(a, b) < 0 ? a : b
	}

	function rb(a, b) {
		this.ranges = a, this.primIndex = b
	}

	function sb(a, b) {
		this.anchor = a, this.head = b
	}

	function tb(a, b) {
		var c = a[b];
		a.sort(function (a, b) {
			return nb(a.from(), b.from())
		}), b = Ag(a, c);
		for (var d = 1; d < a.length; d++) {
			var e = a[d], f = a[d - 1];
			if (nb(f.to(), e.from()) >= 0) {
				var g = qb(f.from(), e.from()), h = pb(f.to(), e.to()), i = f.empty() ? e.from() == e.head : f.from() == f.head;
				b >= d && --b, a.splice(--d, 2, new sb(i ? h : g, i ? g : h))
			}
		}
		return new rb(a, b)
	}

	function ub(a, b) {
		return new rb([new sb(a, b || a)], 0)
	}

	function vb(a, b) {
		return Math.max(a.first, Math.min(b, a.first + a.size - 1))
	}

	function wb(a, b) {
		if (b.line < a.first)return mb(a.first, 0);
		var c = a.first + a.size - 1;
		return b.line > c ? mb(c, Cf(a, c).text.length) : xb(b, Cf(a, b.line).text.length)
	}

	function xb(a, b) {
		var c = a.ch;
		return null == c || c > b ? mb(a.line, b) : 0 > c ? mb(a.line, 0) : a
	}

	function yb(a, b) {
		return b >= a.first && b < a.first + a.size
	}

	function zb(a, b) {
		for (var c = [], d = 0; d < b.length; d++)c[d] = wb(a, b[d]);
		return c
	}

	function Ab(a, b, c, d) {
		if (a.cm && a.cm.display.shift || a.extend) {
			var e = b.anchor;
			if (d) {
				var f = nb(c, e) < 0;
				f != nb(d, e) < 0 ? (e = c, c = d) : f != nb(c, d) < 0 && (c = d)
			}
			return new sb(e, c)
		}
		return new sb(d || c, c)
	}

	function Bb(a, b, c, d) {
		Hb(a, new rb([Ab(a, a.sel.primary(), b, c)], 0), d)
	}

	function Cb(a, b, c) {
		for (var d = [], e = 0; e < a.sel.ranges.length; e++)d[e] = Ab(a, a.sel.ranges[e], b[e], null);
		var f = tb(d, a.sel.primIndex);
		Hb(a, f, c)
	}

	function Db(a, b, c, d) {
		var e = a.sel.ranges.slice(0);
		e[b] = c, Hb(a, tb(e, a.sel.primIndex), d)
	}

	function Eb(a, b, c, d) {
		Hb(a, ub(b, c), d)
	}

	function Fb(a, b) {
		var c = {
			ranges: b.ranges, update: function (b) {
				this.ranges = [];
				for (var c = 0; c < b.length; c++)this.ranges[c] = new sb(wb(a, b[c].anchor), wb(a, b[c].head))
			}
		};
		return fg(a, "beforeSelectionChange", a, c), a.cm && fg(a.cm, "beforeSelectionChange", a.cm, c), c.ranges != b.ranges ? tb(c.ranges, c.ranges.length - 1) : b
	}

	function Gb(a, b, c) {
		var d = a.history.done, e = yg(d);
		e && e.ranges ? (d[d.length - 1] = b, Ib(a, b, c)) : Hb(a, b, c)
	}

	function Hb(a, b, c) {
		Ib(a, b, c), Qf(a, a.sel, a.cm ? a.cm.curOp.id : 0 / 0, c)
	}

	function Ib(a, b, c) {
		(mg(a, "beforeSelectionChange") || a.cm && mg(a.cm, "beforeSelectionChange")) && (b = Fb(a, b));
		var d = c && c.bias || (nb(b.primary().head, a.sel.primary().head) < 0 ? -1 : 1);
		Jb(a, Lb(a, b, d, !0)), c && c.scroll === !1 || !a.cm || Qd(a.cm)
	}

	function Jb(a, b) {
		b.equals(a.sel) || (a.sel = b, a.cm && (a.cm.curOp.updateInput = a.cm.curOp.selectionChanged = !0, lg(a.cm)), ig(a, "cursorActivity", a))
	}

	function Kb(a) {
		Jb(a, Lb(a, a.sel, null, !1), qg)
	}

	function Lb(a, b, c, d) {
		for (var e, f = 0; f < b.ranges.length; f++) {
			var g = b.ranges[f], h = Mb(a, g.anchor, c, d), i = Mb(a, g.head, c, d);
			(e || h != g.anchor || i != g.head) && (e || (e = b.ranges.slice(0, f)), e[f] = new sb(h, i))
		}
		return e ? tb(e, b.primIndex) : b
	}

	function Mb(a, b, c, d) {
		var e = !1, f = b, g = c || 1;
		a.cantEdit = !1;
		a:for (; ;) {
			var h = Cf(a, f.line);
			if (h.markedSpans)for (var i = 0; i < h.markedSpans.length; ++i) {
				var j = h.markedSpans[i], k = j.marker;
				if ((null == j.from || (k.inclusiveLeft ? j.from <= f.ch : j.from < f.ch)) && (null == j.to || (k.inclusiveRight ? j.to >= f.ch : j.to > f.ch))) {
					if (d && (fg(k, "beforeCursorEnter"), k.explicitlyCleared)) {
						if (h.markedSpans) {
							--i;
							continue
						}
						break
					}
					if (!k.atomic)continue;
					var l = k.find(0 > g ? -1 : 1);
					if (0 == nb(l, f) && (l.ch += g, l.ch < 0 ? l = l.line > a.first ? wb(a, mb(l.line - 1)) : null : l.ch > h.text.length && (l = l.line < a.first + a.size - 1 ? mb(l.line + 1, 0) : null), !l)) {
						if (e)return d ? (a.cantEdit = !0, mb(a.first, 0)) : Mb(a, b, c, !0);
						e = !0, l = b, g = -g
					}
					f = l;
					continue a
				}
			}
			return f
		}
	}

	function Nb(a) {
		for (var b = a.display, c = a.doc, d = document.createDocumentFragment(), e = document.createDocumentFragment(), f = 0; f < c.sel.ranges.length; f++) {
			var g = c.sel.ranges[f], h = g.empty();
			(h || a.options.showCursorWhenSelecting) && Ob(a, g, d), h || Pb(a, g, e)
		}
		if (a.options.moveInputWithCursor) {
			var i = nc(a, c.sel.primary().head, "div"), j = b.wrapper.getBoundingClientRect(), k = b.lineDiv.getBoundingClientRect(), l = Math.max(0, Math.min(b.wrapper.clientHeight - 10, i.top + k.top - j.top)), m = Math.max(0, Math.min(b.wrapper.clientWidth - 10, i.left + k.left - j.left));
			b.inputDiv.style.top = l + "px", b.inputDiv.style.left = m + "px"
		}
		Og(b.cursorDiv, d), Og(b.selectionDiv, e)
	}

	function Ob(a, b, c) {
		var d = nc(a, b.head, "div", null, null, !a.options.singleCursorHeightPerLine), e = c.appendChild(Lg("div", "\xa0", "CodeMirror-cursor"));
		if (e.style.left = d.left + "px", e.style.top = d.top + "px", e.style.height = Math.max(0, d.bottom - d.top) * a.options.cursorHeight + "px", d.other) {
			var f = c.appendChild(Lg("div", "\xa0", "CodeMirror-cursor CodeMirror-secondarycursor"));
			f.style.display = "", f.style.left = d.other.left + "px", f.style.top = d.other.top + "px", f.style.height = .85 * (d.other.bottom - d.other.top) + "px"
		}
	}

	function Pb(a, b, c) {
		function j(a, b, c, d) {
			0 > b && (b = 0), b = Math.round(b), d = Math.round(d), f.appendChild(Lg("div", null, "CodeMirror-selected", "position: absolute; left: " + a + "px; top: " + b + "px; width: " + (null == c ? i - a : c) + "px; height: " + (d - b) + "px"))
		}

		function k(b, c, d) {
			function m(c, d) {
				return mc(a, mb(b, c), "div", f, d)
			}

			var k, l, f = Cf(e, b), g = f.text.length;
			return ih(Jf(f), c || 0, null == d ? g : d, function (a, b, e) {
				var n, o, p, f = m(a, "left");
				if (a == b)n = f, o = p = f.left; else {
					if (n = m(b - 1, "right"), "rtl" == e) {
						var q = f;
						f = n, n = q
					}
					o = f.left, p = n.right
				}
				null == c && 0 == a && (o = h), n.top - f.top > 3 && (j(o, f.top, null, f.bottom), o = h, f.bottom < n.top && j(o, f.bottom, null, n.top)), null == d && b == g && (p = i), (!k || f.top < k.top || f.top == k.top && f.left < k.left) && (k = f), (!l || n.bottom > l.bottom || n.bottom == l.bottom && n.right > l.right) && (l = n), h + 1 > o && (o = h), j(o, n.top, p - o, n.bottom)
			}), {start: k, end: l}
		}

		var d = a.display, e = a.doc, f = document.createDocumentFragment(), g = Xb(a.display), h = g.left, i = d.lineSpace.offsetWidth - g.right, l = b.from(), m = b.to();
		if (l.line == m.line)k(l.line, l.ch, m.ch); else {
			var n = Cf(e, l.line), o = Cf(e, m.line), p = Pe(n) == Pe(o), q = k(l.line, l.ch, p ? n.text.length + 1 : null).end, r = k(m.line, p ? 0 : null, m.ch).start;
			p && (q.top < r.top - 2 ? (j(q.right, q.top, null, q.bottom), j(h, r.top, r.left, r.bottom)) : j(q.right, q.top, r.left - q.right, q.bottom)), q.bottom < r.top && j(h, q.bottom, null, r.top)
		}
		c.appendChild(f)
	}

	function Qb(a) {
		if (a.state.focused) {
			var b = a.display;
			clearInterval(b.blinker);
			var c = !0;
			b.cursorDiv.style.visibility = "", a.options.cursorBlinkRate > 0 ? b.blinker = setInterval(function () {
				b.cursorDiv.style.visibility = (c = !c) ? "" : "hidden"
			}, a.options.cursorBlinkRate) : a.options.cursorBlinkRate < 0 && (b.cursorDiv.style.visibility = "hidden")
		}
	}

	function Rb(a, b) {
		a.doc.mode.startState && a.doc.frontier < a.display.viewTo && a.state.highlight.set(b, Eg(Sb, a))
	}

	function Sb(a) {
		var b = a.doc;
		if (b.frontier < b.first && (b.frontier = b.first), !(b.frontier >= a.display.viewTo)) {
			var c = +new Date + a.options.workTime, d = fe(b.mode, Ub(a, b.frontier));
			yc(a, function () {
				b.iter(b.frontier, Math.min(b.first + b.size, a.display.viewTo + 500), function (e) {
					if (b.frontier >= a.display.viewFrom) {
						var f = e.styles, g = ef(a, e, d, !0);
						e.styles = g.styles, g.classes ? e.styleClasses = g.classes : e.styleClasses && (e.styleClasses = null);
						for (var h = !f || f.length != e.styles.length, i = 0; !h && i < f.length; ++i)h = f[i] != e.styles[i];
						h && Fc(a, b.frontier, "text"), e.stateAfter = fe(b.mode, d)
					} else gf(a, e.text, d), e.stateAfter = 0 == b.frontier % 5 ? fe(b.mode, d) : null;
					return ++b.frontier, +new Date > c ? (Rb(a, a.options.workDelay), !0) : void 0
				})
			})
		}
	}

	function Tb(a, b, c) {
		for (var d, e, f = a.doc, g = c ? -1 : b - (a.doc.mode.innerMode ? 1e3 : 100), h = b; h > g; --h) {
			if (h <= f.first)return f.first;
			var i = Cf(f, h - 1);
			if (i.stateAfter && (!c || h <= f.frontier))return h;
			var j = ug(i.text, null, a.options.tabSize);
			(null == e || d > j) && (e = h - 1, d = j)
		}
		return e
	}

	function Ub(a, b, c) {
		var d = a.doc, e = a.display;
		if (!d.mode.startState)return !0;
		var f = Tb(a, b, c), g = f > d.first && Cf(d, f - 1).stateAfter;
		return g = g ? fe(d.mode, g) : ge(d.mode), d.iter(f, b, function (c) {
			gf(a, c.text, g);
			var h = f == b - 1 || 0 == f % 5 || f >= e.viewFrom && f < e.viewTo;
			c.stateAfter = h ? fe(d.mode, g) : null, ++f
		}), c && (d.frontier = f), g
	}

	function Vb(a) {
		return a.lineSpace.offsetTop
	}

	function Wb(a) {
		return a.mover.offsetHeight - a.lineSpace.offsetHeight
	}

	function Xb(a) {
		if (a.cachedPaddingH)return a.cachedPaddingH;
		var b = Og(a.measure, Lg("pre", "x")), c = window.getComputedStyle ? window.getComputedStyle(b) : b.currentStyle, d = {
			left:  parseInt(c.paddingLeft),
			right: parseInt(c.paddingRight)
		};
		return isNaN(d.left) || isNaN(d.right) || (a.cachedPaddingH = d), d
	}

	function Yb(a, b, c) {
		var d = a.options.lineWrapping, e = d && a.display.scroller.clientWidth;
		if (!b.measure.heights || d && b.measure.width != e) {
			var f = b.measure.heights = [];
			if (d) {
				b.measure.width = e;
				for (var g = b.text.firstChild.getClientRects(), h = 0; h < g.length - 1; h++) {
					var i = g[h], j = g[h + 1];
					Math.abs(i.bottom - j.bottom) > 2 && f.push((i.bottom + j.top) / 2 - c.top)
				}
			}
			f.push(c.bottom - c.top)
		}
	}

	function Zb(a, b, c) {
		if (a.line == b)return {map: a.measure.map, cache: a.measure.cache};
		for (var d = 0; d < a.rest.length; d++)if (a.rest[d] == b)return {
			map:   a.measure.maps[d],
			cache: a.measure.caches[d]
		};
		for (var d = 0; d < a.rest.length; d++)if (Gf(a.rest[d]) > c)return {
			map:    a.measure.maps[d],
			cache:  a.measure.caches[d],
			before: !0
		}
	}

	function $b(a, b) {
		b = Pe(b);
		var c = Gf(b), d = a.display.externalMeasured = new Cc(a.doc, b, c);
		d.lineN = c;
		var e = d.built = lf(a, d);
		return d.text = e.pre, Og(a.display.lineMeasure, e.pre), d
	}

	function _b(a, b, c, d) {
		return cc(a, bc(a, b), c, d)
	}

	function ac(a, b) {
		if (b >= a.display.viewFrom && b < a.display.viewTo)return a.display.view[Hc(a, b)];
		var c = a.display.externalMeasured;
		return c && b >= c.lineN && b < c.lineN + c.size ? c : void 0
	}

	function bc(a, b) {
		var c = Gf(b), d = ac(a, c);
		d && !d.text ? d = null : d && d.changes && ab(a, d, c, $(a)), d || (d = $b(a, b));
		var e = Zb(d, b, c);
		return {line: b, view: d, rect: null, map: e.map, cache: e.cache, before: e.before, hasHeights: !1}
	}

	function cc(a, b, c, d, e) {
		b.before && (c = -1);
		var g, f = c + (d || "");
		return b.cache.hasOwnProperty(f) ? g = b.cache[f] : (b.rect || (b.rect = b.view.text.getBoundingClientRect()), b.hasHeights || (Yb(a, b.view, b.rect), b.hasHeights = !0), g = ec(a, b, c, d), g.bogus || (b.cache[f] = g)), {
			left: g.left,
			right: g.right,
			top: e ? g.rtop : g.top,
			bottom: e ? g.rbottom : g.bottom
		}
	}

	function ec(a, b, c, f) {
		for (var h, i, j, k, g = b.map, l = 0; l < g.length; l += 3) {
			var m = g[l], n = g[l + 1];
			if (m > c ? (i = 0, j = 1, k = "left") : n > c ? (i = c - m, j = i + 1) : (l == g.length - 3 || c == n && g[l + 3] > c) && (j = n - m, i = j - 1, c >= n && (k = "right")), null != i) {
				if (h = g[l + 2], m == n && f == (h.insertLeft ? "left" : "right") && (k = f), "left" == f && 0 == i)for (; l && g[l - 2] == g[l - 3] && g[l - 1].insertLeft;)h = g[(l -= 3) + 2], k = "left";
				if ("right" == f && i == n - m)for (; l < g.length - 3 && g[l + 3] == g[l + 4] && !g[l + 5].insertLeft;)h = g[(l += 3) + 2], k = "right";
				break
			}
		}
		var o;
		if (3 == h.nodeType) {
			for (; i && Kg(b.line.text.charAt(m + i));)--i;
			for (; n > m + j && Kg(b.line.text.charAt(m + j));)++j;
			if (d && 9 > e && 0 == i && j == n - m)o = h.parentNode.getBoundingClientRect(); else if (d && a.options.lineWrapping) {
				var p = Mg(h, i, j).getClientRects();
				o = p.length ? p["right" == f ? p.length - 1 : 0] : dc
			} else o = Mg(h, i, j).getBoundingClientRect() || dc
		} else {
			i > 0 && (k = f = "right");
			var p;
			o = a.options.lineWrapping && (p = h.getClientRects()).length > 1 ? p["right" == f ? p.length - 1 : 0] : h.getBoundingClientRect()
		}
		if (d && 9 > e && !i && (!o || !o.left && !o.right)) {
			var q = h.parentNode.getClientRects()[0];
			o = q ? {left: q.left, right: q.left + uc(a.display), top: q.top, bottom: q.bottom} : dc
		}
		for (var r = o.top - b.rect.top, s = o.bottom - b.rect.top, t = (r + s) / 2, u = b.view.measure.heights, l = 0; l < u.length - 1 && !(t < u[l]); l++);
		var v = l ? u[l - 1] : 0, w = u[l], x = {
			left:   ("right" == k ? o.right : o.left) - b.rect.left,
			right:  ("left" == k ? o.left : o.right) - b.rect.left,
			top:    v,
			bottom: w
		};
		return o.left || o.right || (x.bogus = !0), a.options.singleCursorHeightPerLine || (x.rtop = r, x.rbottom = s), x
	}

	function fc(a) {
		if (a.measure && (a.measure.cache = {}, a.measure.heights = null, a.rest))for (var b = 0; b < a.rest.length; b++)a.measure.caches[b] = {}
	}

	function gc(a) {
		a.display.externalMeasure = null, Ng(a.display.lineMeasure);
		for (var b = 0; b < a.display.view.length; b++)fc(a.display.view[b])
	}

	function hc(a) {
		gc(a), a.display.cachedCharWidth = a.display.cachedTextHeight = a.display.cachedPaddingH = null, a.options.lineWrapping || (a.display.maxLineChanged = !0), a.display.lineNumChars = null
	}

	function ic() {
		return window.pageXOffset || (document.documentElement || document.body).scrollLeft
	}

	function jc() {
		return window.pageYOffset || (document.documentElement || document.body).scrollTop
	}

	function kc(a, b, c, d) {
		if (b.widgets)for (var e = 0; e < b.widgets.length; ++e)if (b.widgets[e].above) {
			var f = Xe(b.widgets[e]);
			c.top += f, c.bottom += f
		}
		if ("line" == d)return c;
		d || (d = "local");
		var g = If(b);
		if ("local" == d ? g += Vb(a.display) : g -= a.display.viewOffset, "page" == d || "window" == d) {
			var h = a.display.lineSpace.getBoundingClientRect();
			g += h.top + ("window" == d ? 0 : jc());
			var i = h.left + ("window" == d ? 0 : ic());
			c.left += i, c.right += i
		}
		return c.top += g, c.bottom += g, c
	}

	function lc(a, b, c) {
		if ("div" == c)return b;
		var d = b.left, e = b.top;
		if ("page" == c)d -= ic(), e -= jc(); else if ("local" == c || !c) {
			var f = a.display.sizer.getBoundingClientRect();
			d += f.left, e += f.top
		}
		var g = a.display.lineSpace.getBoundingClientRect();
		return {left: d - g.left, top: e - g.top}
	}

	function mc(a, b, c, d, e) {
		return d || (d = Cf(a.doc, b.line)), kc(a, d, _b(a, d, b.ch, e), c)
	}

	function nc(a, b, c, d, e, f) {
		function g(b, g) {
			var h = cc(a, e, b, g ? "right" : "left", f);
			return g ? h.left = h.right : h.right = h.left, kc(a, d, h, c)
		}

		function h(a, b) {
			var c = i[b], d = c.level % 2;
			return a == jh(c) && b && c.level < i[b - 1].level ? (c = i[--b], a = kh(c) - (c.level % 2 ? 0 : 1), d = !0) : a == kh(c) && b < i.length - 1 && c.level < i[b + 1].level && (c = i[++b], a = jh(c) - c.level % 2, d = !1), d && a == c.to && a > c.from ? g(a - 1) : g(a, d)
		}

		d = d || Cf(a.doc, b.line), e || (e = bc(a, d));
		var i = Jf(d), j = b.ch;
		if (!i)return g(j);
		var k = rh(i, j), l = h(j, k);
		return null != qh && (l.other = h(j, qh)), l
	}

	function oc(a, b) {
		var c = 0, b = wb(a.doc, b);
		a.options.lineWrapping || (c = uc(a.display) * b.ch);
		var d = Cf(a.doc, b.line), e = If(d) + Vb(a.display);
		return {left: c, right: c, top: e, bottom: e + d.height}
	}

	function pc(a, b, c, d) {
		var e = mb(a, b);
		return e.xRel = d, c && (e.outside = !0), e
	}

	function qc(a, b, c) {
		var d = a.doc;
		if (c += a.display.viewOffset, 0 > c)return pc(d.first, 0, !0, -1);
		var e = Hf(d, c), f = d.first + d.size - 1;
		if (e > f)return pc(d.first + d.size - 1, Cf(d, f).text.length, !0, 1);
		0 > b && (b = 0);
		for (var g = Cf(d, e); ;) {
			var h = rc(a, g, e, b, c), i = Ne(g), j = i && i.find(0, !0);
			if (!i || !(h.ch > j.from.ch || h.ch == j.from.ch && h.xRel > 0))return h;
			e = Gf(g = j.to.line)
		}
	}

	function rc(a, b, c, d, e) {
		function j(d) {
			var e = nc(a, mb(c, d), "line", b, i);
			return g = !0, f > e.bottom ? e.left - h : f < e.top ? e.left + h : (g = !1, e.left)
		}

		var f = e - If(b), g = !1, h = 2 * a.display.wrapper.clientWidth, i = bc(a, b), k = Jf(b), l = b.text.length, m = lh(b), n = mh(b), o = j(m), p = g, q = j(n), r = g;
		if (d > q)return pc(c, n, r, 1);
		for (; ;) {
			if (k ? n == m || n == th(b, m, 1) : 1 >= n - m) {
				for (var s = o > d || q - d >= d - o ? m : n, t = d - (s == m ? o : q); Kg(b.text.charAt(s));)++s;
				var u = pc(c, s, s == m ? p : r, -1 > t ? -1 : t > 1 ? 1 : 0);
				return u
			}
			var v = Math.ceil(l / 2), w = m + v;
			if (k) {
				w = m;
				for (var x = 0; v > x; ++x)w = th(b, w, 1)
			}
			var y = j(w);
			y > d ? (n = w, q = y, (r = g) && (q += 1e3), l = v) : (m = w, o = y, p = g, l -= v)
		}
	}

	function tc(a) {
		if (null != a.cachedTextHeight)return a.cachedTextHeight;
		if (null == sc) {
			sc = Lg("pre");
			for (var b = 0; 49 > b; ++b)sc.appendChild(document.createTextNode("x")), sc.appendChild(Lg("br"));
			sc.appendChild(document.createTextNode("x"))
		}
		Og(a.measure, sc);
		var c = sc.offsetHeight / 50;
		return c > 3 && (a.cachedTextHeight = c), Ng(a.measure), c || 1
	}

	function uc(a) {
		if (null != a.cachedCharWidth)return a.cachedCharWidth;
		var b = Lg("span", "xxxxxxxxxx"), c = Lg("pre", [b]);
		Og(a.measure, c);
		var d = b.getBoundingClientRect(), e = (d.right - d.left) / 10;
		return e > 2 && (a.cachedCharWidth = e), e || 10
	}

	function wc(a) {
		a.curOp = {
			viewChanged:            !1,
			startHeight:            a.doc.height,
			forceUpdate:            !1,
			updateInput:            null,
			typing:                 !1,
			changeObjs:             null,
			cursorActivityHandlers: null,
			selectionChanged:       !1,
			updateMaxLine:          !1,
			scrollLeft:             null,
			scrollTop:              null,
			scrollToPos:            null,
			id:                     ++vc
		}, hg++ || (gg = [])
	}

	function xc(a) {
		var b = a.curOp, c = a.doc, d = a.display;
		if (a.curOp = null, b.updateMaxLine && J(a), b.viewChanged || b.forceUpdate || null != b.scrollTop || b.scrollToPos && (b.scrollToPos.from.line < d.viewFrom || b.scrollToPos.to.line >= d.viewTo) || d.maxLineChanged && a.options.lineWrapping) {
			var e = T(a, {top: b.scrollTop, ensure: b.scrollToPos}, b.forceUpdate);
			a.display.scroller.offsetHeight && (a.doc.scrollTop = a.display.scroller.scrollTop)
		}
		if (!e && b.selectionChanged && Nb(a), e || b.startHeight == a.doc.height || N(a), null == d.wheelStartX || null == b.scrollTop && null == b.scrollLeft && !b.scrollToPos || (d.wheelStartX = d.wheelStartY = null), null != b.scrollTop && d.scroller.scrollTop != b.scrollTop) {
			var f = Math.max(0, Math.min(d.scroller.scrollHeight - d.scroller.clientHeight, b.scrollTop));
			d.scroller.scrollTop = d.scrollbarV.scrollTop = c.scrollTop = f
		}
		if (null != b.scrollLeft && d.scroller.scrollLeft != b.scrollLeft) {
			var g = Math.max(0, Math.min(d.scroller.scrollWidth - d.scroller.clientWidth, b.scrollLeft));
			d.scroller.scrollLeft = d.scrollbarH.scrollLeft = c.scrollLeft = g, P(a)
		}
		if (b.scrollToPos) {
			var h = Md(a, wb(a.doc, b.scrollToPos.from), wb(a.doc, b.scrollToPos.to), b.scrollToPos.margin);
			b.scrollToPos.isCursor && a.state.focused && Ld(a, h)
		}
		b.selectionChanged && Qb(a), a.state.focused && b.updateInput && Oc(a, b.typing);
		var i = b.maybeHiddenMarkers, j = b.maybeUnhiddenMarkers;
		if (i)for (var k = 0; k < i.length; ++k)i[k].lines.length || fg(i[k], "hide");
		if (j)for (var k = 0; k < j.length; ++k)j[k].lines.length && fg(j[k], "unhide");
		var l;
		if (--hg || (l = gg, gg = null), b.changeObjs && fg(a, "changes", a, b.changeObjs), l)for (var k = 0; k < l.length; ++k)l[k]();
		if (b.cursorActivityHandlers)for (var k = 0; k < b.cursorActivityHandlers.length; k++)b.cursorActivityHandlers[k](a)
	}

	function yc(a, b) {
		if (a.curOp)return b();
		wc(a);
		try {
			return b()
		} finally {
			xc(a)
		}
	}

	function zc(a, b) {
		return function () {
			if (a.curOp)return b.apply(a, arguments);
			wc(a);
			try {
				return b.apply(a, arguments)
			} finally {
				xc(a)
			}
		}
	}

	function Ac(a) {
		return function () {
			if (this.curOp)return a.apply(this, arguments);
			wc(this);
			try {
				return a.apply(this, arguments)
			} finally {
				xc(this)
			}
		}
	}

	function Bc(a) {
		return function () {
			var b = this.cm;
			if (!b || b.curOp)return a.apply(this, arguments);
			wc(b);
			try {
				return a.apply(this, arguments)
			} finally {
				xc(b)
			}
		}
	}

	function Cc(a, b, c) {
		this.line = b, this.rest = Qe(b), this.size = this.rest ? Gf(yg(this.rest)) - c + 1 : 1, this.node = this.text = null, this.hidden = Te(a, b)
	}

	function Dc(a, b, c) {
		for (var e, d = [], f = b; c > f; f = e) {
			var g = new Cc(a.doc, Cf(a.doc, f), f);
			e = f + g.size, d.push(g)
		}
		return d
	}

	function Ec(a, b, c, d) {
		null == b && (b = a.doc.first), null == c && (c = a.doc.first + a.doc.size), d || (d = 0);
		var e = a.display;
		if (d && c < e.viewTo && (null == e.updateLineNumbers || e.updateLineNumbers > b) && (e.updateLineNumbers = b), a.curOp.viewChanged = !0, b >= e.viewTo)v && Re(a.doc, b) < e.viewTo && Gc(a); else if (c <= e.viewFrom)v && Se(a.doc, c + d) > e.viewFrom ? Gc(a) : (e.viewFrom += d, e.viewTo += d); else if (b <= e.viewFrom && c >= e.viewTo)Gc(a); else if (b <= e.viewFrom) {
			var f = Ic(a, c, c + d, 1);
			f ? (e.view = e.view.slice(f.index), e.viewFrom = f.lineN, e.viewTo += d) : Gc(a)
		} else if (c >= e.viewTo) {
			var f = Ic(a, b, b, -1);
			f ? (e.view = e.view.slice(0, f.index), e.viewTo = f.lineN) : Gc(a)
		} else {
			var g = Ic(a, b, b, -1), h = Ic(a, c, c + d, 1);
			g && h ? (e.view = e.view.slice(0, g.index).concat(Dc(a, g.lineN, h.lineN)).concat(e.view.slice(h.index)), e.viewTo += d) : Gc(a)
		}
		var i = e.externalMeasured;
		i && (c < i.lineN ? i.lineN += d : b < i.lineN + i.size && (e.externalMeasured = null))
	}

	function Fc(a, b, c) {
		a.curOp.viewChanged = !0;
		var d = a.display, e = a.display.externalMeasured;
		if (e && b >= e.lineN && b < e.lineN + e.size && (d.externalMeasured = null), !(b < d.viewFrom || b >= d.viewTo)) {
			var f = d.view[Hc(a, b)];
			if (null != f.node) {
				var g = f.changes || (f.changes = []);
				-1 == Ag(g, c) && g.push(c)
			}
		}
	}

	function Gc(a) {
		a.display.viewFrom = a.display.viewTo = a.doc.first, a.display.view = [], a.display.viewOffset = 0
	}

	function Hc(a, b) {
		if (b >= a.display.viewTo)return null;
		if (b -= a.display.viewFrom, 0 > b)return null;
		for (var c = a.display.view, d = 0; d < c.length; d++)if (b -= c[d].size, 0 > b)return d
	}

	function Ic(a, b, c, d) {
		var f, e = Hc(a, b), g = a.display.view;
		if (!v || c == a.doc.first + a.doc.size)return {index: e, lineN: c};
		for (var h = 0, i = a.display.viewFrom; e > h; h++)i += g[h].size;
		if (i != b) {
			if (d > 0) {
				if (e == g.length - 1)return null;
				f = i + g[e].size - b, e++
			} else f = i - b;
			b += f, c += f
		}
		for (; Re(a.doc, c) != c;) {
			if (e == (0 > d ? 0 : g.length - 1))return null;
			c += d * g[e - (0 > d ? 1 : 0)].size, e += d
		}
		return {index: e, lineN: c}
	}

	function Jc(a, b, c) {
		var d = a.display, e = d.view;
		0 == e.length || b >= d.viewTo || c <= d.viewFrom ? (d.view = Dc(a, b, c), d.viewFrom = b) : (d.viewFrom > b ? d.view = Dc(a, b, d.viewFrom).concat(d.view) : d.viewFrom < b && (d.view = d.view.slice(Hc(a, b))), d.viewFrom = b, d.viewTo < c ? d.view = d.view.concat(Dc(a, d.viewTo, c)) : d.viewTo > c && (d.view = d.view.slice(0, Hc(a, c)))), d.viewTo = c
	}

	function Kc(a) {
		for (var b = a.display.view, c = 0, d = 0; d < b.length; d++) {
			var e = b[d];
			e.hidden || e.node && !e.changes || ++c
		}
		return c
	}

	function Lc(a) {
		a.display.pollingFast || a.display.poll.set(a.options.pollInterval, function () {
			Nc(a), a.state.focused && Lc(a)
		})
	}

	function Mc(a) {
		function c() {
			var d = Nc(a);
			d || b ? (a.display.pollingFast = !1, Lc(a)) : (b = !0, a.display.poll.set(60, c))
		}

		var b = !1;
		a.display.pollingFast = !0, a.display.poll.set(20, c)
	}

	function Nc(a) {
		var b = a.display.input, c = a.display.prevInput, f = a.doc;
		if (!a.state.focused || fh(b) && !c || Rc(a) || a.options.disableInput)return !1;
		a.state.pasteIncoming && a.state.fakedLastChar && (b.value = b.value.substring(0, b.value.length - 1), a.state.fakedLastChar = !1);
		var g = b.value;
		if (g == c && !a.somethingSelected())return !1;
		if (d && e >= 9 && a.display.inputHasSelection === g)return Oc(a), !1;
		var h = !a.curOp;
		h && wc(a), a.display.shift = !1, 8203 != g.charCodeAt(0) || f.sel != a.display.selForContextMenu || c || (c = "\u200b");
		for (var i = 0, j = Math.min(c.length, g.length); j > i && c.charCodeAt(i) == g.charCodeAt(i);)++i;
		for (var k = g.slice(i), l = eh(k), m = a.state.pasteIncoming && l.length > 1 && f.sel.ranges.length == l.length, n = f.sel.ranges.length - 1; n >= 0; n--) {
			var o = f.sel.ranges[n], p = o.from(), q = o.to();
			i < c.length ? p = mb(p.line, p.ch - (c.length - i)) : a.state.overwrite && o.empty() && !a.state.pasteIncoming && (q = mb(q.line, Math.min(Cf(f, q.line).text.length, q.ch + yg(l).length)));
			var r = a.curOp.updateInput, s = {
				from:   p,
				to:     q,
				text:   m ? [l[n]] : l,
				origin: a.state.pasteIncoming ? "paste" : a.state.cutIncoming ? "cut" : "+input"
			};
			if (Ed(a.doc, s), ig(a, "inputRead", a, s), k && !a.state.pasteIncoming && a.options.electricChars && a.options.smartIndent && o.head.ch < 100 && (!n || f.sel.ranges[n - 1].head.line != o.head.line)) {
				var t = a.getModeAt(o.head);
				if (t.electricChars) {
					for (var u = 0; u < t.electricChars.length; u++)if (k.indexOf(t.electricChars.charAt(u)) > -1) {
						Sd(a, o.head.line, "smart");
						break
					}
				} else if (t.electricInput) {
					var v = yd(s);
					t.electricInput.test(Cf(f, v.line).text.slice(0, v.ch)) && Sd(a, o.head.line, "smart")
				}
			}
		}
		return Qd(a), a.curOp.updateInput = r, a.curOp.typing = !0, g.length > 1e3 || g.indexOf("\n") > -1 ? b.value = a.display.prevInput = "" : a.display.prevInput = g, h && xc(a), a.state.pasteIncoming = a.state.cutIncoming = !1, !0
	}

	function Oc(a, b) {
		var c, f, g = a.doc;
		if (a.somethingSelected()) {
			a.display.prevInput = "";
			var h = g.sel.primary();
			c = gh && (h.to().line - h.from().line > 100 || (f = a.getSelection()).length > 1e3);
			var i = c ? "-" : f || a.getSelection();
			a.display.input.value = i, a.state.focused && zg(a.display.input), d && e >= 9 && (a.display.inputHasSelection = i)
		} else b || (a.display.prevInput = a.display.input.value = "", d && e >= 9 && (a.display.inputHasSelection = null));
		a.display.inaccurateSelection = c
	}

	function Pc(a) {
		"nocursor" == a.options.readOnly || o && Qg() == a.display.input || a.display.input.focus()
	}

	function Qc(a) {
		a.state.focused || (Pc(a), ud(a))
	}

	function Rc(a) {
		return a.options.readOnly || a.doc.cantEdit
	}

	function Sc(a) {
		function c() {
			a.state.focused && setTimeout(Eg(Pc, a), 0)
		}

		function g(b) {
			kg(a, b) || ag(b)
		}

		function h(c) {
			if (a.somethingSelected())b.inaccurateSelection && (b.prevInput = "", b.inaccurateSelection = !1, b.input.value = a.getSelection(), zg(b.input)); else {
				for (var d = "", e = [], f = 0; f < a.doc.sel.ranges.length; f++) {
					var g = a.doc.sel.ranges[f].head.line, h = {anchor: mb(g, 0), head: mb(g + 1, 0)};
					e.push(h), d += a.getRange(h.anchor, h.head)
				}
				"cut" == c.type ? a.setSelections(e, null, qg) : (b.prevInput = "", b.input.value = d, zg(b.input))
			}
			"cut" == c.type && (a.state.cutIncoming = !0)
		}

		var b = a.display;
		dg(b.scroller, "mousedown", zc(a, Wc)), d && 11 > e ? dg(b.scroller, "dblclick", zc(a, function (b) {
			if (!kg(a, b)) {
				var c = Vc(a, b);
				if (c && !bd(a, b) && !Uc(a.display, b)) {
					Zf(b);
					var d = Xd(a, c);
					Bb(a.doc, d.anchor, d.head)
				}
			}
		})) : dg(b.scroller, "dblclick", function (b) {
			kg(a, b) || Zf(b)
		}), dg(b.lineSpace, "selectstart", function (a) {
			Uc(b, a) || Zf(a)
		}), t || dg(b.scroller, "contextmenu", function (b) {
			wd(a, b)
		}), dg(b.scroller, "scroll", function () {
			b.scroller.clientHeight && (fd(a, b.scroller.scrollTop), gd(a, b.scroller.scrollLeft, !0), fg(a, "scroll", a))
		}), dg(b.scrollbarV, "scroll", function () {
			b.scroller.clientHeight && fd(a, b.scrollbarV.scrollTop)
		}), dg(b.scrollbarH, "scroll", function () {
			b.scroller.clientHeight && gd(a, b.scrollbarH.scrollLeft)
		}), dg(b.scroller, "mousewheel", function (b) {
			jd(a, b)
		}), dg(b.scroller, "DOMMouseScroll", function (b) {
			jd(a, b)
		}), dg(b.scrollbarH, "mousedown", c), dg(b.scrollbarV, "mousedown", c), dg(b.wrapper, "scroll", function () {
			b.wrapper.scrollTop = b.wrapper.scrollLeft = 0
		}), dg(b.input, "keyup", zc(a, sd)), dg(b.input, "input", function () {
			d && e >= 9 && a.display.inputHasSelection && (a.display.inputHasSelection = null), Mc(a)
		}), dg(b.input, "keydown", zc(a, qd)), dg(b.input, "keypress", zc(a, td)), dg(b.input, "focus", Eg(ud, a)), dg(b.input, "blur", Eg(vd, a)), a.options.dragDrop && (dg(b.scroller, "dragstart", function (b) {
			ed(a, b)
		}), dg(b.scroller, "dragenter", g), dg(b.scroller, "dragover", g), dg(b.scroller, "drop", zc(a, dd))), dg(b.scroller, "paste", function (c) {
			Uc(b, c) || (a.state.pasteIncoming = !0, Pc(a), Mc(a))
		}), dg(b.input, "paste", function () {
			if (f && !a.state.fakedLastChar && !(new Date - a.state.lastMiddleDown < 200)) {
				var c = b.input.selectionStart, d = b.input.selectionEnd;
				b.input.value += "$", b.input.selectionStart = c, b.input.selectionEnd = d, a.state.fakedLastChar = !0
			}
			a.state.pasteIncoming = !0, Mc(a)
		}), dg(b.input, "cut", h), dg(b.input, "copy", h), k && dg(b.sizer, "mouseup", function () {
			Qg() == b.input && b.input.blur(), Pc(a)
		})
	}

	function Tc(a) {
		var b = a.display;
		b.cachedCharWidth = b.cachedTextHeight = b.cachedPaddingH = null, a.setSize()
	}

	function Uc(a, b) {
		for (var c = bg(b); c != a.wrapper; c = c.parentNode)if (!c || c.ignoreEvents || c.parentNode == a.sizer && c != a.mover)return !0
	}

	function Vc(a, b, c, d) {
		var e = a.display;
		if (!c) {
			var f = bg(b);
			if (f == e.scrollbarH || f == e.scrollbarV || f == e.scrollbarFiller || f == e.gutterFiller)return null
		}
		var g, h, i = e.lineSpace.getBoundingClientRect();
		try {
			g = b.clientX - i.left, h = b.clientY - i.top
		} catch (b) {
			return null
		}
		var k, j = qc(a, g, h);
		if (d && 1 == j.xRel && (k = Cf(a.doc, j.line).text).length == j.ch) {
			var l = ug(k, k.length, a.options.tabSize) - k.length;
			j = mb(j.line, Math.max(0, Math.round((g - Xb(a.display).left) / uc(a.display)) - l))
		}
		return j
	}

	function Wc(a) {
		if (!kg(this, a)) {
			var b = this, c = b.display;
			if (c.shift = a.shiftKey, Uc(c, a))return f || (c.scroller.draggable = !1, setTimeout(function () {
				c.scroller.draggable = !0
			}, 100)), void 0;
			if (!bd(b, a)) {
				var d = Vc(b, a);
				switch (window.focus(), cg(a)) {
					case 1:
						d ? Zc(b, a, d) : bg(a) == c.scroller && Zf(a);
						break;
					case 2:
						f && (b.state.lastMiddleDown = +new Date), d && Bb(b.doc, d), setTimeout(Eg(Pc, b), 20), Zf(a);
						break;
					case 3:
						t && wd(b, a)
				}
			}
		}
	}

	function Zc(a, b, c) {
		setTimeout(Eg(Qc, a), 0);
		var e, d = +new Date;
		Yc && Yc.time > d - 400 && 0 == nb(Yc.pos, c) ? e = "triple" : Xc && Xc.time > d - 400 && 0 == nb(Xc.pos, c) ? (e = "double", Yc = {
			time: d,
			pos: c
		}) : (e = "single", Xc = {time: d, pos: c});
		var f = a.doc.sel, g = p ? b.metaKey : b.ctrlKey;
		a.options.dragDrop && Zg && !Rc(a) && "single" == e && f.contains(c) > -1 && f.somethingSelected() ? $c(a, b, c, g) : _c(a, b, c, e, g)
	}

	function $c(a, b, c, g) {
		var h = a.display, i = zc(a, function (j) {
			f && (h.scroller.draggable = !1), a.state.draggingText = !1, eg(document, "mouseup", i), eg(h.scroller, "drop", i), Math.abs(b.clientX - j.clientX) + Math.abs(b.clientY - j.clientY) < 10 && (Zf(j), g || Bb(a.doc, c), Pc(a), d && 9 == e && setTimeout(function () {
				document.body.focus(), Pc(a)
			}, 20))
		});
		f && (h.scroller.draggable = !0), a.state.draggingText = i, h.scroller.dragDrop && h.scroller.dragDrop(), dg(document, "mouseup", i), dg(h.scroller, "drop", i)
	}

	function _c(a, b, c, d, e) {
		function n(b) {
			if (0 != nb(m, b))if (m = b, "rect" == d) {
				for (var e = [], f = a.options.tabSize, k = ug(Cf(g, c.line).text, c.ch, f), l = ug(Cf(g, b.line).text, b.ch, f), n = Math.min(k, l), o = Math.max(k, l), p = Math.min(c.line, b.line), q = Math.min(a.lastLine(), Math.max(c.line, b.line)); q >= p; p++) {
					var r = Cf(g, p).text, s = vg(r, n, f);
					n == o ? e.push(new sb(mb(p, s), mb(p, s))) : r.length > s && e.push(new sb(mb(p, s), mb(p, vg(r, o, f))))
				}
				e.length || e.push(new sb(c, c)), Hb(g, tb(j.ranges.slice(0, i).concat(e), i), {
					origin: "*mouse",
					scroll: !1
				}), a.scrollIntoView(b)
			} else {
				var t = h, u = t.anchor, v = b;
				if ("single" != d) {
					if ("double" == d)var w = Xd(a, b); else var w = new sb(mb(b.line, 0), wb(g, mb(b.line + 1, 0)));
					nb(w.anchor, u) > 0 ? (v = w.head, u = qb(t.from(), w.anchor)) : (v = w.anchor, u = pb(t.to(), w.head))
				}
				var e = j.ranges.slice(0);
				e[i] = new sb(wb(g, u), v), Hb(g, tb(e, i), rg)
			}
		}

		function q(b) {
			var c = ++p, e = Vc(a, b, !0, "rect" == d);
			if (e)if (0 != nb(e, m)) {
				Qc(a), n(e);
				var h = O(f, g);
				(e.line >= h.to || e.line < h.from) && setTimeout(zc(a, function () {
					p == c && q(b)
				}), 150)
			} else {
				var i = b.clientY < o.top ? -20 : b.clientY > o.bottom ? 20 : 0;
				i && setTimeout(zc(a, function () {
					p == c && (f.scroller.scrollTop += i, q(b))
				}), 50)
			}
		}

		function r(b) {
			p = 1 / 0, Zf(b), Pc(a), eg(document, "mousemove", s), eg(document, "mouseup", t), g.history.lastSelOrigin = null
		}

		var f = a.display, g = a.doc;
		Zf(b);
		var h, i, j = g.sel;
		if (e && !b.shiftKey ? (i = g.sel.contains(c), h = i > -1 ? g.sel.ranges[i] : new sb(c, c)) : h = g.sel.primary(), b.altKey)d = "rect", e || (h = new sb(c, c)), c = Vc(a, b, !0, !0), i = -1; else if ("double" == d) {
			var k = Xd(a, c);
			h = a.display.shift || g.extend ? Ab(g, h, k.anchor, k.head) : k
		} else if ("triple" == d) {
			var l = new sb(mb(c.line, 0), wb(g, mb(c.line + 1, 0)));
			h = a.display.shift || g.extend ? Ab(g, h, l.anchor, l.head) : l
		} else h = Ab(g, h, c);
		e ? i > -1 ? Db(g, i, h, rg) : (i = g.sel.ranges.length, Hb(g, tb(g.sel.ranges.concat([h]), i), {
			scroll: !1,
			origin: "*mouse"
		})) : (i = 0, Hb(g, new rb([h], 0), rg), j = g.sel);
		var m = c, o = f.wrapper.getBoundingClientRect(), p = 0, s = zc(a, function (a) {
			cg(a) ? q(a) : r(a)
		}), t = zc(a, r);
		dg(document, "mousemove", s), dg(document, "mouseup", t)
	}

	function ad(a, b, c, d, e) {
		try {
			var f = b.clientX, g = b.clientY
		} catch (b) {
			return !1
		}
		if (f >= Math.floor(a.display.gutters.getBoundingClientRect().right))return !1;
		d && Zf(b);
		var h = a.display, i = h.lineDiv.getBoundingClientRect();
		if (g > i.bottom || !mg(a, c))return _f(b);
		g -= i.top - h.viewOffset;
		for (var j = 0; j < a.options.gutters.length; ++j) {
			var k = h.gutters.childNodes[j];
			if (k && k.getBoundingClientRect().right >= f) {
				var l = Hf(a.doc, g), m = a.options.gutters[j];
				return e(a, c, a, l, m, b), _f(b)
			}
		}
	}

	function bd(a, b) {
		return ad(a, b, "gutterClick", !0, ig)
	}

	function dd(a) {
		var b = this;
		if (!kg(b, a) && !Uc(b.display, a)) {
			Zf(a), d && (cd = +new Date);
			var c = Vc(b, a, !0), e = a.dataTransfer.files;
			if (c && !Rc(b))if (e && e.length && window.FileReader && window.File)for (var f = e.length, g = Array(f), h = 0, i = function (a, d) {
				var e = new FileReader;
				e.onload = zc(b, function () {
					if (g[d] = e.result, ++h == f) {
						c = wb(b.doc, c);
						var a = {from: c, to: c, text: eh(g.join("\n")), origin: "paste"};
						Ed(b.doc, a), Gb(b.doc, ub(c, yd(a)))
					}
				}), e.readAsText(a)
			}, j = 0; f > j; ++j)i(e[j], j); else {
				if (b.state.draggingText && b.doc.sel.contains(c) > -1)return b.state.draggingText(a), setTimeout(Eg(Pc, b), 20), void 0;
				try {
					var g = a.dataTransfer.getData("Text");
					if (g) {
						if (b.state.draggingText && !(p ? a.metaKey : a.ctrlKey))var k = b.listSelections();
						if (Ib(b.doc, ub(c, c)), k)for (var j = 0; j < k.length; ++j)Kd(b.doc, "", k[j].anchor, k[j].head, "drag");
						b.replaceSelection(g, "around", "paste"), Pc(b)
					}
				} catch (a) {
				}
			}
		}
	}

	function ed(a, b) {
		if (d && (!a.state.draggingText || +new Date - cd < 100))return ag(b), void 0;
		if (!kg(a, b) && !Uc(a.display, b) && (b.dataTransfer.setData("Text", a.getSelection()), b.dataTransfer.setDragImage && !j)) {
			var c = Lg("img", null, null, "position: fixed; left: 0; top: 0;");
			c.src = "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==", i && (c.width = c.height = 1, a.display.wrapper.appendChild(c), c._top = c.offsetTop), b.dataTransfer.setDragImage(c, 0, 0), i && c.parentNode.removeChild(c)
		}
	}

	function fd(b, c) {
		Math.abs(b.doc.scrollTop - c) < 2 || (b.doc.scrollTop = c, a || T(b, {top: c}), b.display.scroller.scrollTop != c && (b.display.scroller.scrollTop = c), b.display.scrollbarV.scrollTop != c && (b.display.scrollbarV.scrollTop = c), a && T(b), Rb(b, 100))
	}

	function gd(a, b, c) {
		(c ? b == a.doc.scrollLeft : Math.abs(a.doc.scrollLeft - b) < 2) || (b = Math.min(b, a.display.scroller.scrollWidth - a.display.scroller.clientWidth), a.doc.scrollLeft = b, P(a), a.display.scroller.scrollLeft != b && (a.display.scroller.scrollLeft = b), a.display.scrollbarH.scrollLeft != b && (a.display.scrollbarH.scrollLeft = b))
	}

	function jd(b, c) {
		var d = c.wheelDeltaX, e = c.wheelDeltaY;
		null == d && c.detail && c.axis == c.HORIZONTAL_AXIS && (d = c.detail), null == e && c.detail && c.axis == c.VERTICAL_AXIS ? e = c.detail : null == e && (e = c.wheelDelta);
		var g = b.display, h = g.scroller;
		if (d && h.scrollWidth > h.clientWidth || e && h.scrollHeight > h.clientHeight) {
			if (e && p && f)a:for (var j = c.target, k = g.view; j != h; j = j.parentNode)for (var l = 0; l < k.length; l++)if (k[l].node == j) {
				b.display.currentWheelTarget = j;
				break a
			}
			if (d && !a && !i && null != id)return e && fd(b, Math.max(0, Math.min(h.scrollTop + e * id, h.scrollHeight - h.clientHeight))), gd(b, Math.max(0, Math.min(h.scrollLeft + d * id, h.scrollWidth - h.clientWidth))), Zf(c), g.wheelStartX = null, void 0;
			if (e && null != id) {
				var m = e * id, n = b.doc.scrollTop, o = n + g.wrapper.clientHeight;
				0 > m ? n = Math.max(0, n + m - 50) : o = Math.min(b.doc.height, o + m + 50), T(b, {top: n, bottom: o})
			}
			20 > hd && (null == g.wheelStartX ? (g.wheelStartX = h.scrollLeft, g.wheelStartY = h.scrollTop, g.wheelDX = d, g.wheelDY = e, setTimeout(function () {
				if (null != g.wheelStartX) {
					var a = h.scrollLeft - g.wheelStartX, b = h.scrollTop - g.wheelStartY, c = b && g.wheelDY && b / g.wheelDY || a && g.wheelDX && a / g.wheelDX;
					g.wheelStartX = g.wheelStartY = null, c && (id = (id * hd + c) / (hd + 1), ++hd)
				}
			}, 200)) : (g.wheelDX += d, g.wheelDY += e))
		}
	}

	function kd(a, b, c) {
		if ("string" == typeof b && (b = he[b], !b))return !1;
		a.display.pollingFast && Nc(a) && (a.display.pollingFast = !1);
		var d = a.display.shift, e = !1;
		try {
			Rc(a) && (a.state.suppressEdits = !0), c && (a.display.shift = !1), e = b(a) != pg
		} finally {
			a.display.shift = d, a.state.suppressEdits = !1
		}
		return e
	}

	function ld(a) {
		var b = a.state.keyMaps.slice(0);
		return a.options.extraKeys && b.push(a.options.extraKeys), b.push(a.options.keyMap), b
	}

	function nd(a, b) {
		var c = je(a.options.keyMap), d = c.auto;
		clearTimeout(md), d && !le(b) && (md = setTimeout(function () {
			je(a.options.keyMap) == c && (a.options.keyMap = d.call ? d.call(null, a) : d, D(a))
		}, 50));
		var e = me(b, !0), f = !1;
		if (!e)return !1;
		var g = ld(a);
		return f = b.shiftKey ? ke("Shift-" + e, g, function (b) {
			return kd(a, b, !0)
		}) || ke(e, g, function (b) {
			return ("string" == typeof b ? /^go[A-Z]/.test(b) : b.motion) ? kd(a, b) : void 0
		}) : ke(e, g, function (b) {
			return kd(a, b)
		}), f && (Zf(b), Qb(a), ig(a, "keyHandled", a, e, b)), f
	}

	function od(a, b, c) {
		var d = ke("'" + c + "'", ld(a), function (b) {
			return kd(a, b, !0)
		});
		return d && (Zf(b), Qb(a), ig(a, "keyHandled", a, "'" + c + "'", b)), d
	}

	function qd(a) {
		var b = this;
		if (Qc(b), !kg(b, a)) {
			d && 11 > e && 27 == a.keyCode && (a.returnValue = !1);
			var c = a.keyCode;
			b.display.shift = 16 == c || a.shiftKey;
			var f = nd(b, a);
			i && (pd = f ? c : null, !f && 88 == c && !gh && (p ? a.metaKey : a.ctrlKey) && b.replaceSelection("", null, "cut")), 18 != c || /\bCodeMirror-crosshair\b/.test(b.display.lineDiv.className) || rd(b)
		}
	}

	function rd(a) {
		function c(a) {
			18 != a.keyCode && a.altKey || (Sg(b, "CodeMirror-crosshair"), eg(document, "keyup", c), eg(document, "mouseover", c))
		}

		var b = a.display.lineDiv;
		Tg(b, "CodeMirror-crosshair"), dg(document, "keyup", c), dg(document, "mouseover", c)
	}

	function sd(a) {
		kg(this, a) || 16 == a.keyCode && (this.doc.sel.shift = !1)
	}

	function td(a) {
		var b = this;
		if (!(kg(b, a) || a.ctrlKey || p && a.metaKey)) {
			var c = a.keyCode, f = a.charCode;
			if (i && c == pd)return pd = null, Zf(a), void 0;
			if (!(i && (!a.which || a.which < 10) || k) || !nd(b, a)) {
				var g = String.fromCharCode(null == f ? c : f);
				od(b, a, g) || (d && e >= 9 && (b.display.inputHasSelection = null), Mc(b))
			}
		}
	}

	function ud(a) {
		"nocursor" != a.options.readOnly && (a.state.focused || (fg(a, "focus", a), a.state.focused = !0, Tg(a.display.wrapper, "CodeMirror-focused"), a.curOp || a.display.selForContextMenu == a.doc.sel || (Oc(a), f && setTimeout(Eg(Oc, a, !0), 0))), Lc(a), Qb(a))
	}

	function vd(a) {
		a.state.focused && (fg(a, "blur", a), a.state.focused = !1, Sg(a.display.wrapper, "CodeMirror-focused")), clearInterval(a.display.blinker), setTimeout(function () {
			a.state.focused || (a.display.shift = !1)
		}, 150)
	}

	function wd(a, b) {
		function k() {
			if (null != c.input.selectionStart) {
				var b = a.somethingSelected(), d = c.input.value = "\u200b" + (b ? c.input.value : "");
				c.prevInput = b ? "" : "\u200b", c.input.selectionStart = 1, c.input.selectionEnd = d.length, c.selForContextMenu = a.doc.sel
			}
		}

		function l() {
			if (c.inputDiv.style.position = "relative", c.input.style.cssText = j, d && 9 > e && (c.scrollbarV.scrollTop = c.scroller.scrollTop = g), Lc(a), null != c.input.selectionStart) {
				(!d || d && 9 > e) && k();
				var b = 0, f = function () {
					c.selForContextMenu == a.doc.sel && 0 == c.input.selectionStart ? zc(a, he.selectAll)(a) : b++ < 10 ? c.detectingSelectAll = setTimeout(f, 500) : Oc(a)
				};
				c.detectingSelectAll = setTimeout(f, 200)
			}
		}

		if (!kg(a, b, "contextmenu")) {
			var c = a.display;
			if (!Uc(c, b) && !xd(a, b)) {
				var f = Vc(a, b), g = c.scroller.scrollTop;
				if (f && !i) {
					var h = a.options.resetSelectionOnContextMenu;
					h && -1 == a.doc.sel.contains(f) && zc(a, Hb)(a.doc, ub(f), qg);
					var j = c.input.style.cssText;
					if (c.inputDiv.style.position = "absolute", c.input.style.cssText = "position: fixed; width: 30px; height: 30px; top: " + (b.clientY - 5) + "px; left: " + (b.clientX - 5) + "px; z-index: 1000; background: " + (d ? "rgba(255, 255, 255, .05)" : "transparent") + "; outline: none; border-width: 0; outline: none; overflow: hidden; opacity: .05; filter: alpha(opacity=5);", Pc(a), Oc(a), a.somethingSelected() || (c.input.value = c.prevInput = " "), c.selForContextMenu = a.doc.sel, clearTimeout(c.detectingSelectAll), d && e >= 9 && k(), t) {
						ag(b);
						var m = function () {
							eg(window, "mouseup", m), setTimeout(l, 20)
						};
						dg(window, "mouseup", m)
					} else setTimeout(l, 50)
				}
			}
		}
	}

	function xd(a, b) {
		return mg(a, "gutterContextMenu") ? ad(a, b, "gutterContextMenu", !1, fg) : !1
	}

	function zd(a, b) {
		if (nb(a, b.from) < 0)return a;
		if (nb(a, b.to) <= 0)return yd(b);
		var c = a.line + b.text.length - (b.to.line - b.from.line) - 1, d = a.ch;
		return a.line == b.to.line && (d += yd(b).ch - b.to.ch), mb(c, d)
	}

	function Ad(a, b) {
		for (var c = [], d = 0; d < a.sel.ranges.length; d++) {
			var e = a.sel.ranges[d];
			c.push(new sb(zd(e.anchor, b), zd(e.head, b)))
		}
		return tb(c, a.sel.primIndex)
	}

	function Bd(a, b, c) {
		return a.line == b.line ? mb(c.line, a.ch - b.ch + c.ch) : mb(c.line + (a.line - b.line), a.ch)
	}

	function Cd(a, b, c) {
		for (var d = [], e = mb(a.first, 0), f = e, g = 0; g < b.length; g++) {
			var h = b[g], i = Bd(h.from, e, f), j = Bd(yd(h), e, f);
			if (e = h.to, f = j, "around" == c) {
				var k = a.sel.ranges[g], l = nb(k.head, k.anchor) < 0;
				d[g] = new sb(l ? j : i, l ? i : j)
			} else d[g] = new sb(i, i)
		}
		return new rb(d, a.sel.primIndex)
	}

	function Dd(a, b, c) {
		var d = {
			canceled: !1, from: b.from, to: b.to, text: b.text, origin: b.origin, cancel: function () {
				this.canceled = !0
			}
		};
		return c && (d.update = function (b, c, d, e) {
			b && (this.from = wb(a, b)), c && (this.to = wb(a, c)), d && (this.text = d), void 0 !== e && (this.origin = e)
		}), fg(a, "beforeChange", a, d), a.cm && fg(a.cm, "beforeChange", a.cm, d), d.canceled ? null : {
			from:   d.from,
			to:     d.to,
			text:   d.text,
			origin: d.origin
		}
	}

	function Ed(a, b, c) {
		if (a.cm) {
			if (!a.cm.curOp)return zc(a.cm, Ed)(a, b, c);
			if (a.cm.state.suppressEdits)return
		}
		if (!(mg(a, "beforeChange") || a.cm && mg(a.cm, "beforeChange")) || (b = Dd(a, b, !0))) {
			var d = u && !c && Fe(a, b.from, b.to);
			if (d)for (var e = d.length - 1; e >= 0; --e)Fd(a, {
				from: d[e].from,
				to:   d[e].to,
				text: e ? [""] : b.text
			}); else Fd(a, b)
		}
	}

	function Fd(a, b) {
		if (1 != b.text.length || "" != b.text[0] || 0 != nb(b.from, b.to)) {
			var c = Ad(a, b);
			Of(a, b, c, a.cm ? a.cm.curOp.id : 0 / 0), Id(a, b, c, Ce(a, b));
			var d = [];
			Af(a, function (a, c) {
				c || -1 != Ag(d, a.history) || (Yf(a.history, b), d.push(a.history)), Id(a, b, null, Ce(a, b))
			})
		}
	}

	function Gd(a, b, c) {
		if (!a.cm || !a.cm.state.suppressEdits) {
			for (var e, d = a.history, f = a.sel, g = "undo" == b ? d.done : d.undone, h = "undo" == b ? d.undone : d.done, i = 0; i < g.length && (e = g[i], c ? !e.ranges || e.equals(a.sel) : e.ranges); i++);
			if (i != g.length) {
				for (d.lastOrigin = d.lastSelOrigin = null; e = g.pop(), e.ranges;) {
					if (Rf(e, h), c && !e.equals(a.sel))return Hb(a, e, {clearRedo: !1}), void 0;
					f = e
				}
				var j = [];
				Rf(f, h), h.push({changes: j, generation: d.generation}), d.generation = e.generation || ++d.maxGeneration;
				for (var k = mg(a, "beforeChange") || a.cm && mg(a.cm, "beforeChange"), i = e.changes.length - 1; i >= 0; --i) {
					var l = e.changes[i];
					if (l.origin = b, k && !Dd(a, l, !1))return g.length = 0, void 0;
					j.push(Lf(a, l));
					var m = i ? Ad(a, l, null) : yg(g);
					Id(a, l, m, Ee(a, l)), !i && a.cm && a.cm.scrollIntoView(l);
					var n = [];
					Af(a, function (a, b) {
						b || -1 != Ag(n, a.history) || (Yf(a.history, l), n.push(a.history)), Id(a, l, null, Ee(a, l))
					})
				}
			}
		}
	}

	function Hd(a, b) {
		if (0 != b && (a.first += b, a.sel = new rb(Bg(a.sel.ranges, function (a) {
				return new sb(mb(a.anchor.line + b, a.anchor.ch), mb(a.head.line + b, a.head.ch))
			}), a.sel.primIndex), a.cm)) {
			Ec(a.cm, a.first, a.first - b, b);
			for (var c = a.cm.display, d = c.viewFrom; d < c.viewTo; d++)Fc(a.cm, d, "gutter")
		}
	}

	function Id(a, b, c, d) {
		if (a.cm && !a.cm.curOp)return zc(a.cm, Id)(a, b, c, d);
		if (b.to.line < a.first)return Hd(a, b.text.length - 1 - (b.to.line - b.from.line)), void 0;
		if (!(b.from.line > a.lastLine())) {
			if (b.from.line < a.first) {
				var e = b.text.length - 1 - (a.first - b.from.line);
				Hd(a, e), b = {from: mb(a.first, 0), to: mb(b.to.line + e, b.to.ch), text: [yg(b.text)], origin: b.origin}
			}
			var f = a.lastLine();
			b.to.line > f && (b = {
				from:   b.from,
				to:     mb(f, Cf(a, f).text.length),
				text:   [b.text[0]],
				origin: b.origin
			}), b.removed = Df(a, b.from, b.to), c || (c = Ad(a, b, null)), a.cm ? Jd(a.cm, b, d) : tf(a, b, d), Ib(a, c, qg)
		}
	}

	function Jd(a, b, c) {
		var d = a.doc, e = a.display, f = b.from, g = b.to, h = !1, i = f.line;
		a.options.lineWrapping || (i = Gf(Pe(Cf(d, f.line))), d.iter(i, g.line + 1, function (a) {
			return a == e.maxLine ? (h = !0, !0) : void 0
		})), d.sel.contains(b.from, b.to) > -1 && lg(a), tf(d, b, c, B(a)), a.options.lineWrapping || (d.iter(i, f.line + b.text.length, function (a) {
			var b = I(a);
			b > e.maxLineLength && (e.maxLine = a, e.maxLineLength = b, e.maxLineChanged = !0, h = !1)
		}), h && (a.curOp.updateMaxLine = !0)), d.frontier = Math.min(d.frontier, f.line), Rb(a, 400);
		var j = b.text.length - (g.line - f.line) - 1;
		f.line != g.line || 1 != b.text.length || sf(a.doc, b) ? Ec(a, f.line, g.line + 1, j) : Fc(a, f.line, "text");
		var k = mg(a, "changes"), l = mg(a, "change");
		if (l || k) {
			var m = {from: f, to: g, text: b.text, removed: b.removed, origin: b.origin};
			l && ig(a, "change", a, m), k && (a.curOp.changeObjs || (a.curOp.changeObjs = [])).push(m)
		}
		a.display.selForContextMenu = null
	}

	function Kd(a, b, c, d, e) {
		if (d || (d = c), nb(d, c) < 0) {
			var f = d;
			d = c, c = f
		}
		"string" == typeof b && (b = eh(b)), Ed(a, {from: c, to: d, text: b, origin: e})
	}

	function Ld(a, b) {
		var c = a.display, d = c.sizer.getBoundingClientRect(), e = null;
		if (b.top + d.top < 0 ? e = !0 : b.bottom + d.top > (window.innerHeight || document.documentElement.clientHeight) && (e = !1), null != e && !m) {
			var f = Lg("div", "\u200b", null, "position: absolute; top: " + (b.top - c.viewOffset - Vb(a.display)) + "px; height: " + (b.bottom - b.top + og) + "px; left: " + b.left + "px; width: 2px;");
			a.display.lineSpace.appendChild(f), f.scrollIntoView(e), a.display.lineSpace.removeChild(f)
		}
	}

	function Md(a, b, c, d) {
		for (null == d && (d = 0); ;) {
			var e = !1, f = nc(a, b), g = c && c != b ? nc(a, c) : f, h = Od(a, Math.min(f.left, g.left), Math.min(f.top, g.top) - d, Math.max(f.left, g.left), Math.max(f.bottom, g.bottom) + d), i = a.doc.scrollTop, j = a.doc.scrollLeft;
			if (null != h.scrollTop && (fd(a, h.scrollTop), Math.abs(a.doc.scrollTop - i) > 1 && (e = !0)), null != h.scrollLeft && (gd(a, h.scrollLeft), Math.abs(a.doc.scrollLeft - j) > 1 && (e = !0)), !e)return f
		}
	}

	function Nd(a, b, c, d, e) {
		var f = Od(a, b, c, d, e);
		null != f.scrollTop && fd(a, f.scrollTop), null != f.scrollLeft && gd(a, f.scrollLeft)
	}

	function Od(a, b, c, d, e) {
		var f = a.display, g = tc(a.display);
		0 > c && (c = 0);
		var h = a.curOp && null != a.curOp.scrollTop ? a.curOp.scrollTop : f.scroller.scrollTop, i = f.scroller.clientHeight - og, j = {}, k = a.doc.height + Wb(f), l = g > c, m = e > k - g;
		if (h > c)j.scrollTop = l ? 0 : c; else if (e > h + i) {
			var n = Math.min(c, (m ? k : e) - i);
			n != h && (j.scrollTop = n)
		}
		var o = a.curOp && null != a.curOp.scrollLeft ? a.curOp.scrollLeft : f.scroller.scrollLeft, p = f.scroller.clientWidth - og;
		b += f.gutters.offsetWidth, d += f.gutters.offsetWidth;
		var q = f.gutters.offsetWidth, r = q + 10 > b;
		return o + q > b || r ? (r && (b = 0), j.scrollLeft = Math.max(0, b - 10 - q)) : d > p + o - 3 && (j.scrollLeft = d + 10 - p), j
	}

	function Pd(a, b, c) {
		(null != b || null != c) && Rd(a), null != b && (a.curOp.scrollLeft = (null == a.curOp.scrollLeft ? a.doc.scrollLeft : a.curOp.scrollLeft) + b), null != c && (a.curOp.scrollTop = (null == a.curOp.scrollTop ? a.doc.scrollTop : a.curOp.scrollTop) + c)
	}

	function Qd(a) {
		Rd(a);
		var b = a.getCursor(), c = b, d = b;
		a.options.lineWrapping || (c = b.ch ? mb(b.line, b.ch - 1) : b, d = mb(b.line, b.ch + 1)), a.curOp.scrollToPos = {
			from:     c,
			to:       d,
			margin:   a.options.cursorScrollMargin,
			isCursor: !0
		}
	}

	function Rd(a) {
		var b = a.curOp.scrollToPos;
		if (b) {
			a.curOp.scrollToPos = null;
			var c = oc(a, b.from), d = oc(a, b.to), e = Od(a, Math.min(c.left, d.left), Math.min(c.top, d.top) - b.margin, Math.max(c.right, d.right), Math.max(c.bottom, d.bottom) + b.margin);
			a.scrollTo(e.scrollLeft, e.scrollTop)
		}
	}

	function Sd(a, b, c, d) {
		var f, e = a.doc;
		null == c && (c = "add"), "smart" == c && (a.doc.mode.indent ? f = Ub(a, b) : c = "prev");
		var g = a.options.tabSize, h = Cf(e, b), i = ug(h.text, null, g);
		h.stateAfter && (h.stateAfter = null);
		var k, j = h.text.match(/^\s*/)[0];
		if (d || /\S/.test(h.text)) {
			if ("smart" == c && (k = a.doc.mode.indent(f, h.text.slice(j.length), h.text), k == pg)) {
				if (!d)return;
				c = "prev"
			}
		} else k = 0, c = "not";
		"prev" == c ? k = b > e.first ? ug(Cf(e, b - 1).text, null, g) : 0 : "add" == c ? k = i + a.options.indentUnit : "subtract" == c ? k = i - a.options.indentUnit : "number" == typeof c && (k = i + c), k = Math.max(0, k);
		var l = "", m = 0;
		if (a.options.indentWithTabs)for (var n = Math.floor(k / g); n; --n)m += g, l += "	";
		if (k > m && (l += xg(k - m)), l != j)Kd(a.doc, l, mb(b, 0), mb(b, j.length), "+input"); else for (var n = 0; n < e.sel.ranges.length; n++) {
			var o = e.sel.ranges[n];
			if (o.head.line == b && o.head.ch < j.length) {
				var m = mb(b, j.length);
				Db(e, n, new sb(m, m));
				break
			}
		}
		h.stateAfter = null
	}

	function Td(a, b, c, d) {
		var e = b, f = b;
		return "number" == typeof b ? f = Cf(a, vb(a, b)) : e = Gf(b), null == e ? null : (d(f, e) && a.cm && Fc(a.cm, e, c), f)
	}

	function Ud(a, b) {
		for (var c = a.doc.sel.ranges, d = [], e = 0; e < c.length; e++) {
			for (var f = b(c[e]); d.length && nb(f.from, yg(d).to) <= 0;) {
				var g = d.pop();
				if (nb(g.from, f.from) < 0) {
					f.from = g.from;
					break
				}
			}
			d.push(f)
		}
		yc(a, function () {
			for (var b = d.length - 1; b >= 0; b--)Kd(a.doc, "", d[b].from, d[b].to, "+delete");
			Qd(a)
		})
	}

	function Vd(a, b, c, d, e) {
		function k() {
			var b = f + c;
			return b < a.first || b >= a.first + a.size ? j = !1 : (f = b, i = Cf(a, b))
		}

		function l(a) {
			var b = (e ? th : uh)(i, g, c, !0);
			if (null == b) {
				if (a || !k())return j = !1;
				g = e ? (0 > c ? mh : lh)(i) : 0 > c ? i.text.length : 0
			} else g = b;
			return !0
		}

		var f = b.line, g = b.ch, h = c, i = Cf(a, f), j = !0;
		if ("char" == d)l(); else if ("column" == d)l(!0); else if ("word" == d || "group" == d)for (var m = null, n = "group" == d, o = a.cm && a.cm.getHelper(b, "wordChars"), p = !0; !(0 > c) || l(!p); p = !1) {
			var q = i.text.charAt(g) || "\n", r = Hg(q, o) ? "w" : n && "\n" == q ? "n" : !n || /\s/.test(q) ? null : "p";
			if (!n || p || r || (r = "s"), m && m != r) {
				0 > c && (c = 1, l());
				break
			}
			if (r && (m = r), c > 0 && !l(!p))break
		}
		var s = Mb(a, mb(f, g), h, !0);
		return j || (s.hitSide = !0), s
	}

	function Wd(a, b, c, d) {
		var g, e = a.doc, f = b.left;
		if ("page" == d) {
			var h = Math.min(a.display.wrapper.clientHeight, window.innerHeight || document.documentElement.clientHeight);
			g = b.top + c * (h - (0 > c ? 1.5 : .5) * tc(a.display))
		} else"line" == d && (g = c > 0 ? b.bottom + 3 : b.top - 3);
		for (; ;) {
			var i = qc(a, f, g);
			if (!i.outside)break;
			if (0 > c ? 0 >= g : g >= e.height) {
				i.hitSide = !0;
				break
			}
			g += 5 * c
		}
		return i
	}

	function Xd(a, b) {
		var c = a.doc, d = Cf(c, b.line).text, e = b.ch, f = b.ch;
		if (d) {
			var g = a.getHelper(b, "wordChars");
			(b.xRel < 0 || f == d.length) && e ? --e : ++f;
			for (var h = d.charAt(e), i = Hg(h, g) ? function (a) {
				return Hg(a, g)
			} : /\s/.test(h) ? function (a) {
				return /\s/.test(a)
			} : function (a) {
				return !/\s/.test(a) && !Hg(a)
			}; e > 0 && i(d.charAt(e - 1));)--e;
			for (; f < d.length && i(d.charAt(f));)++f
		}
		return new sb(mb(b.line, e), mb(b.line, f))
	}

	function $d(a, b, c, d) {
		w.defaults[a] = b, c && (Zd[a] = d ? function (a, b, d) {
			d != _d && c(a, b, d)
		} : c)
	}

	function je(a) {
		return "string" == typeof a ? ie[a] : a
	}

	function qe(a, b, c, d, e) {
		if (d && d.shared)return se(a, b, c, d, e);
		if (a.cm && !a.cm.curOp)return zc(a.cm, qe)(a, b, c, d, e);
		var f = new oe(a, e), g = nb(b, c);
		if (d && Dg(d, f, !1), g > 0 || 0 == g && f.clearWhenEmpty !== !1)return f;
		if (f.replacedWith && (f.collapsed = !0, f.widgetNode = Lg("span", [f.replacedWith], "CodeMirror-widget"), d.handleMouseEvents || (f.widgetNode.ignoreEvents = !0), d.insertLeft && (f.widgetNode.insertLeft = !0)), f.collapsed) {
			if (Oe(a, b.line, b, c, f) || b.line != c.line && Oe(a, c.line, b, c, f))throw new Error("Inserting collapsed marker partially overlapping an existing one");
			v = !0
		}
		f.addToHistory && Of(a, {from: b, to: c, origin: "markText"}, a.sel, 0 / 0);
		var j, h = b.line, i = a.cm;
		if (a.iter(h, c.line + 1, function (a) {
				i && f.collapsed && !i.options.lineWrapping && Pe(a) == i.display.maxLine && (j = !0), f.collapsed && h != b.line && Ff(a, 0), ze(a, new we(f, h == b.line ? b.ch : null, h == c.line ? c.ch : null)), ++h
			}), f.collapsed && a.iter(b.line, c.line + 1, function (b) {
				Te(a, b) && Ff(b, 0)
			}), f.clearOnEnter && dg(f, "beforeCursorEnter", function () {
				f.clear()
			}), f.readOnly && (u = !0, (a.history.done.length || a.history.undone.length) && a.clearHistory()), f.collapsed && (f.id = ++pe, f.atomic = !0), i) {
			if (j && (i.curOp.updateMaxLine = !0), f.collapsed)Ec(i, b.line, c.line + 1); else if (f.className || f.title || f.startStyle || f.endStyle)for (var k = b.line; k <= c.line; k++)Fc(i, k, "text");
			f.atomic && Kb(i.doc), ig(i, "markerAdded", i, f)
		}
		return f
	}

	function se(a, b, c, d, e) {
		d = Dg(d), d.shared = !1;
		var f = [qe(a, b, c, d, e)], g = f[0], h = d.widgetNode;
		return Af(a, function (a) {
			h && (d.widgetNode = h.cloneNode(!0)), f.push(qe(a, wb(a, b), wb(a, c), d, e));
			for (var i = 0; i < a.linked.length; ++i)if (a.linked[i].isParent)return;
			g = yg(f)
		}), new re(f, g)
	}

	function te(a) {
		return a.findMarks(mb(a.first, 0), a.clipPos(mb(a.lastLine())), function (a) {
			return a.parent
		})
	}

	function ue(a, b) {
		for (var c = 0; c < b.length; c++) {
			var d = b[c], e = d.find(), f = a.clipPos(e.from), g = a.clipPos(e.to);
			if (nb(f, g)) {
				var h = qe(a, f, g, d.primary, d.primary.type);
				d.markers.push(h), h.parent = d
			}
		}
	}

	function ve(a) {
		for (var b = 0; b < a.length; b++) {
			var c = a[b], d = [c.primary.doc];
			Af(c.primary.doc, function (a) {
				d.push(a)
			});
			for (var e = 0; e < c.markers.length; e++) {
				var f = c.markers[e];
				-1 == Ag(d, f.doc) && (f.parent = null, c.markers.splice(e--, 1))
			}
		}
	}

	function we(a, b, c) {
		this.marker = a, this.from = b, this.to = c
	}

	function xe(a, b) {
		if (a)for (var c = 0; c < a.length; ++c) {
			var d = a[c];
			if (d.marker == b)return d
		}
	}

	function ye(a, b) {
		for (var c, d = 0; d < a.length; ++d)a[d] != b && (c || (c = [])).push(a[d]);
		return c
	}

	function ze(a, b) {
		a.markedSpans = a.markedSpans ? a.markedSpans.concat([b]) : [b], b.marker.attachLine(a)
	}

	function Ae(a, b, c) {
		if (a)for (var e, d = 0; d < a.length; ++d) {
			var f = a[d], g = f.marker, h = null == f.from || (g.inclusiveLeft ? f.from <= b : f.from < b);
			if (h || f.from == b && "bookmark" == g.type && (!c || !f.marker.insertLeft)) {
				var i = null == f.to || (g.inclusiveRight ? f.to >= b : f.to > b);
				(e || (e = [])).push(new we(g, f.from, i ? null : f.to))
			}
		}
		return e
	}

	function Be(a, b, c) {
		if (a)for (var e, d = 0; d < a.length; ++d) {
			var f = a[d], g = f.marker, h = null == f.to || (g.inclusiveRight ? f.to >= b : f.to > b);
			if (h || f.from == b && "bookmark" == g.type && (!c || f.marker.insertLeft)) {
				var i = null == f.from || (g.inclusiveLeft ? f.from <= b : f.from < b);
				(e || (e = [])).push(new we(g, i ? null : f.from - b, null == f.to ? null : f.to - b))
			}
		}
		return e
	}

	function Ce(a, b) {
		var c = yb(a, b.from.line) && Cf(a, b.from.line).markedSpans, d = yb(a, b.to.line) && Cf(a, b.to.line).markedSpans;
		if (!c && !d)return null;
		var e = b.from.ch, f = b.to.ch, g = 0 == nb(b.from, b.to), h = Ae(c, e, g), i = Be(d, f, g), j = 1 == b.text.length, k = yg(b.text).length + (j ? e : 0);
		if (h)for (var l = 0; l < h.length; ++l) {
			var m = h[l];
			if (null == m.to) {
				var n = xe(i, m.marker);
				n ? j && (m.to = null == n.to ? null : n.to + k) : m.to = e
			}
		}
		if (i)for (var l = 0; l < i.length; ++l) {
			var m = i[l];
			if (null != m.to && (m.to += k), null == m.from) {
				var n = xe(h, m.marker);
				n || (m.from = k, j && (h || (h = [])).push(m))
			} else m.from += k, j && (h || (h = [])).push(m)
		}
		h && (h = De(h)), i && i != h && (i = De(i));
		var o = [h];
		if (!j) {
			var q, p = b.text.length - 2;
			if (p > 0 && h)for (var l = 0; l < h.length; ++l)null == h[l].to && (q || (q = [])).push(new we(h[l].marker, null, null));
			for (var l = 0; p > l; ++l)o.push(q);
			o.push(i)
		}
		return o
	}

	function De(a) {
		for (var b = 0; b < a.length; ++b) {
			var c = a[b];
			null != c.from && c.from == c.to && c.marker.clearWhenEmpty !== !1 && a.splice(b--, 1)
		}
		return a.length ? a : null
	}

	function Ee(a, b) {
		var c = Uf(a, b), d = Ce(a, b);
		if (!c)return d;
		if (!d)return c;
		for (var e = 0; e < c.length; ++e) {
			var f = c[e], g = d[e];
			if (f && g)a:for (var h = 0; h < g.length; ++h) {
				for (var i = g[h], j = 0; j < f.length; ++j)if (f[j].marker == i.marker)continue a;
				f.push(i)
			} else g && (c[e] = g)
		}
		return c
	}

	function Fe(a, b, c) {
		var d = null;
		if (a.iter(b.line, c.line + 1, function (a) {
				if (a.markedSpans)for (var b = 0; b < a.markedSpans.length; ++b) {
					var c = a.markedSpans[b].marker;
					!c.readOnly || d && -1 != Ag(d, c) || (d || (d = [])).push(c)
				}
			}), !d)return null;
		for (var e = [{
			from: b,
			to:   c
		}], f = 0; f < d.length; ++f)for (var g = d[f], h = g.find(0), i = 0; i < e.length; ++i) {
			var j = e[i];
			if (!(nb(j.to, h.from) < 0 || nb(j.from, h.to) > 0)) {
				var k = [i, 1], l = nb(j.from, h.from), m = nb(j.to, h.to);
				(0 > l || !g.inclusiveLeft && !l) && k.push({
					from: j.from,
					to:   h.from
				}), (m > 0 || !g.inclusiveRight && !m) && k.push({
					from: h.to,
					to:   j.to
				}), e.splice.apply(e, k), i += k.length - 1
			}
		}
		return e
	}

	function Ge(a) {
		var b = a.markedSpans;
		if (b) {
			for (var c = 0; c < b.length; ++c)b[c].marker.detachLine(a);
			a.markedSpans = null
		}
	}

	function He(a, b) {
		if (b) {
			for (var c = 0; c < b.length; ++c)b[c].marker.attachLine(a);
			a.markedSpans = b
		}
	}

	function Ie(a) {
		return a.inclusiveLeft ? -1 : 0
	}

	function Je(a) {
		return a.inclusiveRight ? 1 : 0
	}

	function Ke(a, b) {
		var c = a.lines.length - b.lines.length;
		if (0 != c)return c;
		var d = a.find(), e = b.find(), f = nb(d.from, e.from) || Ie(a) - Ie(b);
		if (f)return -f;
		var g = nb(d.to, e.to) || Je(a) - Je(b);
		return g ? g : b.id - a.id
	}

	function Le(a, b) {
		var d, c = v && a.markedSpans;
		if (c)for (var e, f = 0; f < c.length; ++f)e = c[f], e.marker.collapsed && null == (b ? e.from : e.to) && (!d || Ke(d, e.marker) < 0) && (d = e.marker);
		return d
	}

	function Me(a) {
		return Le(a, !0)
	}

	function Ne(a) {
		return Le(a, !1)
	}

	function Oe(a, b, c, d, e) {
		var f = Cf(a, b), g = v && f.markedSpans;
		if (g)for (var h = 0; h < g.length; ++h) {
			var i = g[h];
			if (i.marker.collapsed) {
				var j = i.marker.find(0), k = nb(j.from, c) || Ie(i.marker) - Ie(e), l = nb(j.to, d) || Je(i.marker) - Je(e);
				if (!(k >= 0 && 0 >= l || 0 >= k && l >= 0) && (0 >= k && (nb(j.to, c) || Je(i.marker) - Ie(e)) > 0 || k >= 0 && (nb(j.from, d) || Ie(i.marker) - Je(e)) < 0))return !0
			}
		}
	}

	function Pe(a) {
		for (var b; b = Me(a);)a = b.find(-1, !0).line;
		return a
	}

	function Qe(a) {
		for (var b, c; b = Ne(a);)a = b.find(1, !0).line, (c || (c = [])).push(a);
		return c
	}

	function Re(a, b) {
		var c = Cf(a, b), d = Pe(c);
		return c == d ? b : Gf(d)
	}

	function Se(a, b) {
		if (b > a.lastLine())return b;
		var d, c = Cf(a, b);
		if (!Te(a, c))return b;
		for (; d = Ne(c);)c = d.find(1, !0).line;
		return Gf(c) + 1
	}

	function Te(a, b) {
		var c = v && b.markedSpans;
		if (c)for (var d, e = 0; e < c.length; ++e)if (d = c[e], d.marker.collapsed) {
			if (null == d.from)return !0;
			if (!d.marker.widgetNode && 0 == d.from && d.marker.inclusiveLeft && Ue(a, b, d))return !0
		}
	}

	function Ue(a, b, c) {
		if (null == c.to) {
			var d = c.marker.find(1, !0);
			return Ue(a, d.line, xe(d.line.markedSpans, c.marker))
		}
		if (c.marker.inclusiveRight && c.to == b.text.length)return !0;
		for (var e, f = 0; f < b.markedSpans.length; ++f)if (e = b.markedSpans[f], e.marker.collapsed && !e.marker.widgetNode && e.from == c.to && (null == e.to || e.to != c.from) && (e.marker.inclusiveLeft || c.marker.inclusiveRight) && Ue(a, b, e))return !0
	}

	function We(a, b, c) {
		If(b) < (a.curOp && a.curOp.scrollTop || a.doc.scrollTop) && Pd(a, null, c)
	}

	function Xe(a) {
		return null != a.height ? a.height : (Pg(document.body, a.node) || Og(a.cm.display.measure, Lg("div", [a.node], null, "position: relative")), a.height = a.node.offsetHeight)
	}

	function Ye(a, b, c, d) {
		var e = new Ve(a, c, d);
		return e.noHScroll && (a.display.alignWidgets = !0), Td(a.doc, b, "widget", function (b) {
			var c = b.widgets || (b.widgets = []);
			if (null == e.insertAt ? c.push(e) : c.splice(Math.min(c.length - 1, Math.max(0, e.insertAt)), 0, e), e.line = b, !Te(a.doc, b)) {
				var d = If(b) < a.doc.scrollTop;
				Ff(b, b.height + Xe(e)), d && Pd(a, null, e.height), a.curOp.forceUpdate = !0
			}
			return !0
		}), e
	}

	function $e(a, b, c, d) {
		a.text = b, a.stateAfter && (a.stateAfter = null), a.styles && (a.styles = null), null != a.order && (a.order = null), Ge(a), He(a, c);
		var e = d ? d(a) : 1;
		e != a.height && Ff(a, e)
	}

	function _e(a) {
		a.parent = null, Ge(a)
	}

	function af(a, b) {
		if (a)for (; ;) {
			var c = a.match(/(?:^|\s+)line-(background-)?(\S+)/);
			if (!c)break;
			a = a.slice(0, c.index) + a.slice(c.index + c[0].length);
			var d = c[1] ? "bgClass" : "textClass";
			null == b[d] ? b[d] = c[2] : new RegExp("(?:^|s)" + c[2] + "(?:$|s)").test(b[d]) || (b[d] += " " + c[2])
		}
		return a
	}

	function bf(a, b) {
		if (a.blankLine)return a.blankLine(b);
		if (a.innerMode) {
			var c = w.innerMode(a, b);
			return c.mode.blankLine ? c.mode.blankLine(c.state) : void 0
		}
	}

	function cf(a, b, c) {
		for (var d = 0; 10 > d; d++) {
			var e = a.token(b, c);
			if (b.pos > b.start)return e
		}
		throw new Error("Mode " + a.name + " failed to advance stream.")
	}

	function df(a, b, c, d, e, f, g) {
		var h = c.flattenSpans;
		null == h && (h = a.options.flattenSpans);
		var l, i = 0, j = null, k = new ne(b, a.options.tabSize);
		for ("" == b && af(bf(c, d), f); !k.eol();) {
			if (k.pos > a.options.maxHighlightLength ? (h = !1, g && gf(a, b, d, k.pos), k.pos = b.length, l = null) : l = af(cf(c, k, d), f), a.options.addModeClass) {
				var m = w.innerMode(c, d).mode.name;
				m && (l = "m-" + (l ? m + " " + l : m))
			}
			h && j == l || (i < k.start && e(k.start, j), i = k.start, j = l), k.start = k.pos
		}
		for (; i < k.pos;) {
			var n = Math.min(k.pos, i + 5e4);
			e(n, j), i = n
		}
	}

	function ef(a, b, c, d) {
		var e = [a.state.modeGen], f = {};
		df(a, b.text, a.doc.mode, c, function (a, b) {
			e.push(a, b)
		}, f, d);
		for (var g = 0; g < a.state.overlays.length; ++g) {
			var h = a.state.overlays[g], i = 1, j = 0;
			df(a, b.text, h.mode, !0, function (a, b) {
				for (var c = i; a > j;) {
					var d = e[i];
					d > a && e.splice(i, 1, a, e[i + 1], d), i += 2, j = Math.min(a, d)
				}
				if (b)if (h.opaque)e.splice(c, i - c, a, "cm-overlay " + b), i = c + 2; else for (; i > c; c += 2) {
					var f = e[c + 1];
					e[c + 1] = (f ? f + " " : "") + "cm-overlay " + b
				}
			}, f)
		}
		return {styles: e, classes: f.bgClass || f.textClass ? f : null}
	}

	function ff(a, b) {
		if (!b.styles || b.styles[0] != a.state.modeGen) {
			var c = ef(a, b, b.stateAfter = Ub(a, Gf(b)));
			b.styles = c.styles, c.classes ? b.styleClasses = c.classes : b.styleClasses && (b.styleClasses = null)
		}
		return b.styles
	}

	function gf(a, b, c, d) {
		var e = a.doc.mode, f = new ne(b, a.options.tabSize);
		for (f.start = f.pos = d || 0, "" == b && bf(e, c); !f.eol() && f.pos <= a.options.maxHighlightLength;)cf(e, f, c), f.start = f.pos
	}

	function kf(a, b) {
		if (!a || /^\s*$/.test(a))return null;
		var c = b.addModeClass ? jf : hf;
		return c[a] || (c[a] = a.replace(/\S+/g, "cm-$&"))
	}

	function lf(a, b) {
		var c = Lg("span", null, null, f ? "padding-right: .1px" : null), e = {
			pre:     Lg("pre", [c]),
			content: c,
			col:     0,
			pos:     0,
			cm:      a
		};
		b.measure = {};
		for (var g = 0; g <= (b.rest ? b.rest.length : 0); g++) {
			var i, h = g ? b.rest[g - 1] : b.line;
			e.pos = 0, e.addToken = nf, (d || f) && a.getOption("lineWrapping") && (e.addToken = of(e.addToken)), dh(a.display.measure) && (i = Jf(h)) && (e.addToken = pf(e.addToken, i)), e.map = [], rf(h, e, ff(a, h)), h.styleClasses && (h.styleClasses.bgClass && (e.bgClass = Ug(h.styleClasses.bgClass, e.bgClass || "")), h.styleClasses.textClass && (e.textClass = Ug(h.styleClasses.textClass, e.textClass || ""))), 0 == e.map.length && e.map.push(0, 0, e.content.appendChild(bh(a.display.measure))), 0 == g ? (b.measure.map = e.map, b.measure.cache = {}) : ((b.measure.maps || (b.measure.maps = [])).push(e.map), (b.measure.caches || (b.measure.caches = [])).push({}))
		}
		return fg(a, "renderLine", a, b.line, e.pre), e.pre.className && (e.textClass = Ug(e.pre.className, e.textClass || "")), e
	}

	function mf(a) {
		var b = Lg("span", "\u2022", "cm-invalidchar");
		return b.title = "\\u" + a.charCodeAt(0).toString(16), b
	}

	function nf(a, b, c, f, g, h) {
		if (b) {
			var i = a.cm.options.specialChars, j = !1;
			if (i.test(b))for (var k = document.createDocumentFragment(), l = 0; ;) {
				i.lastIndex = l;
				var m = i.exec(b), n = m ? m.index - l : b.length - l;
				if (n) {
					var o = document.createTextNode(b.slice(l, l + n));
					d && 9 > e ? k.appendChild(Lg("span", [o])) : k.appendChild(o), a.map.push(a.pos, a.pos + n, o), a.col += n, a.pos += n
				}
				if (!m)break;
				if (l += n + 1, "	" == m[0]) {
					var p = a.cm.options.tabSize, q = p - a.col % p, o = k.appendChild(Lg("span", xg(q), "cm-tab"));
					a.col += q
				} else {
					var o = a.cm.options.specialCharPlaceholder(m[0]);
					d && 9 > e ? k.appendChild(Lg("span", [o])) : k.appendChild(o), a.col += 1
				}
				a.map.push(a.pos, a.pos + 1, o), a.pos++
			} else {
				a.col += b.length;
				var k = document.createTextNode(b);
				a.map.push(a.pos, a.pos + b.length, k), d && 9 > e && (j = !0), a.pos += b.length
			}
			if (c || f || g || j) {
				var r = c || "";
				f && (r += f), g && (r += g);
				var s = Lg("span", [k], r);
				return h && (s.title = h), a.content.appendChild(s)
			}
			a.content.appendChild(k)
		}
	}

	function of(a) {
		function b(a) {
			for (var b = " ", c = 0; c < a.length - 2; ++c)b += c % 2 ? " " : "\xa0";
			return b += " "
		}

		return function (c, d, e, f, g, h) {
			a(c, d.replace(/ {3,}/g, b), e, f, g, h)
		}
	}

	function pf(a, b) {
		return function (c, d, e, f, g, h) {
			e = e ? e + " cm-force-border" : "cm-force-border";
			for (var i = c.pos, j = i + d.length; ;) {
				for (var k = 0; k < b.length; k++) {
					var l = b[k];
					if (l.to > i && l.from <= i)break
				}
				if (l.to >= j)return a(c, d, e, f, g, h);
				a(c, d.slice(0, l.to - i), e, f, null, h), f = null, d = d.slice(l.to - i), i = l.to
			}
		}
	}

	function qf(a, b, c, d) {
		var e = !d && c.widgetNode;
		e && (a.map.push(a.pos, a.pos + b, e), a.content.appendChild(e)), a.pos += b
	}

	function rf(a, b, c) {
		var d = a.markedSpans, e = a.text, f = 0;
		if (d)for (var k, m, n, o, p, q, h = e.length, i = 0, g = 1, j = "", l = 0; ;) {
			if (l == i) {
				m = n = o = p = "", q = null, l = 1 / 0;
				for (var r = [], s = 0; s < d.length; ++s) {
					var t = d[s], u = t.marker;
					t.from <= i && (null == t.to || t.to > i) ? (null != t.to && l > t.to && (l = t.to, n = ""), u.className && (m += " " + u.className), u.startStyle && t.from == i && (o += " " + u.startStyle), u.endStyle && t.to == l && (n += " " + u.endStyle), u.title && !p && (p = u.title), u.collapsed && (!q || Ke(q.marker, u) < 0) && (q = t)) : t.from > i && l > t.from && (l = t.from), "bookmark" == u.type && t.from == i && u.widgetNode && r.push(u)
				}
				if (q && (q.from || 0) == i && (qf(b, (null == q.to ? h + 1 : q.to) - i, q.marker, null == q.from), null == q.to))return;
				if (!q && r.length)for (var s = 0; s < r.length; ++s)qf(b, 0, r[s])
			}
			if (i >= h)break;
			for (var v = Math.min(h, l); ;) {
				if (j) {
					var w = i + j.length;
					if (!q) {
						var x = w > v ? j.slice(0, v - i) : j;
						b.addToken(b, x, k ? k + m : m, o, i + x.length == l ? n : "", p)
					}
					if (w >= v) {
						j = j.slice(v - i), i = v;
						break
					}
					i = w, o = ""
				}
				j = e.slice(f, f = c[g++]), k = kf(c[g++], b.cm.options)
			}
		} else for (var g = 1; g < c.length; g += 2)b.addToken(b, e.slice(f, f = c[g]), kf(c[g + 1], b.cm.options))
	}

	function sf(a, b) {
		return 0 == b.from.ch && 0 == b.to.ch && "" == yg(b.text) && (!a.cm || a.cm.options.wholeLineUpdateBefore)
	}

	function tf(a, b, c, d) {
		function e(a) {
			return c ? c[a] : null
		}

		function f(a, c, e) {
			$e(a, c, e, d), ig(a, "change", a, b)
		}

		var g = b.from, h = b.to, i = b.text, j = Cf(a, g.line), k = Cf(a, h.line), l = yg(i), m = e(i.length - 1), n = h.line - g.line;
		if (sf(a, b)) {
			for (var o = 0, p = []; o < i.length - 1; ++o)p.push(new Ze(i[o], e(o), d));
			f(k, k.text, m), n && a.remove(g.line, n), p.length && a.insert(g.line, p)
		} else if (j == k)if (1 == i.length)f(j, j.text.slice(0, g.ch) + l + j.text.slice(h.ch), m); else {
			for (var p = [], o = 1; o < i.length - 1; ++o)p.push(new Ze(i[o], e(o), d));
			p.push(new Ze(l + j.text.slice(h.ch), m, d)), f(j, j.text.slice(0, g.ch) + i[0], e(0)), a.insert(g.line + 1, p)
		} else if (1 == i.length)f(j, j.text.slice(0, g.ch) + i[0] + k.text.slice(h.ch), e(0)), a.remove(g.line + 1, n); else {
			f(j, j.text.slice(0, g.ch) + i[0], e(0)), f(k, l + k.text.slice(h.ch), m);
			for (var o = 1, p = []; o < i.length - 1; ++o)p.push(new Ze(i[o], e(o), d));
			n > 1 && a.remove(g.line + 1, n - 1), a.insert(g.line + 1, p)
		}
		ig(a, "change", a, b)
	}

	function uf(a) {
		this.lines = a, this.parent = null;
		for (var b = 0, c = 0; b < a.length; ++b)a[b].parent = this, c += a[b].height;
		this.height = c
	}

	function vf(a) {
		this.children = a;
		for (var b = 0, c = 0, d = 0; d < a.length; ++d) {
			var e = a[d];
			b += e.chunkSize(), c += e.height, e.parent = this
		}
		this.size = b, this.height = c, this.parent = null
	}

	function Af(a, b, c) {
		function d(a, e, f) {
			if (a.linked)for (var g = 0; g < a.linked.length; ++g) {
				var h = a.linked[g];
				if (h.doc != e) {
					var i = f && h.sharedHist;
					(!c || i) && (b(h.doc, i), d(h.doc, a, i))
				}
			}
		}

		d(a, null, !0)
	}

	function Bf(a, b) {
		if (b.cm)throw new Error("This document is already in use.");
		a.doc = b, b.cm = a, C(a), y(a), a.options.lineWrapping || J(a), a.options.mode = b.modeOption, Ec(a)
	}

	function Cf(a, b) {
		if (b -= a.first, 0 > b || b >= a.size)throw new Error("There is no line " + (b + a.first) + " in the document.");
		for (var c = a; !c.lines;)for (var d = 0; ; ++d) {
			var e = c.children[d], f = e.chunkSize();
			if (f > b) {
				c = e;
				break
			}
			b -= f
		}
		return c.lines[b]
	}

	function Df(a, b, c) {
		var d = [], e = b.line;
		return a.iter(b.line, c.line + 1, function (a) {
			var f = a.text;
			e == c.line && (f = f.slice(0, c.ch)), e == b.line && (f = f.slice(b.ch)), d.push(f), ++e
		}), d
	}

	function Ef(a, b, c) {
		var d = [];
		return a.iter(b, c, function (a) {
			d.push(a.text)
		}), d
	}

	function Ff(a, b) {
		var c = b - a.height;
		if (c)for (var d = a; d; d = d.parent)d.height += c
	}

	function Gf(a) {
		if (null == a.parent)return null;
		for (var b = a.parent, c = Ag(b.lines, a), d = b.parent; d; b = d, d = d.parent)for (var e = 0; d.children[e] != b; ++e)c += d.children[e].chunkSize();
		return c + b.first
	}

	function Hf(a, b) {
		var c = a.first;
		a:do {
			for (var d = 0; d < a.children.length; ++d) {
				var e = a.children[d], f = e.height;
				if (f > b) {
					a = e;
					continue a
				}
				b -= f, c += e.chunkSize()
			}
			return c
		} while (!a.lines);
		for (var d = 0; d < a.lines.length; ++d) {
			var g = a.lines[d], h = g.height;
			if (h > b)break;
			b -= h
		}
		return c + d
	}

	function If(a) {
		a = Pe(a);
		for (var b = 0, c = a.parent, d = 0; d < c.lines.length; ++d) {
			var e = c.lines[d];
			if (e == a)break;
			b += e.height
		}
		for (var f = c.parent; f; c = f, f = c.parent)for (var d = 0; d < f.children.length; ++d) {
			var g = f.children[d];
			if (g == c)break;
			b += g.height
		}
		return b
	}

	function Jf(a) {
		var b = a.order;
		return null == b && (b = a.order = vh(a.text)), b
	}

	function Kf(a) {
		this.done = [], this.undone = [], this.undoDepth = 1 / 0, this.lastModTime = this.lastSelTime = 0, this.lastOp = null, this.lastOrigin = this.lastSelOrigin = null, this.generation = this.maxGeneration = a || 1
	}

	function Lf(a, b) {
		var c = {from: ob(b.from), to: yd(b), text: Df(a, b.from, b.to)};
		return Sf(a, c, b.from.line, b.to.line + 1), Af(a, function (a) {
			Sf(a, c, b.from.line, b.to.line + 1)
		}, !0), c
	}

	function Mf(a) {
		for (; a.length;) {
			var b = yg(a);
			if (!b.ranges)break;
			a.pop()
		}
	}

	function Nf(a, b) {
		return b ? (Mf(a.done), yg(a.done)) : a.done.length && !yg(a.done).ranges ? yg(a.done) : a.done.length > 1 && !a.done[a.done.length - 2].ranges ? (a.done.pop(), yg(a.done)) : void 0
	}

	function Of(a, b, c, d) {
		var e = a.history;
		e.undone.length = 0;
		var g, f = +new Date;
		if ((e.lastOp == d || e.lastOrigin == b.origin && b.origin && ("+" == b.origin.charAt(0) && a.cm && e.lastModTime > f - a.cm.options.historyEventDelay || "*" == b.origin.charAt(0))) && (g = Nf(e, e.lastOp == d))) {
			var h = yg(g.changes);
			0 == nb(b.from, b.to) && 0 == nb(b.from, h.to) ? h.to = yd(b) : g.changes.push(Lf(a, b))
		} else {
			var i = yg(e.done);
			for (i && i.ranges || Rf(a.sel, e.done), g = {
				changes:    [Lf(a, b)],
				generation: e.generation
			}, e.done.push(g); e.done.length > e.undoDepth;)e.done.shift(), e.done[0].ranges || e.done.shift()
		}
		e.done.push(c), e.generation = ++e.maxGeneration, e.lastModTime = e.lastSelTime = f, e.lastOp = d, e.lastOrigin = e.lastSelOrigin = b.origin, h || fg(a, "historyAdded")
	}

	function Pf(a, b, c, d) {
		var e = b.charAt(0);
		return "*" == e || "+" == e && c.ranges.length == d.ranges.length && c.somethingSelected() == d.somethingSelected() && new Date - a.history.lastSelTime <= (a.cm ? a.cm.options.historyEventDelay : 500)
	}

	function Qf(a, b, c, d) {
		var e = a.history, f = d && d.origin;
		c == e.lastOp || f && e.lastSelOrigin == f && (e.lastModTime == e.lastSelTime && e.lastOrigin == f || Pf(a, f, yg(e.done), b)) ? e.done[e.done.length - 1] = b : Rf(b, e.done), e.lastSelTime = +new Date, e.lastSelOrigin = f, e.lastOp = c, d && d.clearRedo !== !1 && Mf(e.undone)
	}

	function Rf(a, b) {
		var c = yg(b);
		c && c.ranges && c.equals(a) || b.push(a)
	}

	function Sf(a, b, c, d) {
		var e = b["spans_" + a.id], f = 0;
		a.iter(Math.max(a.first, c), Math.min(a.first + a.size, d), function (c) {
			c.markedSpans && ((e || (e = b["spans_" + a.id] = {}))[f] = c.markedSpans), ++f
		})
	}

	function Tf(a) {
		if (!a)return null;
		for (var c, b = 0; b < a.length; ++b)a[b].marker.explicitlyCleared ? c || (c = a.slice(0, b)) : c && c.push(a[b]);
		return c ? c.length ? c : null : a
	}

	function Uf(a, b) {
		var c = b["spans_" + a.id];
		if (!c)return null;
		for (var d = 0, e = []; d < b.text.length; ++d)e.push(Tf(c[d]));
		return e
	}

	function Vf(a, b, c) {
		for (var d = 0, e = []; d < a.length; ++d) {
			var f = a[d];
			if (f.ranges)e.push(c ? rb.prototype.deepCopy.call(f) : f); else {
				var g = f.changes, h = [];
				e.push({changes: h});
				for (var i = 0; i < g.length; ++i) {
					var k, j = g[i];
					if (h.push({
							from: j.from,
							to:   j.to,
							text: j.text
						}), b)for (var l in j)(k = l.match(/^spans_(\d+)$/)) && Ag(b, Number(k[1])) > -1 && (yg(h)[l] = j[l], delete j[l])
				}
			}
		}
		return e
	}

	function Wf(a, b, c, d) {
		c < a.line ? a.line += d : b < a.line && (a.line = b, a.ch = 0)
	}

	function Xf(a, b, c, d) {
		for (var e = 0; e < a.length; ++e) {
			var f = a[e], g = !0;
			if (f.ranges) {
				f.copied || (f = a[e] = f.deepCopy(), f.copied = !0);
				for (var h = 0; h < f.ranges.length; h++)Wf(f.ranges[h].anchor, b, c, d), Wf(f.ranges[h].head, b, c, d)
			} else {
				for (var h = 0; h < f.changes.length; ++h) {
					var i = f.changes[h];
					if (c < i.from.line)i.from = mb(i.from.line + d, i.from.ch), i.to = mb(i.to.line + d, i.to.ch); else if (b <= i.to.line) {
						g = !1;
						break
					}
				}
				g || (a.splice(0, e + 1), e = 0)
			}
		}
	}

	function Yf(a, b) {
		var c = b.from.line, d = b.to.line, e = b.text.length - (d - c) - 1;
		Xf(a.done, c, d, e), Xf(a.undone, c, d, e)
	}

	function _f(a) {
		return null != a.defaultPrevented ? a.defaultPrevented : 0 == a.returnValue
	}

	function bg(a) {
		return a.target || a.srcElement
	}

	function cg(a) {
		var b = a.which;
		return null == b && (1 & a.button ? b = 1 : 2 & a.button ? b = 3 : 4 & a.button && (b = 2)), p && a.ctrlKey && 1 == b && (b = 3), b
	}

	function ig(a, b) {
		function e(a) {
			return function () {
				a.apply(null, d)
			}
		}

		var c = a._handlers && a._handlers[b];
		if (c) {
			var d = Array.prototype.slice.call(arguments, 2);
			gg || (++hg, gg = [], setTimeout(jg, 0));
			for (var f = 0; f < c.length; ++f)gg.push(e(c[f]))
		}
	}

	function jg() {
		--hg;
		var a = gg;
		gg = null;
		for (var b = 0; b < a.length; ++b)a[b]()
	}

	function kg(a, b, c) {
		return fg(a, c || b.type, a, b), _f(b) || b.codemirrorIgnore
	}

	function lg(a) {
		var b = a._handlers && a._handlers.cursorActivity;
		if (b)for (var c = a.curOp.cursorActivityHandlers || (a.curOp.cursorActivityHandlers = []), d = 0; d < b.length; ++d)-1 == Ag(c, b[d]) && c.push(b[d])
	}

	function mg(a, b) {
		var c = a._handlers && a._handlers[b];
		return c && c.length > 0
	}

	function ng(a) {
		a.prototype.on = function (a, b) {
			dg(this, a, b)
		}, a.prototype.off = function (a, b) {
			eg(this, a, b)
		}
	}

	function tg() {
		this.id = null
	}

	function vg(a, b, c) {
		for (var d = 0, e = 0; ;) {
			var f = a.indexOf("	", d);
			-1 == f && (f = a.length);
			var g = f - d;
			if (f == a.length || e + g >= b)return d + Math.min(g, b - e);
			if (e += f - d, e += c - e % c, d = f + 1, e >= b)return d
		}
	}

	function xg(a) {
		for (; wg.length <= a;)wg.push(yg(wg) + " ");
		return wg[a]
	}

	function yg(a) {
		return a[a.length - 1]
	}

	function Ag(a, b) {
		for (var c = 0; c < a.length; ++c)if (a[c] == b)return c;
		return -1
	}

	function Bg(a, b) {
		for (var c = [], d = 0; d < a.length; d++)c[d] = b(a[d], d);
		return c
	}

	function Cg(a, b) {
		var c;
		if (Object.create)c = Object.create(a); else {
			var d = function () {
			};
			d.prototype = a, c = new d
		}
		return b && Dg(b, c), c
	}

	function Dg(a, b, c) {
		b || (b = {});
		for (var d in a)!a.hasOwnProperty(d) || c === !1 && b.hasOwnProperty(d) || (b[d] = a[d]);
		return b
	}

	function Eg(a) {
		var b = Array.prototype.slice.call(arguments, 1);
		return function () {
			return a.apply(null, b)
		}
	}

	function Hg(a, b) {
		return b ? b.source.indexOf("\\w") > -1 && Gg(a) ? !0 : b.test(a) : Gg(a)
	}

	function Ig(a) {
		for (var b in a)if (a.hasOwnProperty(b) && a[b])return !1;
		return !0
	}

	function Kg(a) {
		return a.charCodeAt(0) >= 768 && Jg.test(a)
	}

	function Lg(a, b, c, d) {
		var e = document.createElement(a);
		if (c && (e.className = c), d && (e.style.cssText = d), "string" == typeof b)e.appendChild(document.createTextNode(b)); else if (b)for (var f = 0; f < b.length; ++f)e.appendChild(b[f]);
		return e
	}

	function Ng(a) {
		for (var b = a.childNodes.length; b > 0; --b)a.removeChild(a.firstChild);
		return a
	}

	function Og(a, b) {
		return Ng(a).appendChild(b)
	}

	function Pg(a, b) {
		if (a.contains)return a.contains(b);
		for (; b = b.parentNode;)if (b == a)return !0
	}

	function Qg() {
		return document.activeElement
	}

	function Rg(a) {
		return new RegExp("\\b" + a + "\\b\\s*")
	}

	function Sg(a, b) {
		var c = Rg(b);
		c.test(a.className) && (a.className = a.className.replace(c, ""))
	}

	function Tg(a, b) {
		Rg(b).test(a.className) || (a.className += " " + b)
	}

	function Ug(a, b) {
		for (var c = a.split(" "), d = 0; d < c.length; d++)c[d] && !Rg(c[d]).test(b) && (b += " " + c[d]);
		return b
	}

	function Vg(a) {
		if (document.body.getElementsByClassName)for (var b = document.body.getElementsByClassName("CodeMirror"), c = 0; c < b.length; c++) {
			var d = b[c].CodeMirror;
			d && a(d)
		}
	}

	function Xg() {
		Wg || (Yg(), Wg = !0)
	}

	function Yg() {
		var a;
		dg(window, "resize", function () {
			null == a && (a = setTimeout(function () {
				a = null, $g = null, Vg(Tc)
			}, 100))
		}), dg(window, "blur", function () {
			Vg(vd)
		})
	}

	function _g(a) {
		if (null != $g)return $g;
		var b = Lg("div", null, null, "width: 50px; height: 50px; overflow-x: scroll");
		return Og(a, b), b.offsetWidth && ($g = b.offsetHeight - b.clientHeight), $g || 0
	}

	function bh(a) {
		if (null == ah) {
			var b = Lg("span", "\u200b");
			Og(a, Lg("span", [b, document.createTextNode("x")])), 0 != a.firstChild.offsetHeight && (ah = b.offsetWidth <= 1 && b.offsetHeight > 2 && d && 8 > e)
		}
		return ah ? Lg("span", "\u200b") : Lg("span", "\xa0", null, "display: inline-block; width: 1px; margin-right: -1px")
	}

	function dh(a) {
		if (null != ch)return ch;
		var b = Og(a, document.createTextNode("A\u062eA")), c = Mg(b, 0, 1).getBoundingClientRect();
		if (c.left == c.right)return !1;
		var d = Mg(b, 1, 2).getBoundingClientRect();
		return ch = d.right - c.right < 3
	}

	function ih(a, b, c, d) {
		if (!a)return d(b, c, "ltr");
		for (var e = !1, f = 0; f < a.length; ++f) {
			var g = a[f];
			(g.from < c && g.to > b || b == c && g.to == b) && (d(Math.max(g.from, b), Math.min(g.to, c), 1 == g.level ? "rtl" : "ltr"), e = !0)
		}
		e || d(b, c, "ltr")
	}

	function jh(a) {
		return a.level % 2 ? a.to : a.from
	}

	function kh(a) {
		return a.level % 2 ? a.from : a.to
	}

	function lh(a) {
		var b = Jf(a);
		return b ? jh(b[0]) : 0
	}

	function mh(a) {
		var b = Jf(a);
		return b ? kh(yg(b)) : a.text.length
	}

	function nh(a, b) {
		var c = Cf(a.doc, b), d = Pe(c);
		d != c && (b = Gf(d));
		var e = Jf(d), f = e ? e[0].level % 2 ? mh(d) : lh(d) : 0;
		return mb(b, f)
	}

	function oh(a, b) {
		for (var c, d = Cf(a.doc, b); c = Ne(d);)d = c.find(1, !0).line, b = null;
		var e = Jf(d), f = e ? e[0].level % 2 ? lh(d) : mh(d) : d.text.length;
		return mb(null == b ? Gf(d) : b, f)
	}

	function ph(a, b, c) {
		var d = a[0].level;
		return b == d ? !0 : c == d ? !1 : c > b
	}

	function rh(a, b) {
		qh = null;
		for (var d, c = 0; c < a.length; ++c) {
			var e = a[c];
			if (e.from < b && e.to > b)return c;
			if (e.from == b || e.to == b) {
				if (null != d)return ph(a, e.level, a[d].level) ? (e.from != e.to && (qh = d), c) : (e.from != e.to && (qh = c), d);
				d = c
			}
		}
		return d
	}

	function sh(a, b, c, d) {
		if (!d)return b + c;
		do b += c; while (b > 0 && Kg(a.text.charAt(b)));
		return b
	}

	function th(a, b, c, d) {
		var e = Jf(a);
		if (!e)return uh(a, b, c, d);
		for (var f = rh(e, b), g = e[f], h = sh(a, b, g.level % 2 ? -c : c, d); ;) {
			if (h > g.from && h < g.to)return h;
			if (h == g.from || h == g.to)return rh(e, h) == f ? h : (g = e[f += c], c > 0 == g.level % 2 ? g.to : g.from);
			if (g = e[f += c], !g)return null;
			h = c > 0 == g.level % 2 ? sh(a, g.to, -1, d) : sh(a, g.from, 1, d)
		}
	}

	function uh(a, b, c, d) {
		var e = b + c;
		if (d)for (; e > 0 && Kg(a.text.charAt(e));)e += c;
		return 0 > e || e > a.text.length ? null : e
	}

	var a = /gecko\/\d/i.test(navigator.userAgent), b = /MSIE \d/.test(navigator.userAgent), c = /Trident\/(?:[7-9]|\d{2,})\..*rv:(\d+)/.exec(navigator.userAgent), d = b || c, e = d && (b ? document.documentMode || 6 : c[1]), f = /WebKit\//.test(navigator.userAgent), g = f && /Qt\/\d+\.\d+/.test(navigator.userAgent), h = /Chrome\//.test(navigator.userAgent), i = /Opera\//.test(navigator.userAgent), j = /Apple Computer/.test(navigator.vendor), k = /KHTML\//.test(navigator.userAgent), l = /Mac OS X 1\d\D([8-9]|\d\d)\D/.test(navigator.userAgent), m = /PhantomJS/.test(navigator.userAgent), n = /AppleWebKit/.test(navigator.userAgent) && /Mobile\/\w+/.test(navigator.userAgent), o = n || /Android|webOS|BlackBerry|Opera Mini|Opera Mobi|IEMobile/i.test(navigator.userAgent), p = n || /Mac/.test(navigator.platform), q = /win/i.test(navigator.platform), r = i && navigator.userAgent.match(/Version\/(\d*\.\d*)/);
	r && (r = Number(r[1])), r && r >= 15 && (i = !1, f = !0);
	var s = p && (g || i && (null == r || 12.11 > r)), t = a || d && e >= 9, u = !1, v = !1, mb = w.Pos = function (a, b) {
		return this instanceof mb ? (this.line = a, this.ch = b, void 0) : new mb(a, b)
	}, nb = w.cmpPos = function (a, b) {
		return a.line - b.line || a.ch - b.ch
	};
	rb.prototype = {
		primary:              function () {
			return this.ranges[this.primIndex]
		}, equals:            function (a) {
			if (a == this)return !0;
			if (a.primIndex != this.primIndex || a.ranges.length != this.ranges.length)return !1;
			for (var b = 0; b < this.ranges.length; b++) {
				var c = this.ranges[b], d = a.ranges[b];
				if (0 != nb(c.anchor, d.anchor) || 0 != nb(c.head, d.head))return !1
			}
			return !0
		}, deepCopy:          function () {
			for (var a = [], b = 0; b < this.ranges.length; b++)a[b] = new sb(ob(this.ranges[b].anchor), ob(this.ranges[b].head));
			return new rb(a, this.primIndex)
		}, somethingSelected: function () {
			for (var a = 0; a < this.ranges.length; a++)if (!this.ranges[a].empty())return !0;
			return !1
		}, contains:          function (a, b) {
			b || (b = a);
			for (var c = 0; c < this.ranges.length; c++) {
				var d = this.ranges[c];
				if (nb(b, d.from()) >= 0 && nb(a, d.to()) <= 0)return c
			}
			return -1
		}
	}, sb.prototype = {
		from:     function () {
			return qb(this.anchor, this.head)
		}, to:    function () {
			return pb(this.anchor, this.head)
		}, empty: function () {
			return this.head.line == this.anchor.line && this.head.ch == this.anchor.ch
		}
	};
	var sc, Xc, Yc, dc = {left: 0, right: 0, top: 0, bottom: 0}, vc = 0, cd = 0, hd = 0, id = null;
	d ? id = -.53 : a ? id = 15 : h ? id = -.7 : j && (id = -1 / 3);
	var md, pd = null, yd = w.changeEnd = function (a) {
		return a.text ? mb(a.from.line + a.text.length - 1, yg(a.text).length + (1 == a.text.length ? a.from.ch : 0)) : a.to
	};
	w.prototype = {
		constructor:           w, focus: function () {
			window.focus(), Pc(this), Mc(this)
		}, setOption:          function (a, b) {
			var c = this.options, d = c[a];
			(c[a] != b || "mode" == a) && (c[a] = b, Zd.hasOwnProperty(a) && zc(this, Zd[a])(this, b, d))
		}, getOption:          function (a) {
			return this.options[a]
		}, getDoc:             function () {
			return this.doc
		}, addKeyMap:          function (a, b) {
			this.state.keyMaps[b ? "push" : "unshift"](a)
		}, removeKeyMap:       function (a) {
			for (var b = this.state.keyMaps, c = 0; c < b.length; ++c)if (b[c] == a || "string" != typeof b[c] && b[c].name == a)return b.splice(c, 1), !0
		}, addOverlay:         Ac(function (a, b) {
			var c = a.token ? a : w.getMode(this.options, a);
			if (c.startState)throw new Error("Overlays may not be stateful.");
			this.state.overlays.push({mode: c, modeSpec: a, opaque: b && b.opaque}), this.state.modeGen++, Ec(this)
		}), removeOverlay:     Ac(function (a) {
			for (var b = this.state.overlays, c = 0; c < b.length; ++c) {
				var d = b[c].modeSpec;
				if (d == a || "string" == typeof a && d.name == a)return b.splice(c, 1), this.state.modeGen++, Ec(this), void 0
			}
		}), indentLine:        Ac(function (a, b, c) {
			"string" != typeof b && "number" != typeof b && (b = null == b ? this.options.smartIndent ? "smart" : "prev" : b ? "add" : "subtract"), yb(this.doc, a) && Sd(this, a, b, c)
		}), indentSelection:   Ac(function (a) {
			for (var b = this.doc.sel.ranges, c = -1, d = 0; d < b.length; d++) {
				var e = b[d];
				if (e.empty())e.head.line > c && (Sd(this, e.head.line, a, !0), c = e.head.line, d == this.doc.sel.primIndex && Qd(this)); else {
					var f = Math.max(c, e.from().line), g = e.to();
					c = Math.min(this.lastLine(), g.line - (g.ch ? 0 : 1)) + 1;
					for (var h = f; c > h; ++h)Sd(this, h, a)
				}
			}
		}), getTokenAt:        function (a, b) {
			var c = this.doc;
			a = wb(c, a);
			for (var d = Ub(this, a.line, b), e = this.doc.mode, f = Cf(c, a.line), g = new ne(f.text, this.options.tabSize); g.pos < a.ch && !g.eol();) {
				g.start = g.pos;
				var h = cf(e, g, d)
			}
			return {start: g.start, end: g.pos, string: g.current(), type: h || null, state: d}
		}, getTokenTypeAt:     function (a) {
			a = wb(this.doc, a);
			var f, b = ff(this, Cf(this.doc, a.line)), c = 0, d = (b.length - 1) / 2, e = a.ch;
			if (0 == e)f = b[2]; else for (; ;) {
				var g = c + d >> 1;
				if ((g ? b[2 * g - 1] : 0) >= e)d = g; else {
					if (!(b[2 * g + 1] < e)) {
						f = b[2 * g + 2];
						break
					}
					c = g + 1
				}
			}
			var h = f ? f.indexOf("cm-overlay ") : -1;
			return 0 > h ? f : 0 == h ? null : f.slice(0, h - 1)
		}, getModeAt:          function (a) {
			var b = this.doc.mode;
			return b.innerMode ? w.innerMode(b, this.getTokenAt(a).state).mode : b
		}, getHelper:          function (a, b) {
			return this.getHelpers(a, b)[0]
		}, getHelpers:         function (a, b) {
			var c = [];
			if (!ee.hasOwnProperty(b))return ee;
			var d = ee[b], e = this.getModeAt(a);
			if ("string" == typeof e[b])d[e[b]] && c.push(d[e[b]]); else if (e[b])for (var f = 0; f < e[b].length; f++) {
				var g = d[e[b][f]];
				g && c.push(g)
			} else e.helperType && d[e.helperType] ? c.push(d[e.helperType]) : d[e.name] && c.push(d[e.name]);
			for (var f = 0; f < d._global.length; f++) {
				var h = d._global[f];
				h.pred(e, this) && -1 == Ag(c, h.val) && c.push(h.val)
			}
			return c
		}, getStateAfter:      function (a, b) {
			var c = this.doc;
			return a = vb(c, null == a ? c.first + c.size - 1 : a), Ub(this, a + 1, b)
		}, cursorCoords:       function (a, b) {
			var c, d = this.doc.sel.primary();
			return c = null == a ? d.head : "object" == typeof a ? wb(this.doc, a) : a ? d.from() : d.to(), nc(this, c, b || "page")
		}, charCoords:         function (a, b) {
			return mc(this, wb(this.doc, a), b || "page")
		}, coordsChar:         function (a, b) {
			return a = lc(this, a, b || "page"), qc(this, a.left, a.top)
		}, lineAtHeight:       function (a, b) {
			return a = lc(this, {top: a, left: 0}, b || "page").top, Hf(this.doc, a + this.display.viewOffset)
		}, heightAtLine:       function (a, b) {
			var c = !1, d = this.doc.first + this.doc.size - 1;
			a < this.doc.first ? a = this.doc.first : a > d && (a = d, c = !0);
			var e = Cf(this.doc, a);
			return kc(this, e, {top: 0, left: 0}, b || "page").top + (c ? this.doc.height - If(e) : 0)
		}, defaultTextHeight:  function () {
			return tc(this.display)
		}, defaultCharWidth:   function () {
			return uc(this.display)
		}, setGutterMarker:    Ac(function (a, b, c) {
			return Td(this.doc, a, "gutter", function (a) {
				var d = a.gutterMarkers || (a.gutterMarkers = {});
				return d[b] = c, !c && Ig(d) && (a.gutterMarkers = null), !0
			})
		}), clearGutter:       Ac(function (a) {
			var b = this, c = b.doc, d = c.first;
			c.iter(function (c) {
				c.gutterMarkers && c.gutterMarkers[a] && (c.gutterMarkers[a] = null, Fc(b, d, "gutter"), Ig(c.gutterMarkers) && (c.gutterMarkers = null)), ++d
			})
		}), addLineWidget:     Ac(function (a, b, c) {
			return Ye(this, a, b, c)
		}), removeLineWidget:  function (a) {
			a.clear()
		}, lineInfo:           function (a) {
			if ("number" == typeof a) {
				if (!yb(this.doc, a))return null;
				var b = a;
				if (a = Cf(this.doc, a), !a)return null
			} else {
				var b = Gf(a);
				if (null == b)return null
			}
			return {
				line:          b,
				handle:        a,
				text:          a.text,
				gutterMarkers: a.gutterMarkers,
				textClass:     a.textClass,
				bgClass:       a.bgClass,
				wrapClass:     a.wrapClass,
				widgets:       a.widgets
			}
		}, getViewport:        function () {
			return {from: this.display.viewFrom, to: this.display.viewTo}
		}, addWidget:          function (a, b, c, d, e) {
			var f = this.display;
			a = nc(this, wb(this.doc, a));
			var g = a.bottom, h = a.left;
			if (b.style.position = "absolute", f.sizer.appendChild(b), "over" == d)g = a.top; else if ("above" == d || "near" == d) {
				var i = Math.max(f.wrapper.clientHeight, this.doc.height), j = Math.max(f.sizer.clientWidth, f.lineSpace.clientWidth);
				("above" == d || a.bottom + b.offsetHeight > i) && a.top > b.offsetHeight ? g = a.top - b.offsetHeight : a.bottom + b.offsetHeight <= i && (g = a.bottom), h + b.offsetWidth > j && (h = j - b.offsetWidth)
			}
			b.style.top = g + "px", b.style.left = b.style.right = "", "right" == e ? (h = f.sizer.clientWidth - b.offsetWidth, b.style.right = "0px") : ("left" == e ? h = 0 : "middle" == e && (h = (f.sizer.clientWidth - b.offsetWidth) / 2), b.style.left = h + "px"), c && Nd(this, h, g, h + b.offsetWidth, g + b.offsetHeight)
		}, triggerOnKeyDown:   Ac(qd), triggerOnKeyPress: Ac(td), triggerOnKeyUp: Ac(sd), execCommand: function (a) {
			return he.hasOwnProperty(a) ? he[a](this) : void 0
		}, findPosH:           function (a, b, c, d) {
			var e = 1;
			0 > b && (e = -1, b = -b);
			for (var f = 0, g = wb(this.doc, a); b > f && (g = Vd(this.doc, g, e, c, d), !g.hitSide); ++f);
			return g
		}, moveH:              Ac(function (a, b) {
			var c = this;
			c.extendSelectionsBy(function (d) {
				return c.display.shift || c.doc.extend || d.empty() ? Vd(c.doc, d.head, a, b, c.options.rtlMoveVisually) : 0 > a ? d.from() : d.to()
			}, sg)
		}), deleteH:           Ac(function (a, b) {
			var c = this.doc.sel, d = this.doc;
			c.somethingSelected() ? d.replaceSelection("", null, "+delete") : Ud(this, function (c) {
				var e = Vd(d, c.head, a, b, !1);
				return 0 > a ? {from: e, to: c.head} : {from: c.head, to: e}
			})
		}), findPosV:          function (a, b, c, d) {
			var e = 1, f = d;
			0 > b && (e = -1, b = -b);
			for (var g = 0, h = wb(this.doc, a); b > g; ++g) {
				var i = nc(this, h, "div");
				if (null == f ? f = i.left : i.left = f, h = Wd(this, i, e, c), h.hitSide)break
			}
			return h
		}, moveV:              Ac(function (a, b) {
			var c = this, d = this.doc, e = [], f = !c.display.shift && !d.extend && d.sel.somethingSelected();
			if (d.extendSelectionsBy(function (g) {
					if (f)return 0 > a ? g.from() : g.to();
					var h = nc(c, g.head, "div");
					null != g.goalColumn && (h.left = g.goalColumn), e.push(h.left);
					var i = Wd(c, h, a, b);
					return "page" == b && g == d.sel.primary() && Pd(c, null, mc(c, i, "div").top - h.top), i
				}, sg), e.length)for (var g = 0; g < d.sel.ranges.length; g++)d.sel.ranges[g].goalColumn = e[g]
		}), toggleOverwrite:   function (a) {
			(null == a || a != this.state.overwrite) && ((this.state.overwrite = !this.state.overwrite) ? Tg(this.display.cursorDiv, "CodeMirror-overwrite") : Sg(this.display.cursorDiv, "CodeMirror-overwrite"), fg(this, "overwriteToggle", this, this.state.overwrite))
		}, hasFocus:           function () {
			return Qg() == this.display.input
		}, scrollTo:           Ac(function (a, b) {
			(null != a || null != b) && Rd(this), null != a && (this.curOp.scrollLeft = a), null != b && (this.curOp.scrollTop = b)
		}), getScrollInfo:     function () {
			var a = this.display.scroller, b = og;
			return {
				left:         a.scrollLeft,
				top:          a.scrollTop,
				height:       a.scrollHeight - b,
				width:        a.scrollWidth - b,
				clientHeight: a.clientHeight - b,
				clientWidth:  a.clientWidth - b
			}
		}, scrollIntoView:     Ac(function (a, b) {
			if (null == a ? (a = {
					from: this.doc.sel.primary().head,
					to:   null
				}, null == b && (b = this.options.cursorScrollMargin)) : "number" == typeof a ? a = {
					from: mb(a, 0),
					to:   null
				} : null == a.from && (a = {
					from: a,
					to:   null
				}), a.to || (a.to = a.from), a.margin = b || 0, null != a.from.line)Rd(this), this.curOp.scrollToPos = a; else {
				var c = Od(this, Math.min(a.from.left, a.to.left), Math.min(a.from.top, a.to.top) - a.margin, Math.max(a.from.right, a.to.right), Math.max(a.from.bottom, a.to.bottom) + a.margin);
				this.scrollTo(c.scrollLeft, c.scrollTop)
			}
		}), setSize:           Ac(function (a, b) {
			function d(a) {
				return "number" == typeof a || /^\d+$/.test(String(a)) ? a + "px" : a
			}

			var c = this;
			null != a && (c.display.wrapper.style.width = d(a)), null != b && (c.display.wrapper.style.height = d(b)), c.options.lineWrapping && gc(this);
			var e = c.display.viewFrom;
			c.doc.iter(e, c.display.viewTo, function (a) {
				if (a.widgets)for (var b = 0; b < a.widgets.length; b++)if (a.widgets[b].noHScroll) {
					Fc(c, e, "widget");
					break
				}
				++e
			}), c.curOp.forceUpdate = !0, fg(c, "refresh", this)
		}), operation:         function (a) {
			return yc(this, a)
		}, refresh:            Ac(function () {
			var a = this.display.cachedTextHeight;
			Ec(this), this.curOp.forceUpdate = !0, hc(this), this.scrollTo(this.doc.scrollLeft, this.doc.scrollTop), H(this), (null == a || Math.abs(a - tc(this.display)) > .5) && C(this), fg(this, "refresh", this)
		}), swapDoc:           Ac(function (a) {
			var b = this.doc;
			return b.cm = null, Bf(this, a), hc(this), Oc(this), this.scrollTo(a.scrollLeft, a.scrollTop), ig(this, "swapDoc", this, b), b
		}), getInputField:     function () {
			return this.display.input
		}, getWrapperElement:  function () {
			return this.display.wrapper
		}, getScrollerElement: function () {
			return this.display.scroller
		}, getGutterElement:   function () {
			return this.display.gutters
		}
	}, ng(w);
	var Yd = w.defaults = {}, Zd = w.optionHandlers = {}, _d = w.Init = {
		toString: function () {
			return "CodeMirror.Init"
		}
	};
	$d("value", "", function (a, b) {
		a.setValue(b)
	}, !0), $d("mode", null, function (a, b) {
		a.doc.modeOption = b, y(a)
	}, !0), $d("indentUnit", 2, y, !0), $d("indentWithTabs", !1), $d("smartIndent", !0), $d("tabSize", 4, function (a) {
		z(a), hc(a), Ec(a)
	}, !0), $d("specialChars", /[\t\u0000-\u0019\u00ad\u200b\u2028\u2029\ufeff]/g, function (a, b) {
		a.options.specialChars = new RegExp(b.source + (b.test("	") ? "" : "|	"), "g"), a.refresh()
	}, !0), $d("specialCharPlaceholder", mf, function (a) {
		a.refresh()
	}, !0), $d("electricChars", !0), $d("rtlMoveVisually", !q), $d("wholeLineUpdateBefore", !0), $d("theme", "default", function (a) {
		E(a), F(a)
	}, !0), $d("keyMap", "default", D), $d("extraKeys", null), $d("lineWrapping", !1, A, !0), $d("gutters", [], function (a) {
		K(a.options), F(a)
	}, !0), $d("fixedGutter", !0, function (a, b) {
		a.display.gutters.style.left = b ? S(a.display) + "px" : "0", a.refresh()
	}, !0), $d("coverGutterNextToScrollbar", !1, N, !0), $d("lineNumbers", !1, function (a) {
		K(a.options), F(a)
	}, !0), $d("firstLineNumber", 1, F, !0), $d("lineNumberFormatter", function (a) {
		return a
	}, F, !0), $d("showCursorWhenSelecting", !1, Nb, !0), $d("resetSelectionOnContextMenu", !0), $d("readOnly", !1, function (a, b) {
		"nocursor" == b ? (vd(a), a.display.input.blur(), a.display.disabled = !0) : (a.display.disabled = !1, b || Oc(a))
	}), $d("disableInput", !1, function (a, b) {
		b || Oc(a)
	}, !0), $d("dragDrop", !0), $d("cursorBlinkRate", 530), $d("cursorScrollMargin", 0), $d("cursorHeight", 1, Nb, !0), $d("singleCursorHeightPerLine", !0, Nb, !0), $d("workTime", 100), $d("workDelay", 100), $d("flattenSpans", !0, z, !0), $d("addModeClass", !1, z, !0), $d("pollInterval", 100), $d("undoDepth", 200, function (a, b) {
		a.doc.history.undoDepth = b
	}), $d("historyEventDelay", 1250), $d("viewportMargin", 10, function (a) {
		a.refresh()
	}, !0), $d("maxHighlightLength", 1e4, z, !0), $d("moveInputWithCursor", !0, function (a, b) {
		b || (a.display.inputDiv.style.top = a.display.inputDiv.style.left = 0)
	}), $d("tabindex", null, function (a, b) {
		a.display.input.tabIndex = b || ""
	}), $d("autofocus", null);
	var ae = w.modes = {}, be = w.mimeModes = {};
	w.defineMode = function (a, b) {
		if (w.defaults.mode || "null" == a || (w.defaults.mode = a), arguments.length > 2) {
			b.dependencies = [];
			for (var c = 2; c < arguments.length; ++c)b.dependencies.push(arguments[c])
		}
		ae[a] = b
	}, w.defineMIME = function (a, b) {
		be[a] = b
	}, w.resolveMode = function (a) {
		if ("string" == typeof a && be.hasOwnProperty(a))a = be[a]; else if (a && "string" == typeof a.name && be.hasOwnProperty(a.name)) {
			var b = be[a.name];
			"string" == typeof b && (b = {name: b}), a = Cg(b, a), a.name = b.name
		} else if ("string" == typeof a && /^[\w\-]+\/[\w\-]+\+xml$/.test(a))return w.resolveMode("application/xml");
		return "string" == typeof a ? {name: a} : a || {name: "null"}
	}, w.getMode = function (a, b) {
		var b = w.resolveMode(b), c = ae[b.name];
		if (!c)return w.getMode(a, "text/plain");
		var d = c(a, b);
		if (ce.hasOwnProperty(b.name)) {
			var e = ce[b.name];
			for (var f in e)e.hasOwnProperty(f) && (d.hasOwnProperty(f) && (d["_" + f] = d[f]), d[f] = e[f])
		}
		if (d.name = b.name, b.helperType && (d.helperType = b.helperType), b.modeProps)for (var f in b.modeProps)d[f] = b.modeProps[f];
		return d
	}, w.defineMode("null", function () {
		return {
			token: function (a) {
				a.skipToEnd()
			}
		}
	}), w.defineMIME("text/plain", "null");
	var ce = w.modeExtensions = {};
	w.extendMode = function (a, b) {
		var c = ce.hasOwnProperty(a) ? ce[a] : ce[a] = {};
		Dg(b, c)
	}, w.defineExtension = function (a, b) {
		w.prototype[a] = b
	}, w.defineDocExtension = function (a, b) {
		xf.prototype[a] = b
	}, w.defineOption = $d;
	var de = [];
	w.defineInitHook = function (a) {
		de.push(a)
	};
	var ee = w.helpers = {};
	w.registerHelper = function (a, b, c) {
		ee.hasOwnProperty(a) || (ee[a] = w[a] = {_global: []}), ee[a][b] = c
	}, w.registerGlobalHelper = function (a, b, c, d) {
		w.registerHelper(a, b, d), ee[a]._global.push({pred: c, val: d})
	};
	var fe = w.copyState = function (a, b) {
		if (b === !0)return b;
		if (a.copyState)return a.copyState(b);
		var c = {};
		for (var d in b) {
			var e = b[d];
			e instanceof Array && (e = e.concat([])), c[d] = e
		}
		return c
	}, ge = w.startState = function (a, b, c) {
		return a.startState ? a.startState(b, c) : !0
	};
	w.innerMode = function (a, b) {
		for (; a.innerMode;) {
			var c = a.innerMode(b);
			if (!c || c.mode == a)break;
			b = c.state, a = c.mode
		}
		return c || {mode: a, state: b}
	};
	var he = w.commands = {
		selectAll:           function (a) {
			a.setSelection(mb(a.firstLine(), 0), mb(a.lastLine()), qg)
		}, singleSelection:  function (a) {
			a.setSelection(a.getCursor("anchor"), a.getCursor("head"), qg)
		}, killLine:         function (a) {
			Ud(a, function (b) {
				if (b.empty()) {
					var c = Cf(a.doc, b.head.line).text.length;
					return b.head.ch == c && b.head.line < a.lastLine() ? {
						from: b.head,
						to:   mb(b.head.line + 1, 0)
					} : {from: b.head, to: mb(b.head.line, c)}
				}
				return {from: b.from(), to: b.to()}
			})
		}, deleteLine:       function (a) {
			Ud(a, function (b) {
				return {from: mb(b.from().line, 0), to: wb(a.doc, mb(b.to().line + 1, 0))}
			})
		}, delLineLeft:      function (a) {
			Ud(a, function (a) {
				return {from: mb(a.from().line, 0), to: a.from()}
			})
		}, undo:             function (a) {
			a.undo()
		}, redo:             function (a) {
			a.redo()
		}, undoSelection:    function (a) {
			a.undoSelection()
		}, redoSelection:    function (a) {
			a.redoSelection()
		}, goDocStart:       function (a) {
			a.extendSelection(mb(a.firstLine(), 0))
		}, goDocEnd:         function (a) {
			a.extendSelection(mb(a.lastLine()))
		}, goLineStart:      function (a) {
			a.extendSelectionsBy(function (b) {
				return nh(a, b.head.line)
			}, sg)
		}, goLineStartSmart: function (a) {
			a.extendSelectionsBy(function (b) {
				var c = nh(a, b.head.line), d = a.getLineHandle(c.line), e = Jf(d);
				if (!e || 0 == e[0].level) {
					var f = Math.max(0, d.text.search(/\S/)), g = b.head.line == c.line && b.head.ch <= f && b.head.ch;
					return mb(c.line, g ? 0 : f)
				}
				return c
			}, sg)
		}, goLineEnd:        function (a) {
			a.extendSelectionsBy(function (b) {
				return oh(a, b.head.line)
			}, sg)
		}, goLineRight:      function (a) {
			a.extendSelectionsBy(function (b) {
				var c = a.charCoords(b.head, "div").top + 5;
				return a.coordsChar({left: a.display.lineDiv.offsetWidth + 100, top: c}, "div")
			}, sg)
		}, goLineLeft:       function (a) {
			a.extendSelectionsBy(function (b) {
				var c = a.charCoords(b.head, "div").top + 5;
				return a.coordsChar({left: 0, top: c}, "div")
			}, sg)
		}, goLineUp:         function (a) {
			a.moveV(-1, "line")
		}, goLineDown:       function (a) {
			a.moveV(1, "line")
		}, goPageUp:         function (a) {
			a.moveV(-1, "page")
		}, goPageDown:       function (a) {
			a.moveV(1, "page")
		}, goCharLeft:       function (a) {
			a.moveH(-1, "char")
		}, goCharRight:      function (a) {
			a.moveH(1, "char")
		}, goColumnLeft:     function (a) {
			a.moveH(-1, "column")
		}, goColumnRight:    function (a) {
			a.moveH(1, "column")
		}, goWordLeft:       function (a) {
			a.moveH(-1, "word")
		}, goGroupRight:     function (a) {
			a.moveH(1, "group")
		}, goGroupLeft:      function (a) {
			a.moveH(-1, "group")
		}, goWordRight:      function (a) {
			a.moveH(1, "word")
		}, delCharBefore:    function (a) {
			a.deleteH(-1, "char")
		}, delCharAfter:     function (a) {
			a.deleteH(1, "char")
		}, delWordBefore:    function (a) {
			a.deleteH(-1, "word")
		}, delWordAfter:     function (a) {
			a.deleteH(1, "word")
		}, delGroupBefore:   function (a) {
			a.deleteH(-1, "group")
		}, delGroupAfter:    function (a) {
			a.deleteH(1, "group")
		}, indentAuto:       function (a) {
			a.indentSelection("smart")
		}, indentMore:       function (a) {
			a.indentSelection("add")
		}, indentLess:       function (a) {
			a.indentSelection("subtract")
		}, insertTab:        function (a) {
			a.replaceSelection("	")
		}, insertSoftTab:    function (a) {
			for (var b = [], c = a.listSelections(), d = a.options.tabSize, e = 0; e < c.length; e++) {
				var f = c[e].from(), g = ug(a.getLine(f.line), f.ch, d);
				b.push(new Array(d - g % d + 1).join(" "))
			}
			a.replaceSelections(b)
		}, defaultTab:       function (a) {
			a.somethingSelected() ? a.indentSelection("add") : a.execCommand("insertTab")
		}, transposeChars:   function (a) {
			yc(a, function () {
				for (var b = a.listSelections(), c = [], d = 0; d < b.length; d++) {
					var e = b[d].head, f = Cf(a.doc, e.line).text;
					if (f)if (e.ch == f.length && (e = new mb(e.line, e.ch - 1)), e.ch > 0)e = new mb(e.line, e.ch + 1), a.replaceRange(f.charAt(e.ch - 1) + f.charAt(e.ch - 2), mb(e.line, e.ch - 2), e, "+transpose"); else if (e.line > a.doc.first) {
						var g = Cf(a.doc, e.line - 1).text;
						g && a.replaceRange(f.charAt(0) + "\n" + g.charAt(g.length - 1), mb(e.line - 1, g.length - 1), mb(e.line, 1), "+transpose")
					}
					c.push(new sb(e, e))
				}
				a.setSelections(c)
			})
		}, newlineAndIndent: function (a) {
			yc(a, function () {
				for (var b = a.listSelections().length, c = 0; b > c; c++) {
					var d = a.listSelections()[c];
					a.replaceRange("\n", d.anchor, d.head, "+input"), a.indentLine(d.from().line + 1, null, !0), Qd(a)
				}
			})
		}, toggleOverwrite:  function (a) {
			a.toggleOverwrite()
		}
	}, ie = w.keyMap = {};
	ie.basic = {
		Left:              "goCharLeft",
		Right:             "goCharRight",
		Up:                "goLineUp",
		Down:              "goLineDown",
		End:               "goLineEnd",
		Home:              "goLineStartSmart",
		PageUp:            "goPageUp",
		PageDown:          "goPageDown",
		Delete:            "delCharAfter",
		Backspace:         "delCharBefore",
		"Shift-Backspace": "delCharBefore",
		Tab:               "defaultTab",
		"Shift-Tab":       "indentAuto",
		Enter:             "newlineAndIndent",
		Insert:            "toggleOverwrite",
		Esc:               "singleSelection"
	}, ie.pcDefault = {
		"Ctrl-A":         "selectAll",
		"Ctrl-D":         "deleteLine",
		"Ctrl-Z":         "undo",
		"Shift-Ctrl-Z":   "redo",
		"Ctrl-Y":         "redo",
		"Ctrl-Home":      "goDocStart",
		"Ctrl-Up":        "goDocStart",
		"Ctrl-End":       "goDocEnd",
		"Ctrl-Down":      "goDocEnd",
		"Ctrl-Left":      "goGroupLeft",
		"Ctrl-Right":     "goGroupRight",
		"Alt-Left":       "goLineStart",
		"Alt-Right":      "goLineEnd",
		"Ctrl-Backspace": "delGroupBefore",
		"Ctrl-Delete":    "delGroupAfter",
		"Ctrl-S":         "save",
		"Ctrl-F":         "find",
		"Ctrl-G":         "findNext",
		"Shift-Ctrl-G":   "findPrev",
		"Shift-Ctrl-F":   "replace",
		"Shift-Ctrl-R":   "replaceAll",
		"Ctrl-[":         "indentLess",
		"Ctrl-]":         "indentMore",
		"Ctrl-U":         "undoSelection",
		"Shift-Ctrl-U":   "redoSelection",
		"Alt-U":          "redoSelection",
		fallthrough:      "basic"
	}, ie.macDefault = {
		"Cmd-A":              "selectAll",
		"Cmd-D":              "deleteLine",
		"Cmd-Z":              "undo",
		"Shift-Cmd-Z":        "redo",
		"Cmd-Y":              "redo",
		"Cmd-Up":             "goDocStart",
		"Cmd-End":            "goDocEnd",
		"Cmd-Down":           "goDocEnd",
		"Alt-Left":           "goGroupLeft",
		"Alt-Right":          "goGroupRight",
		"Cmd-Left":           "goLineStart",
		"Cmd-Right":          "goLineEnd",
		"Alt-Backspace":      "delGroupBefore",
		"Ctrl-Alt-Backspace": "delGroupAfter",
		"Alt-Delete":         "delGroupAfter",
		"Cmd-S":              "save",
		"Cmd-F":              "find",
		"Cmd-G":              "findNext",
		"Shift-Cmd-G":        "findPrev",
		"Cmd-Alt-F":          "replace",
		"Shift-Cmd-Alt-F":    "replaceAll",
		"Cmd-[":              "indentLess",
		"Cmd-]":              "indentMore",
		"Cmd-Backspace":      "delLineLeft",
		"Cmd-U":              "undoSelection",
		"Shift-Cmd-U":        "redoSelection",
		fallthrough:          ["basic", "emacsy"]
	}, ie.emacsy = {
		"Ctrl-F":        "goCharRight",
		"Ctrl-B":        "goCharLeft",
		"Ctrl-P":        "goLineUp",
		"Ctrl-N":        "goLineDown",
		"Alt-F":         "goWordRight",
		"Alt-B":         "goWordLeft",
		"Ctrl-A":        "goLineStart",
		"Ctrl-E":        "goLineEnd",
		"Ctrl-V":        "goPageDown",
		"Shift-Ctrl-V":  "goPageUp",
		"Ctrl-D":        "delCharAfter",
		"Ctrl-H":        "delCharBefore",
		"Alt-D":         "delWordAfter",
		"Alt-Backspace": "delWordBefore",
		"Ctrl-K":        "killLine",
		"Ctrl-T":        "transposeChars"
	}, ie["default"] = p ? ie.macDefault : ie.pcDefault;
	var ke = w.lookupKey = function (a, b, c) {
		function d(b) {
			b = je(b);
			var e = b[a];
			if (e === !1)return "stop";
			if (null != e && c(e))return !0;
			if (b.nofallthrough)return "stop";
			var f = b.fallthrough;
			if (null == f)return !1;
			if ("[object Array]" != Object.prototype.toString.call(f))return d(f);
			for (var g = 0; g < f.length; ++g) {
				var h = d(f[g]);
				if (h)return h
			}
			return !1
		}

		for (var e = 0; e < b.length; ++e) {
			var f = d(b[e]);
			if (f)return "stop" != f
		}
	}, le = w.isModifierKey = function (a) {
		var b = hh[a.keyCode];
		return "Ctrl" == b || "Alt" == b || "Shift" == b || "Mod" == b
	}, me = w.keyName = function (a, b) {
		if (i && 34 == a.keyCode && a["char"])return !1;
		var c = hh[a.keyCode];
		return null == c || a.altGraphKey ? !1 : (a.altKey && (c = "Alt-" + c), (s ? a.metaKey : a.ctrlKey) && (c = "Ctrl-" + c), (s ? a.ctrlKey : a.metaKey) && (c = "Cmd-" + c), !b && a.shiftKey && (c = "Shift-" + c), c)
	};
	w.fromTextArea = function (a, b) {
		function d() {
			a.value = i.getValue()
		}

		if (b || (b = {}), b.value = a.value, !b.tabindex && a.tabindex && (b.tabindex = a.tabindex), !b.placeholder && a.placeholder && (b.placeholder = a.placeholder), null == b.autofocus) {
			var c = Qg();
			b.autofocus = c == a || null != a.getAttribute("autofocus") && c == document.body
		}
		if (a.form && (dg(a.form, "submit", d), !b.leaveSubmitMethodAlone)) {
			var e = a.form, f = e.submit;
			try {
				var g = e.submit = function () {
					d(), e.submit = f, e.submit(), e.submit = g
				}
			} catch (h) {
			}
		}
		a.style.display = "none";
		var i = w(function (b) {
			a.parentNode.insertBefore(b, a.nextSibling)
		}, b);
		return i.save = d, i.getTextArea = function () {
			return a
		}, i.toTextArea = function () {
			d(), a.parentNode.removeChild(i.getWrapperElement()), a.style.display = "", a.form && (eg(a.form, "submit", d), "function" == typeof a.form.submit && (a.form.submit = f))
		}, i
	};
	var ne = w.StringStream = function (a, b) {
		this.pos = this.start = 0, this.string = a, this.tabSize = b || 8, this.lastColumnPos = this.lastColumnValue = 0, this.lineStart = 0
	};
	ne.prototype = {
		eol:               function () {
			return this.pos >= this.string.length
		}, sol:            function () {
			return this.pos == this.lineStart
		}, peek:           function () {
			return this.string.charAt(this.pos) || void 0
		}, next:           function () {
			return this.pos < this.string.length ? this.string.charAt(this.pos++) : void 0
		}, eat:            function (a) {
			var b = this.string.charAt(this.pos);
			if ("string" == typeof a)var c = b == a; else var c = b && (a.test ? a.test(b) : a(b));
			return c ? (++this.pos, b) : void 0
		}, eatWhile:       function (a) {
			for (var b = this.pos; this.eat(a););
			return this.pos > b
		}, eatSpace:       function () {
			for (var a = this.pos; /[\s\u00a0]/.test(this.string.charAt(this.pos));)++this.pos;
			return this.pos > a
		}, skipToEnd:      function () {
			this.pos = this.string.length
		}, skipTo:         function (a) {
			var b = this.string.indexOf(a, this.pos);
			return b > -1 ? (this.pos = b, !0) : void 0
		}, backUp:         function (a) {
			this.pos -= a
		}, column:         function () {
			return this.lastColumnPos < this.start && (this.lastColumnValue = ug(this.string, this.start, this.tabSize, this.lastColumnPos, this.lastColumnValue), this.lastColumnPos = this.start), this.lastColumnValue - (this.lineStart ? ug(this.string, this.lineStart, this.tabSize) : 0)
		}, indentation:    function () {
			return ug(this.string, null, this.tabSize) - (this.lineStart ? ug(this.string, this.lineStart, this.tabSize) : 0)
		}, match:          function (a, b, c) {
			if ("string" != typeof a) {
				var f = this.string.slice(this.pos).match(a);
				return f && f.index > 0 ? null : (f && b !== !1 && (this.pos += f[0].length), f)
			}
			var d = function (a) {
				return c ? a.toLowerCase() : a
			}, e = this.string.substr(this.pos, a.length);
			return d(e) == d(a) ? (b !== !1 && (this.pos += a.length), !0) : void 0
		}, current:        function () {
			return this.string.slice(this.start, this.pos)
		}, hideFirstChars: function (a, b) {
			this.lineStart += a;
			try {
				return b()
			} finally {
				this.lineStart -= a
			}
		}
	};
	var oe = w.TextMarker = function (a, b) {
		this.lines = [], this.type = b, this.doc = a
	};
	ng(oe), oe.prototype.clear = function () {
		if (!this.explicitlyCleared) {
			var a = this.doc.cm, b = a && !a.curOp;
			if (b && wc(a), mg(this, "clear")) {
				var c = this.find();
				c && ig(this, "clear", c.from, c.to)
			}
			for (var d = null, e = null, f = 0; f < this.lines.length; ++f) {
				var g = this.lines[f], h = xe(g.markedSpans, this);
				a && !this.collapsed ? Fc(a, Gf(g), "text") : a && (null != h.to && (e = Gf(g)), null != h.from && (d = Gf(g))), g.markedSpans = ye(g.markedSpans, h), null == h.from && this.collapsed && !Te(this.doc, g) && a && Ff(g, tc(a.display))
			}
			if (a && this.collapsed && !a.options.lineWrapping)for (var f = 0; f < this.lines.length; ++f) {
				var i = Pe(this.lines[f]), j = I(i);
				j > a.display.maxLineLength && (a.display.maxLine = i, a.display.maxLineLength = j, a.display.maxLineChanged = !0)
			}
			null != d && a && this.collapsed && Ec(a, d, e + 1), this.lines.length = 0, this.explicitlyCleared = !0, this.atomic && this.doc.cantEdit && (this.doc.cantEdit = !1, a && Kb(a.doc)), a && ig(a, "markerCleared", a, this), b && xc(a), this.parent && this.parent.clear()
		}
	}, oe.prototype.find = function (a, b) {
		null == a && "bookmark" == this.type && (a = 1);
		for (var c, d, e = 0; e < this.lines.length; ++e) {
			var f = this.lines[e], g = xe(f.markedSpans, this);
			if (null != g.from && (c = mb(b ? f : Gf(f), g.from), -1 == a))return c;
			if (null != g.to && (d = mb(b ? f : Gf(f), g.to), 1 == a))return d
		}
		return c && {from: c, to: d}
	}, oe.prototype.changed = function () {
		var a = this.find(-1, !0), b = this, c = this.doc.cm;
		a && c && yc(c, function () {
			var d = a.line, e = Gf(a.line), f = ac(c, e);
			if (f && (fc(f), c.curOp.selectionChanged = c.curOp.forceUpdate = !0), c.curOp.updateMaxLine = !0, !Te(b.doc, d) && null != b.height) {
				var g = b.height;
				b.height = null;
				var h = Xe(b) - g;
				h && Ff(d, d.height + h)
			}
		})
	}, oe.prototype.attachLine = function (a) {
		if (!this.lines.length && this.doc.cm) {
			var b = this.doc.cm.curOp;
			b.maybeHiddenMarkers && -1 != Ag(b.maybeHiddenMarkers, this) || (b.maybeUnhiddenMarkers || (b.maybeUnhiddenMarkers = [])).push(this)
		}
		this.lines.push(a)
	}, oe.prototype.detachLine = function (a) {
		if (this.lines.splice(Ag(this.lines, a), 1), !this.lines.length && this.doc.cm) {
			var b = this.doc.cm.curOp;
			(b.maybeHiddenMarkers || (b.maybeHiddenMarkers = [])).push(this)
		}
	};
	var pe = 0, re = w.SharedTextMarker = function (a, b) {
		this.markers = a, this.primary = b;
		for (var c = 0; c < a.length; ++c)a[c].parent = this
	};
	ng(re), re.prototype.clear = function () {
		if (!this.explicitlyCleared) {
			this.explicitlyCleared = !0;
			for (var a = 0; a < this.markers.length; ++a)this.markers[a].clear();
			ig(this, "clear")
		}
	}, re.prototype.find = function (a, b) {
		return this.primary.find(a, b)
	};
	var Ve = w.LineWidget = function (a, b, c) {
		if (c)for (var d in c)c.hasOwnProperty(d) && (this[d] = c[d]);
		this.cm = a, this.node = b
	};
	ng(Ve), Ve.prototype.clear = function () {
		var a = this.cm, b = this.line.widgets, c = this.line, d = Gf(c);
		if (null != d && b) {
			for (var e = 0; e < b.length; ++e)b[e] == this && b.splice(e--, 1);
			b.length || (c.widgets = null);
			var f = Xe(this);
			yc(a, function () {
				We(a, c, -f), Fc(a, d, "widget"), Ff(c, Math.max(0, c.height - f))
			})
		}
	}, Ve.prototype.changed = function () {
		var a = this.height, b = this.cm, c = this.line;
		this.height = null;
		var d = Xe(this) - a;
		d && yc(b, function () {
			b.curOp.forceUpdate = !0, We(b, c, d), Ff(c, c.height + d)
		})
	};
	var Ze = w.Line = function (a, b, c) {
		this.text = a, He(this, b), this.height = c ? c(this) : 1
	};
	ng(Ze), Ze.prototype.lineNo = function () {
		return Gf(this)
	};
	var hf = {}, jf = {};
	uf.prototype = {
		chunkSize:      function () {
			return this.lines.length
		}, removeInner: function (a, b) {
			for (var c = a, d = a + b; d > c; ++c) {
				var e = this.lines[c];
				this.height -= e.height, _e(e), ig(e, "delete")
			}
			this.lines.splice(a, b)
		}, collapse:    function (a) {
			a.push.apply(a, this.lines)
		}, insertInner: function (a, b, c) {
			this.height += c, this.lines = this.lines.slice(0, a).concat(b).concat(this.lines.slice(a));
			for (var d = 0; d < b.length; ++d)b[d].parent = this
		}, iterN:       function (a, b, c) {
			for (var d = a + b; d > a; ++a)if (c(this.lines[a]))return !0
		}
	}, vf.prototype = {
		chunkSize:      function () {
			return this.size
		}, removeInner: function (a, b) {
			this.size -= b;
			for (var c = 0; c < this.children.length; ++c) {
				var d = this.children[c], e = d.chunkSize();
				if (e > a) {
					var f = Math.min(b, e - a), g = d.height;
					if (d.removeInner(a, f), this.height -= g - d.height, e == f && (this.children.splice(c--, 1), d.parent = null), 0 == (b -= f))break;
					a = 0
				} else a -= e
			}
			if (this.size - b < 25 && (this.children.length > 1 || !(this.children[0]instanceof uf))) {
				var h = [];
				this.collapse(h), this.children = [new uf(h)], this.children[0].parent = this
			}
		}, collapse:    function (a) {
			for (var b = 0; b < this.children.length; ++b)this.children[b].collapse(a)
		}, insertInner: function (a, b, c) {
			this.size += b.length, this.height += c;
			for (var d = 0; d < this.children.length; ++d) {
				var e = this.children[d], f = e.chunkSize();
				if (f >= a) {
					if (e.insertInner(a, b, c), e.lines && e.lines.length > 50) {
						for (; e.lines.length > 50;) {
							var g = e.lines.splice(e.lines.length - 25, 25), h = new uf(g);
							e.height -= h.height, this.children.splice(d + 1, 0, h), h.parent = this
						}
						this.maybeSpill()
					}
					break
				}
				a -= f
			}
		}, maybeSpill:  function () {
			if (!(this.children.length <= 10)) {
				var a = this;
				do {
					var b = a.children.splice(a.children.length - 5, 5), c = new vf(b);
					if (a.parent) {
						a.size -= c.size, a.height -= c.height;
						var e = Ag(a.parent.children, a);
						a.parent.children.splice(e + 1, 0, c)
					} else {
						var d = new vf(a.children);
						d.parent = a, a.children = [d, c], a = d
					}
					c.parent = a.parent
				} while (a.children.length > 10);
				a.parent.maybeSpill()
			}
		}, iterN:       function (a, b, c) {
			for (var d = 0; d < this.children.length; ++d) {
				var e = this.children[d], f = e.chunkSize();
				if (f > a) {
					var g = Math.min(b, f - a);
					if (e.iterN(a, g, c))return !0;
					if (0 == (b -= g))break;
					a = 0
				} else a -= f
			}
		}
	};
	var wf = 0, xf = w.Doc = function (a, b, c) {
		if (!(this instanceof xf))return new xf(a, b, c);
		null == c && (c = 0), vf.call(this, [new uf([new Ze("", null)])]), this.first = c, this.scrollTop = this.scrollLeft = 0, this.cantEdit = !1, this.cleanGeneration = 1, this.frontier = c;
		var d = mb(c, 0);
		this.sel = ub(d), this.history = new Kf(null), this.id = ++wf, this.modeOption = b, "string" == typeof a && (a = eh(a)), tf(this, {
			from: d,
			to: d,
			text: a
		}), Hb(this, ub(d), qg)
	};
	xf.prototype = Cg(vf.prototype, {
		constructor:                 xf, iter: function (a, b, c) {
			c ? this.iterN(a - this.first, b - a, c) : this.iterN(this.first, this.first + this.size, a)
		}, insert:                   function (a, b) {
			for (var c = 0, d = 0; d < b.length; ++d)c += b[d].height;
			this.insertInner(a - this.first, b, c)
		}, remove:                   function (a, b) {
			this.removeInner(a - this.first, b)
		}, getValue:                 function (a) {
			var b = Ef(this, this.first, this.first + this.size);
			return a === !1 ? b : b.join(a || "\n")
		}, setValue:                 Bc(function (a) {
			var b = mb(this.first, 0), c = this.first + this.size - 1;
			Ed(this, {from: b, to: mb(c, Cf(this, c).text.length), text: eh(a), origin: "setValue"}, !0), Hb(this, ub(b))
		}), replaceRange:            function (a, b, c, d) {
			b = wb(this, b), c = c ? wb(this, c) : b, Kd(this, a, b, c, d)
		}, getRange:                 function (a, b, c) {
			var d = Df(this, wb(this, a), wb(this, b));
			return c === !1 ? d : d.join(c || "\n")
		}, getLine:                  function (a) {
			var b = this.getLineHandle(a);
			return b && b.text
		}, getLineHandle:            function (a) {
			return yb(this, a) ? Cf(this, a) : void 0
		}, getLineNumber:            function (a) {
			return Gf(a)
		}, getLineHandleVisualStart: function (a) {
			return "number" == typeof a && (a = Cf(this, a)), Pe(a)
		}, lineCount:                function () {
			return this.size
		}, firstLine:                function () {
			return this.first
		}, lastLine:                 function () {
			return this.first + this.size - 1
		}, clipPos:                  function (a) {
			return wb(this, a)
		}, getCursor:                function (a) {
			var c, b = this.sel.primary();
			return c = null == a || "head" == a ? b.head : "anchor" == a ? b.anchor : "end" == a || "to" == a || a === !1 ? b.to() : b.from()
		}, listSelections:           function () {
			return this.sel.ranges
		}, somethingSelected:        function () {
			return this.sel.somethingSelected()
		}, setCursor:                Bc(function (a, b, c) {
			Eb(this, wb(this, "number" == typeof a ? mb(a, b || 0) : a), null, c)
		}), setSelection:            Bc(function (a, b, c) {
			Eb(this, wb(this, a), wb(this, b || a), c)
		}), extendSelection:         Bc(function (a, b, c) {
			Bb(this, wb(this, a), b && wb(this, b), c)
		}), extendSelections:        Bc(function (a, b) {
			Cb(this, zb(this, a, b))
		}), extendSelectionsBy:      Bc(function (a, b) {
			Cb(this, Bg(this.sel.ranges, a), b)
		}), setSelections:           Bc(function (a, b, c) {
			if (a.length) {
				for (var d = 0, e = []; d < a.length; d++)e[d] = new sb(wb(this, a[d].anchor), wb(this, a[d].head));
				null == b && (b = Math.min(a.length - 1, this.sel.primIndex)), Hb(this, tb(e, b), c)
			}
		}), addSelection:            Bc(function (a, b, c) {
			var d = this.sel.ranges.slice(0);
			d.push(new sb(wb(this, a), wb(this, b || a))), Hb(this, tb(d, d.length - 1), c)
		}), getSelection:            function (a) {
			for (var c, b = this.sel.ranges, d = 0; d < b.length; d++) {
				var e = Df(this, b[d].from(), b[d].to());
				c = c ? c.concat(e) : e
			}
			return a === !1 ? c : c.join(a || "\n")
		}, getSelections:            function (a) {
			for (var b = [], c = this.sel.ranges, d = 0; d < c.length; d++) {
				var e = Df(this, c[d].from(), c[d].to());
				a !== !1 && (e = e.join(a || "\n")), b[d] = e
			}
			return b
		}, replaceSelection:         function (a, b, c) {
			for (var d = [], e = 0; e < this.sel.ranges.length; e++)d[e] = a;
			this.replaceSelections(d, b, c || "+input")
		}, replaceSelections:        Bc(function (a, b, c) {
			for (var d = [], e = this.sel, f = 0; f < e.ranges.length; f++) {
				var g = e.ranges[f];
				d[f] = {from: g.from(), to: g.to(), text: eh(a[f]), origin: c}
			}
			for (var h = b && "end" != b && Cd(this, d, b), f = d.length - 1; f >= 0; f--)Ed(this, d[f]);
			h ? Gb(this, h) : this.cm && Qd(this.cm)
		}), undo:                    Bc(function () {
			Gd(this, "undo")
		}), redo:                    Bc(function () {
			Gd(this, "redo")
		}), undoSelection:           Bc(function () {
			Gd(this, "undo", !0)
		}), redoSelection:           Bc(function () {
			Gd(this, "redo", !0)
		}), setExtending:            function (a) {
			this.extend = a
		}, getExtending:             function () {
			return this.extend
		}, historySize:              function () {
			for (var a = this.history, b = 0, c = 0, d = 0; d < a.done.length; d++)a.done[d].ranges || ++b;
			for (var d = 0; d < a.undone.length; d++)a.undone[d].ranges || ++c;
			return {undo: b, redo: c}
		}, clearHistory:             function () {
			this.history = new Kf(this.history.maxGeneration)
		}, markClean:                function () {
			this.cleanGeneration = this.changeGeneration(!0)
		}, changeGeneration:         function (a) {
			return a && (this.history.lastOp = this.history.lastOrigin = null), this.history.generation
		}, isClean:                  function (a) {
			return this.history.generation == (a || this.cleanGeneration)
		}, getHistory:               function () {
			return {done: Vf(this.history.done), undone: Vf(this.history.undone)}
		}, setHistory:               function (a) {
			var b = this.history = new Kf(this.history.maxGeneration);
			b.done = Vf(a.done.slice(0), null, !0), b.undone = Vf(a.undone.slice(0), null, !0)
		}, addLineClass:             Bc(function (a, b, c) {
			return Td(this, a, "class", function (a) {
				var d = "text" == b ? "textClass" : "background" == b ? "bgClass" : "wrapClass";
				if (a[d]) {
					if (new RegExp("(?:^|\\s)" + c + "(?:$|\\s)").test(a[d]))return !1;
					a[d] += " " + c
				} else a[d] = c;
				return !0
			})
		}), removeLineClass:         Bc(function (a, b, c) {
			return Td(this, a, "class", function (a) {
				var d = "text" == b ? "textClass" : "background" == b ? "bgClass" : "wrapClass", e = a[d];
				if (!e)return !1;
				if (null == c)a[d] = null; else {
					var f = e.match(new RegExp("(?:^|\\s+)" + c + "(?:$|\\s+)"));
					if (!f)return !1;
					var g = f.index + f[0].length;
					a[d] = e.slice(0, f.index) + (f.index && g != e.length ? " " : "") + e.slice(g) || null
				}
				return !0
			})
		}), markText:                function (a, b, c) {
			return qe(this, wb(this, a), wb(this, b), c, "range")
		}, setBookmark:              function (a, b) {
			var c = {
				replacedWith:   b && (null == b.nodeType ? b.widget : b),
				insertLeft:     b && b.insertLeft,
				clearWhenEmpty: !1,
				shared:         b && b.shared
			};
			return a = wb(this, a), qe(this, a, a, c, "bookmark")
		}, findMarksAt:              function (a) {
			a = wb(this, a);
			var b = [], c = Cf(this, a.line).markedSpans;
			if (c)for (var d = 0; d < c.length; ++d) {
				var e = c[d];
				(null == e.from || e.from <= a.ch) && (null == e.to || e.to >= a.ch) && b.push(e.marker.parent || e.marker)
			}
			return b
		}, findMarks:                function (a, b, c) {
			a = wb(this, a), b = wb(this, b);
			var d = [], e = a.line;
			return this.iter(a.line, b.line + 1, function (f) {
				var g = f.markedSpans;
				if (g)for (var h = 0; h < g.length; h++) {
					var i = g[h];
					e == a.line && a.ch > i.to || null == i.from && e != a.line || e == b.line && i.from > b.ch || c && !c(i.marker) || d.push(i.marker.parent || i.marker)
				}
				++e
			}), d
		}, getAllMarks:              function () {
			var a = [];
			return this.iter(function (b) {
				var c = b.markedSpans;
				if (c)for (var d = 0; d < c.length; ++d)null != c[d].from && a.push(c[d].marker)
			}), a
		}, posFromIndex:             function (a) {
			var b, c = this.first;
			return this.iter(function (d) {
				var e = d.text.length + 1;
				return e > a ? (b = a, !0) : (a -= e, ++c, void 0)
			}), wb(this, mb(c, b))
		}, indexFromPos:             function (a) {
			a = wb(this, a);
			var b = a.ch;
			return a.line < this.first || a.ch < 0 ? 0 : (this.iter(this.first, a.line, function (a) {
				b += a.text.length + 1
			}), b)
		}, copy:                     function (a) {
			var b = new xf(Ef(this, this.first, this.first + this.size), this.modeOption, this.first);
			return b.scrollTop = this.scrollTop, b.scrollLeft = this.scrollLeft, b.sel = this.sel, b.extend = !1, a && (b.history.undoDepth = this.history.undoDepth, b.setHistory(this.getHistory())), b
		}, linkedDoc:                function (a) {
			a || (a = {});
			var b = this.first, c = this.first + this.size;
			null != a.from && a.from > b && (b = a.from), null != a.to && a.to < c && (c = a.to);
			var d = new xf(Ef(this, b, c), a.mode || this.modeOption, b);
			return a.sharedHist && (d.history = this.history), (this.linked || (this.linked = [])).push({
				doc:        d,
				sharedHist: a.sharedHist
			}), d.linked = [{doc: this, isParent: !0, sharedHist: a.sharedHist}], ue(d, te(this)), d
		}, unlinkDoc:                function (a) {
			if (a instanceof w && (a = a.doc), this.linked)for (var b = 0; b < this.linked.length; ++b) {
				var c = this.linked[b];
				if (c.doc == a) {
					this.linked.splice(b, 1), a.unlinkDoc(this), ve(te(this));
					break
				}
			}
			if (a.history == this.history) {
				var d = [a.id];
				Af(a, function (a) {
					d.push(a.id)
				}, !0), a.history = new Kf(null), a.history.done = Vf(this.history.done, d), a.history.undone = Vf(this.history.undone, d)
			}
		}, iterLinkedDocs:           function (a) {
			Af(this, a)
		}, getMode:                  function () {
			return this.mode
		}, getEditor:                function () {
			return this.cm
		}
	}), xf.prototype.eachLine = xf.prototype.iter;
	var yf = "iter insert remove copy getEditor".split(" ");
	for (var zf in xf.prototype)xf.prototype.hasOwnProperty(zf) && Ag(yf, zf) < 0 && (w.prototype[zf] = function (a) {
		return function () {
			return a.apply(this.doc, arguments)
		}
	}(xf.prototype[zf]));
	ng(xf);
	var gg, Zf = w.e_preventDefault = function (a) {
		a.preventDefault ? a.preventDefault() : a.returnValue = !1
	}, $f = w.e_stopPropagation = function (a) {
		a.stopPropagation ? a.stopPropagation() : a.cancelBubble = !0
	}, ag = w.e_stop = function (a) {
		Zf(a), $f(a)
	}, dg = w.on = function (a, b, c) {
		if (a.addEventListener)a.addEventListener(b, c, !1); else if (a.attachEvent)a.attachEvent("on" + b, c); else {
			var d = a._handlers || (a._handlers = {}), e = d[b] || (d[b] = []);
			e.push(c)
		}
	}, eg = w.off = function (a, b, c) {
		if (a.removeEventListener)a.removeEventListener(b, c, !1); else if (a.detachEvent)a.detachEvent("on" + b, c); else {
			var d = a._handlers && a._handlers[b];
			if (!d)return;
			for (var e = 0; e < d.length; ++e)if (d[e] == c) {
				d.splice(e, 1);
				break
			}
		}
	}, fg = w.signal = function (a, b) {
		var c = a._handlers && a._handlers[b];
		if (c)for (var d = Array.prototype.slice.call(arguments, 2), e = 0; e < c.length; ++e)c[e].apply(null, d)
	}, hg = 0, og = 30, pg = w.Pass = {
		toString: function () {
			return "CodeMirror.Pass"
		}
	}, qg = {scroll: !1}, rg = {origin: "*mouse"}, sg = {origin: "+move"};
	tg.prototype.set = function (a, b) {
		clearTimeout(this.id), this.id = setTimeout(b, a)
	};
	var ug = w.countColumn = function (a, b, c, d, e) {
		null == b && (b = a.search(/[^\s\u00a0]/), -1 == b && (b = a.length));
		for (var f = d || 0, g = e || 0; ;) {
			var h = a.indexOf("	", f);
			if (0 > h || h >= b)return g + (b - f);
			g += h - f, g += c - g % c, f = h + 1
		}
	}, wg = [""], zg = function (a) {
		a.select()
	};
	n ? zg = function (a) {
		a.selectionStart = 0, a.selectionEnd = a.value.length
	} : d && (zg = function (a) {
		try {
			a.select()
		} catch (b) {
		}
	}), [].indexOf && (Ag = function (a, b) {
		return a.indexOf(b)
	}), [].map && (Bg = function (a, b) {
		return a.map(b)
	});
	var Mg, Fg = /[\u00df\u3040-\u309f\u30a0-\u30ff\u3400-\u4db5\u4e00-\u9fcc\uac00-\ud7af]/, Gg = w.isWordChar = function (a) {
		return /\w/.test(a) || a > "\x80" && (a.toUpperCase() != a.toLowerCase() || Fg.test(a))
	}, Jg = /[\u0300-\u036f\u0483-\u0489\u0591-\u05bd\u05bf\u05c1\u05c2\u05c4\u05c5\u05c7\u0610-\u061a\u064b-\u065e\u0670\u06d6-\u06dc\u06de-\u06e4\u06e7\u06e8\u06ea-\u06ed\u0711\u0730-\u074a\u07a6-\u07b0\u07eb-\u07f3\u0816-\u0819\u081b-\u0823\u0825-\u0827\u0829-\u082d\u0900-\u0902\u093c\u0941-\u0948\u094d\u0951-\u0955\u0962\u0963\u0981\u09bc\u09be\u09c1-\u09c4\u09cd\u09d7\u09e2\u09e3\u0a01\u0a02\u0a3c\u0a41\u0a42\u0a47\u0a48\u0a4b-\u0a4d\u0a51\u0a70\u0a71\u0a75\u0a81\u0a82\u0abc\u0ac1-\u0ac5\u0ac7\u0ac8\u0acd\u0ae2\u0ae3\u0b01\u0b3c\u0b3e\u0b3f\u0b41-\u0b44\u0b4d\u0b56\u0b57\u0b62\u0b63\u0b82\u0bbe\u0bc0\u0bcd\u0bd7\u0c3e-\u0c40\u0c46-\u0c48\u0c4a-\u0c4d\u0c55\u0c56\u0c62\u0c63\u0cbc\u0cbf\u0cc2\u0cc6\u0ccc\u0ccd\u0cd5\u0cd6\u0ce2\u0ce3\u0d3e\u0d41-\u0d44\u0d4d\u0d57\u0d62\u0d63\u0dca\u0dcf\u0dd2-\u0dd4\u0dd6\u0ddf\u0e31\u0e34-\u0e3a\u0e47-\u0e4e\u0eb1\u0eb4-\u0eb9\u0ebb\u0ebc\u0ec8-\u0ecd\u0f18\u0f19\u0f35\u0f37\u0f39\u0f71-\u0f7e\u0f80-\u0f84\u0f86\u0f87\u0f90-\u0f97\u0f99-\u0fbc\u0fc6\u102d-\u1030\u1032-\u1037\u1039\u103a\u103d\u103e\u1058\u1059\u105e-\u1060\u1071-\u1074\u1082\u1085\u1086\u108d\u109d\u135f\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17b7-\u17bd\u17c6\u17c9-\u17d3\u17dd\u180b-\u180d\u18a9\u1920-\u1922\u1927\u1928\u1932\u1939-\u193b\u1a17\u1a18\u1a56\u1a58-\u1a5e\u1a60\u1a62\u1a65-\u1a6c\u1a73-\u1a7c\u1a7f\u1b00-\u1b03\u1b34\u1b36-\u1b3a\u1b3c\u1b42\u1b6b-\u1b73\u1b80\u1b81\u1ba2-\u1ba5\u1ba8\u1ba9\u1c2c-\u1c33\u1c36\u1c37\u1cd0-\u1cd2\u1cd4-\u1ce0\u1ce2-\u1ce8\u1ced\u1dc0-\u1de6\u1dfd-\u1dff\u200c\u200d\u20d0-\u20f0\u2cef-\u2cf1\u2de0-\u2dff\u302a-\u302f\u3099\u309a\ua66f-\ua672\ua67c\ua67d\ua6f0\ua6f1\ua802\ua806\ua80b\ua825\ua826\ua8c4\ua8e0-\ua8f1\ua926-\ua92d\ua947-\ua951\ua980-\ua982\ua9b3\ua9b6-\ua9b9\ua9bc\uaa29-\uaa2e\uaa31\uaa32\uaa35\uaa36\uaa43\uaa4c\uaab0\uaab2-\uaab4\uaab7\uaab8\uaabe\uaabf\uaac1\uabe5\uabe8\uabed\udc00-\udfff\ufb1e\ufe00-\ufe0f\ufe20-\ufe26\uff9e\uff9f]/;
	Mg = document.createRange ? function (a, b, c) {
		var d = document.createRange();
		return d.setEnd(a, c), d.setStart(a, b), d
	} : function (a, b, c) {
		var d = document.body.createTextRange();
		return d.moveToElementText(a.parentNode), d.collapse(!0), d.moveEnd("character", c), d.moveStart("character", b), d
	}, d && 11 > e && (Qg = function () {
		try {
			return document.activeElement
		} catch (a) {
			return document.body
		}
	});
	var $g, ah, ch, Wg = !1, Zg = function () {
		if (d && 9 > e)return !1;
		var a = Lg("div");
		return "draggable"in a || "dragDrop"in a
	}(), eh = w.splitLines = 3 != "\n\nb".split(/\n/).length ? function (a) {
		for (var b = 0, c = [], d = a.length; d >= b;) {
			var e = a.indexOf("\n", b);
			-1 == e && (e = a.length);
			var f = a.slice(b, "\r" == a.charAt(e - 1) ? e - 1 : e), g = f.indexOf("\r");
			-1 != g ? (c.push(f.slice(0, g)), b += g + 1) : (c.push(f), b = e + 1)
		}
		return c
	} : function (a) {
		return a.split(/\r\n?|\n/)
	}, fh = window.getSelection ? function (a) {
		try {
			return a.selectionStart != a.selectionEnd
		} catch (b) {
			return !1
		}
	} : function (a) {
		try {
			var b = a.ownerDocument.selection.createRange()
		} catch (c) {
		}
		return b && b.parentElement() == a ? 0 != b.compareEndPoints("StartToEnd", b) : !1
	}, gh = function () {
		var a = Lg("div");
		return "oncopy"in a ? !0 : (a.setAttribute("oncopy", "return;"), "function" == typeof a.oncopy)
	}(), hh = {
		3:     "Enter",
		8:     "Backspace",
		9:     "Tab",
		13:    "Enter",
		16:    "Shift",
		17:    "Ctrl",
		18:    "Alt",
		19:    "Pause",
		20:    "CapsLock",
		27:    "Esc",
		32:    "Space",
		33:    "PageUp",
		34:    "PageDown",
		35:    "End",
		36:    "Home",
		37:    "Left",
		38:    "Up",
		39:    "Right",
		40:    "Down",
		44:    "PrintScrn",
		45:    "Insert",
		46:    "Delete",
		59:    ";",
		61:    "=",
		91:    "Mod",
		92:    "Mod",
		93:    "Mod",
		107:   "=",
		109:   "-",
		127:   "Delete",
		173:   "-",
		186:   ";",
		187:   "=",
		188:   ",",
		189:   "-",
		190:   ".",
		191:   "/",
		192:   "`",
		219:   "[",
		220:   "\\",
		221:   "]",
		222:   "'",
		63232: "Up",
		63233: "Down",
		63234: "Left",
		63235: "Right",
		63272: "Delete",
		63273: "Home",
		63275: "End",
		63276: "PageUp",
		63277: "PageDown",
		63302: "Insert"
	};
	w.keyNames = hh, function () {
		for (var a = 0; 10 > a; a++)hh[a + 48] = hh[a + 96] = String(a);
		for (var a = 65; 90 >= a; a++)hh[a] = String.fromCharCode(a);
		for (var a = 1; 12 >= a; a++)hh[a + 111] = hh[a + 63235] = "F" + a
	}();
	var qh, vh = function () {
		function c(c) {
			return 247 >= c ? a.charAt(c) : c >= 1424 && 1524 >= c ? "R" : c >= 1536 && 1773 >= c ? b.charAt(c - 1536) : c >= 1774 && 2220 >= c ? "r" : c >= 8192 && 8203 >= c ? "w" : 8204 == c ? "b" : "L"
		}

		function j(a, b, c) {
			this.level = a, this.from = b, this.to = c
		}

		var a = "bbbbbbbbbtstwsbbbbbbbbbbbbbbssstwNN%%%NNNNNN,N,N1111111111NNNNNNNLLLLLLLLLLLLLLLLLLLLLLLLLLNNNNNNLLLLLLLLLLLLLLLLLLLLLLLLLLNNNNbbbbbbsbbbbbbbbbbbbbbbbbbbbbbbbbb,N%%%%NNNNLNNNNN%%11NLNNN1LNNNNNLLLLLLLLLLLLLLLLLLLLLLLNLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLN", b = "rrrrrrrrrrrr,rNNmmmmmmrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrmmmmmmmmmmmmmmrrrrrrrnnnnnnnnnn%nnrrrmrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrmmmmmmmmmmmmmmmmmmmNmmmm", d = /[\u0590-\u05f4\u0600-\u06ff\u0700-\u08ac]/, e = /[stwN]/, f = /[LRr]/, g = /[Lb1n]/, h = /[1n]/, i = "L";
		return function (a) {
			if (!d.test(a))return !1;
			for (var m, b = a.length, k = [], l = 0; b > l; ++l)k.push(m = c(a.charCodeAt(l)));
			for (var l = 0, n = i; b > l; ++l) {
				var m = k[l];
				"m" == m ? k[l] = n : n = m
			}
			for (var l = 0, o = i; b > l; ++l) {
				var m = k[l];
				"1" == m && "r" == o ? k[l] = "n" : f.test(m) && (o = m, "r" == m && (k[l] = "R"))
			}
			for (var l = 1, n = k[0]; b - 1 > l; ++l) {
				var m = k[l];
				"+" == m && "1" == n && "1" == k[l + 1] ? k[l] = "1" : "," != m || n != k[l + 1] || "1" != n && "n" != n || (k[l] = n), n = m
			}
			for (var l = 0; b > l; ++l) {
				var m = k[l];
				if ("," == m)k[l] = "N"; else if ("%" == m) {
					for (var p = l + 1; b > p && "%" == k[p]; ++p);
					for (var q = l && "!" == k[l - 1] || b > p && "1" == k[p] ? "1" : "N", r = l; p > r; ++r)k[r] = q;
					l = p - 1
				}
			}
			for (var l = 0, o = i; b > l; ++l) {
				var m = k[l];
				"L" == o && "1" == m ? k[l] = "L" : f.test(m) && (o = m)
			}
			for (var l = 0; b > l; ++l)if (e.test(k[l])) {
				for (var p = l + 1; b > p && e.test(k[p]); ++p);
				for (var s = "L" == (l ? k[l - 1] : i), t = "L" == (b > p ? k[p] : i), q = s || t ? "L" : "R", r = l; p > r; ++r)k[r] = q;
				l = p - 1
			}
			for (var v, u = [], l = 0; b > l;)if (g.test(k[l])) {
				var w = l;
				for (++l; b > l && g.test(k[l]); ++l);
				u.push(new j(0, w, l))
			} else {
				var x = l, y = u.length;
				for (++l; b > l && "L" != k[l]; ++l);
				for (var r = x; l > r;)if (h.test(k[r])) {
					r > x && u.splice(y, 0, new j(1, x, r));
					var z = r;
					for (++r; l > r && h.test(k[r]); ++r);
					u.splice(y, 0, new j(2, z, r)), x = r
				} else++r;
				l > x && u.splice(y, 0, new j(1, x, l))
			}
			return 1 == u[0].level && (v = a.match(/^\s+/)) && (u[0].from = v[0].length, u.unshift(new j(0, 0, v[0].length))), 1 == yg(u).level && (v = a.match(/\s+$/)) && (yg(u).to -= v[0].length, u.push(new j(0, b - v[0].length, b))), u[0].level != yg(u).level && u.push(new j(u[0].level, b, b)), u
		}
	}();
	return w.version = "4.2.1", w
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a) {
		for (var b = {}, c = a.split(" "), d = 0; d < c.length; ++d)b[c[d]] = !0;
		return b
	}

	function d(a, b) {
		if (!b.startOfLine)return !1;
		for (; ;) {
			if (!a.skipTo("\\")) {
				a.skipToEnd(), b.tokenize = null;
				break
			}
			if (a.next(), a.eol()) {
				b.tokenize = d;
				break
			}
		}
		return "meta"
	}

	function e(a, b) {
		if (a.backUp(1), a.match(/(R|u8R|uR|UR|LR)/)) {
			var c = a.match(/"([^\s\\()]{0,16})\(/);
			return c ? (b.cpp11RawStringDelim = c[1], b.tokenize = g, g(a, b)) : !1
		}
		return a.match(/(u8|u|U|L)/) ? a.match(/["']/, !1) ? "string" : !1 : (a.next(), !1)
	}

	function f(a, b) {
		for (var c; null != (c = a.next());)if ('"' == c && !a.eat('"')) {
			b.tokenize = null;
			break
		}
		return "string"
	}

	function g(a, b) {
		var c = b.cpp11RawStringDelim.replace(/[^\w\s]/g, "\\$&"), d = a.match(new RegExp(".*?\\)" + c + '"'));
		return d ? b.tokenize = null : a.skipToEnd(), "string"
	}

	function h(b, c) {
		function e(a) {
			if (a)for (var b in a)a.hasOwnProperty(b) && d.push(b)
		}

		"string" == typeof b && (b = [b]);
		var d = [];
		e(c.keywords), e(c.builtin), e(c.atoms), d.length && (c.helperType = b[0], a.registerHelper("hintWords", b[0], d));
		for (var f = 0; f < b.length; ++f)a.defineMIME(b[f], c)
	}

	a.defineMode("clike", function (b, c) {
		function o(a, b) {
			var c = a.next();
			if (k[c]) {
				var d = k[c](a, b);
				if (d !== !1)return d
			}
			if ('"' == c || "'" == c)return b.tokenize = p(c), b.tokenize(a, b);
			if (/[\[\]{}\(\),;\:\.]/.test(c))return n = c, null;
			if (/\d/.test(c))return a.eatWhile(/[\w\.]/), "number";
			if ("/" == c) {
				if (a.eat("*"))return b.tokenize = q, q(a, b);
				if (a.eat("/"))return a.skipToEnd(), "comment"
			}
			if (m.test(c))return a.eatWhile(m), "operator";
			a.eatWhile(/[\w\$_]/);
			var e = a.current();
			return g.propertyIsEnumerable(e) ? (i.propertyIsEnumerable(e) && (n = "newstatement"), "keyword") : h.propertyIsEnumerable(e) ? (i.propertyIsEnumerable(e) && (n = "newstatement"), "builtin") : j.propertyIsEnumerable(e) ? "atom" : "variable"
		}

		function p(a) {
			return function (b, c) {
				for (var e, d = !1, f = !1; null != (e = b.next());) {
					if (e == a && !d) {
						f = !0;
						break
					}
					d = !d && "\\" == e
				}
				return (f || !d && !l) && (c.tokenize = null), "string"
			}
		}

		function q(a, b) {
			for (var d, c = !1; d = a.next();) {
				if ("/" == d && c) {
					b.tokenize = null;
					break
				}
				c = "*" == d
			}
			return "comment"
		}

		function r(a, b, c, d, e) {
			this.indented = a, this.column = b, this.type = c, this.align = d, this.prev = e
		}

		function s(a, b, c) {
			var d = a.indented;
			return a.context && "statement" == a.context.type && (d = a.context.indented), a.context = new r(d, b, c, null, a.context)
		}

		function t(a) {
			var b = a.context.type;
			return (")" == b || "]" == b || "}" == b) && (a.indented = a.context.indented), a.context = a.context.prev
		}

		var n, d = b.indentUnit, e = c.statementIndentUnit || d, f = c.dontAlignCalls, g = c.keywords || {}, h = c.builtin || {}, i = c.blockKeywords || {}, j = c.atoms || {}, k = c.hooks || {}, l = c.multiLineStrings, m = /[+\-*&%=<>!?|\/]/;
		return {
			startState:       function (a) {
				return {tokenize: null, context: new r((a || 0) - d, 0, "top", !1), indented: 0, startOfLine: !0}
			}, token:         function (a, b) {
				var c = b.context;
				if (a.sol() && (null == c.align && (c.align = !1), b.indented = a.indentation(), b.startOfLine = !0), a.eatSpace())return null;
				n = null;
				var d = (b.tokenize || o)(a, b);
				if ("comment" == d || "meta" == d)return d;
				if (null == c.align && (c.align = !0), ";" != n && ":" != n && "," != n || "statement" != c.type)if ("{" == n)s(b, a.column(), "}"); else if ("[" == n)s(b, a.column(), "]"); else if ("(" == n)s(b, a.column(), ")"); else if ("}" == n) {
					for (; "statement" == c.type;)c = t(b);
					for ("}" == c.type && (c = t(b)); "statement" == c.type;)c = t(b)
				} else n == c.type ? t(b) : (("}" == c.type || "top" == c.type) && ";" != n || "statement" == c.type && "newstatement" == n) && s(b, a.column(), "statement"); else t(b);
				return b.startOfLine = !1, d
			}, indent:        function (b, c) {
				if (b.tokenize != o && null != b.tokenize)return a.Pass;
				var g = b.context, h = c && c.charAt(0);
				"statement" == g.type && "}" == h && (g = g.prev);
				var i = h == g.type;
				return "statement" == g.type ? g.indented + ("{" == h ? 0 : e) : !g.align || f && ")" == g.type ? ")" != g.type || i ? g.indented + (i ? 0 : d) : g.indented + e : g.column + (i ? 0 : 1)
			}, electricChars: "{}", blockCommentStart: "/*", blockCommentEnd: "*/", lineComment: "//", fold: "brace"
		}
	});
	var c = "auto if break int case long char register continue return default short do sizeof double static else struct entry switch extern typedef float union for unsigned goto while enum void const signed volatile";
	h(["text/x-csrc", "text/x-c", "text/x-chdr"], {
		name:          "clike",
		keywords:      b(c),
		blockKeywords: b("case do else for if switch while struct"),
		atoms:         b("null"),
		hooks:         {"#": d},
		modeProps:     {fold: ["brace", "include"]}
	}), h(["text/x-c++src", "text/x-c++hdr"], {
		name:          "clike",
		keywords:      b(c + " asm dynamic_cast namespace reinterpret_cast try bool explicit new " + "static_cast typeid catch operator template typename class friend private " + "this using const_cast inline public throw virtual delete mutable protected " + "wchar_t alignas alignof constexpr decltype nullptr noexcept thread_local final " + "static_assert override"),
		blockKeywords: b("catch class do else finally for if struct switch try while"),
		atoms:         b("true false null"),
		hooks:         {"#": d, u: e, U: e, L: e, R: e},
		modeProps:     {fold: ["brace", "include"]}
	}), h("text/x-java", {
		name:          "clike",
		keywords:      b("abstract assert boolean break byte case catch char class const continue default do double else enum extends final finally float for goto if implements import instanceof int interface long native new package private protected public return short static strictfp super switch synchronized this throw throws transient try void volatile while"),
		blockKeywords: b("catch class do else finally for if switch try while"),
		atoms:         b("true false null"),
		hooks:         {
			"@": function (a) {
				return a.eatWhile(/[\w\$_]/), "meta"
			}
		},
		modeProps:     {fold: ["brace", "import"]}
	}), h("text/x-csharp", {
		name:          "clike",
		keywords:      b("abstract as base break case catch checked class const continue default delegate do else enum event explicit extern finally fixed for foreach goto if implicit in interface internal is lock namespace new operator out override params private protected public readonly ref return sealed sizeof stackalloc static struct switch this throw try typeof unchecked unsafe using virtual void volatile while add alias ascending descending dynamic from get global group into join let orderby partial remove select set value var yield"),
		blockKeywords: b("catch class do else finally for foreach if struct switch try while"),
		builtin:       b("Boolean Byte Char DateTime DateTimeOffset Decimal Double Guid Int16 Int32 Int64 Object SByte Single String TimeSpan UInt16 UInt32 UInt64 bool byte char decimal double short int long object sbyte float string ushort uint ulong"),
		atoms:         b("true false null"),
		hooks:         {
			"@": function (a, b) {
				return a.eat('"') ? (b.tokenize = f, f(a, b)) : (a.eatWhile(/[\w\$_]/), "meta")
			}
		}
	}), h("text/x-scala", {
		name:          "clike",
		keywords:      b("abstract case catch class def do else extends false final finally for forSome if implicit import lazy match new null object override package private protected return sealed super this throw trait try trye type val var while with yield _ : = => <- <: <% >: # @ assert assume require print println printf readLine readBoolean readByte readShort readChar readInt readLong readFloat readDouble AnyVal App Application Array BufferedIterator BigDecimal BigInt Char Console Either Enumeration Equiv Error Exception Fractional Function IndexedSeq Integral Iterable Iterator List Map Numeric Nil NotNull Option Ordered Ordering PartialFunction PartialOrdering Product Proxy Range Responder Seq Serializable Set Specializable Stream StringBuilder StringContext Symbol Throwable Traversable TraversableOnce Tuple Unit Vector :: #:: Boolean Byte Character CharSequence Class ClassLoader Cloneable Comparable Compiler Double Exception Float Integer Long Math Number Object Package Pair Process Runtime Runnable SecurityManager Short StackTraceElement StrictMath String StringBuffer System Thread ThreadGroup ThreadLocal Throwable Triple Void"),
		blockKeywords: b("catch class do else finally for forSome if match switch try while"),
		atoms:         b("true false null"),
		hooks:         {
			"@": function (a) {
				return a.eatWhile(/[\w\$_]/), "meta"
			}
		}
	}), h(["x-shader/x-vertex", "x-shader/x-fragment"], {
		name:          "clike",
		keywords:      b("float int bool void vec2 vec3 vec4 ivec2 ivec3 ivec4 bvec2 bvec3 bvec4 mat2 mat3 mat4 sampler1D sampler2D sampler3D samplerCube sampler1DShadow sampler2DShadowconst attribute uniform varying break continue discard return for while do if else struct in out inout"),
		blockKeywords: b("for while do if else struct"),
		builtin:       b("radians degrees sin cos tan asin acos atan pow exp log exp2 sqrt inversesqrt abs sign floor ceil fract mod min max clamp mix step smootstep length distance dot cross normalize ftransform faceforward reflect refract matrixCompMult lessThan lessThanEqual greaterThan greaterThanEqual equal notEqual any all not texture1D texture1DProj texture1DLod texture1DProjLod texture2D texture2DProj texture2DLod texture2DProjLod texture3D texture3DProj texture3DLod texture3DProjLod textureCube textureCubeLod shadow1D shadow2D shadow1DProj shadow2DProj shadow1DLod shadow2DLod shadow1DProjLod shadow2DProjLod dFdx dFdy fwidth noise1 noise2 noise3 noise4"),
		atoms:         b("true false gl_FragColor gl_SecondaryColor gl_Normal gl_Vertex gl_MultiTexCoord0 gl_MultiTexCoord1 gl_MultiTexCoord2 gl_MultiTexCoord3 gl_MultiTexCoord4 gl_MultiTexCoord5 gl_MultiTexCoord6 gl_MultiTexCoord7 gl_FogCoord gl_Position gl_PointSize gl_ClipVertex gl_FrontColor gl_BackColor gl_FrontSecondaryColor gl_BackSecondaryColor gl_TexCoord gl_FogFragCoord gl_FragCoord gl_FrontFacing gl_FragColor gl_FragData gl_FragDepth gl_ModelViewMatrix gl_ProjectionMatrix gl_ModelViewProjectionMatrix gl_TextureMatrix gl_NormalMatrix gl_ModelViewMatrixInverse gl_ProjectionMatrixInverse gl_ModelViewProjectionMatrixInverse gl_TexureMatrixTranspose gl_ModelViewMatrixInverseTranspose gl_ProjectionMatrixInverseTranspose gl_ModelViewProjectionMatrixInverseTranspose gl_TextureMatrixInverseTranspose gl_NormalScale gl_DepthRange gl_ClipPlane gl_Point gl_FrontMaterial gl_BackMaterial gl_LightSource gl_LightModel gl_FrontLightModelProduct gl_BackLightModelProduct gl_TextureColor gl_EyePlaneS gl_EyePlaneT gl_EyePlaneR gl_EyePlaneQ gl_FogParameters gl_MaxLights gl_MaxClipPlanes gl_MaxTextureUnits gl_MaxTextureCoords gl_MaxVertexAttribs gl_MaxVertexUniformComponents gl_MaxVaryingFloats gl_MaxVertexTextureImageUnits gl_MaxTextureImageUnits gl_MaxFragmentUniformComponents gl_MaxCombineTextureImageUnits gl_MaxDrawBuffers"),
		hooks:         {"#": d},
		modeProps:     {fold: ["brace", "include"]}
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a) {
		for (var b = {}, c = 0; c < a.length; ++c)b[a[c]] = !0;
		return b
	}

	function q(a, b) {
		for (var d, c = !1; null != (d = a.next());) {
			if (c && "/" == d) {
				b.tokenize = null;
				break
			}
			c = "*" == d
		}
		return ["comment", "comment"]
	}

	function r(a, b) {
		return a.skipTo("-->") ? (a.match("-->"), b.tokenize = null) : a.skipToEnd(), ["comment", "comment"]
	}

	a.defineMode("css", function (b, c) {
		function p(a, b) {
			return n = b, a
		}

		function q(a, b) {
			var c = a.next();
			if (e[c]) {
				var d = e[c](a, b);
				if (d !== !1)return d
			}
			return "@" == c ? (a.eatWhile(/[\w\\\-]/), p("def", a.current())) : "=" == c || ("~" == c || "|" == c) && a.eat("=") ? p(null, "compare") : '"' == c || "'" == c ? (b.tokenize = r(c), b.tokenize(a, b)) : "#" == c ? (a.eatWhile(/[\w\\\-]/), p("atom", "hash")) : "!" == c ? (a.match(/^\s*\w*/), p("keyword", "important")) : /\d/.test(c) || "." == c && a.eat(/\d/) ? (a.eatWhile(/[\w.%]/), p("number", "unit")) : "-" !== c ? /[,+>*\/]/.test(c) ? p(null, "select-op") : "." == c && a.match(/^-?[_a-z][_a-z0-9-]*/i) ? p("qualifier", "qualifier") : /[:;{}\[\]\(\)]/.test(c) ? p(null, c) : "u" == c && a.match("rl(") ? (a.backUp(1), b.tokenize = s, p("property", "word")) : /[\w\\\-]/.test(c) ? (a.eatWhile(/[\w\\\-]/), p("property", "word")) : p(null, null) : /[\d.]/.test(a.peek()) ? (a.eatWhile(/[\w.%]/), p("number", "unit")) : a.match(/^\w+-/) ? p("meta", "meta") : void 0
		}

		function r(a) {
			return function (b, c) {
				for (var e, d = !1; null != (e = b.next());) {
					if (e == a && !d) {
						")" == a && b.backUp(1);
						break
					}
					d = !d && "\\" == e
				}
				return (e == a || !d && ")" != a) && (c.tokenize = null), p("string", "string")
			}
		}

		function s(a, b) {
			return a.next(), b.tokenize = a.match(/\s*[\"\')]/, !1) ? null : r(")"), p(null, "(")
		}

		function t(a, b, c) {
			this.type = a, this.indent = b, this.prev = c
		}

		function u(a, b, c) {
			return a.context = new t(c, b.indentation() + d, a.context), c
		}

		function v(a) {
			return a.context = a.context.prev, a.context.type
		}

		function w(a, b, c) {
			return z[c.context.type](a, b, c)
		}

		function x(a, b, c, d) {
			for (var e = d || 1; e > 0; e--)c.context = c.context.prev;
			return w(a, b, c)
		}

		function y(a) {
			var b = a.current().toLowerCase();
			o = k.hasOwnProperty(b) ? "atom" : j.hasOwnProperty(b) ? "keyword" : "variable"
		}

		c.propertyKeywords || (c = a.resolveMode("text/css"));
		var n, o, d = b.indentUnit, e = c.tokenHooks, f = c.mediaTypes || {}, g = c.mediaFeatures || {}, h = c.propertyKeywords || {}, i = c.nonStandardPropertyKeywords || {}, j = c.colorKeywords || {}, k = c.valueKeywords || {}, l = c.fontProperties || {}, m = c.allowNested, z = {};
		return z.top = function (a, b, c) {
			if ("{" == a)return u(c, b, "block");
			if ("}" == a && c.context.prev)return v(c);
			if ("@media" == a)return u(c, b, "media");
			if ("@font-face" == a)return "font_face_before";
			if (/^@(-(moz|ms|o|webkit)-)?keyframes$/.test(a))return "keyframes";
			if (a && "@" == a.charAt(0))return u(c, b, "at");
			if ("hash" == a)o = "builtin"; else if ("word" == a)o = "tag"; else {
				if ("variable-definition" == a)return "maybeprop";
				if ("interpolation" == a)return u(c, b, "interpolation");
				if (":" == a)return "pseudo";
				if (m && "(" == a)return u(c, b, "parens")
			}
			return c.context.type
		}, z.block = function (a, b, c) {
			if ("word" == a) {
				var d = b.current().toLowerCase();
				return h.hasOwnProperty(d) ? (o = "property", "maybeprop") : i.hasOwnProperty(d) ? (o = "string-2", "maybeprop") : m ? (o = b.match(/^\s*:/, !1) ? "property" : "tag", "block") : (o += " error", "maybeprop")
			}
			return "meta" == a ? "block" : m || "hash" != a && "qualifier" != a ? z.top(a, b, c) : (o = "error", "block")
		}, z.maybeprop = function (a, b, c) {
			return ":" == a ? u(c, b, "prop") : w(a, b, c)
		}, z.prop = function (a, b, c) {
			if (";" == a)return v(c);
			if ("{" == a && m)return u(c, b, "propBlock");
			if ("}" == a || "{" == a)return x(a, b, c);
			if ("(" == a)return u(c, b, "parens");
			if ("hash" != a || /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/.test(b.current())) {
				if ("word" == a)y(b); else if ("interpolation" == a)return u(c, b, "interpolation")
			} else o += " error";
			return "prop"
		}, z.propBlock = function (a, b, c) {
			return "}" == a ? v(c) : "word" == a ? (o = "property", "maybeprop") : c.context.type
		}, z.parens = function (a, b, c) {
			return "{" == a || "}" == a ? x(a, b, c) : ")" == a ? v(c) : "(" == a ? u(c, b, "parens") : ("word" == a && y(b), "parens")
		}, z.pseudo = function (a, b, c) {
			return "word" == a ? (o = "variable-3", c.context.type) : w(a, b, c)
		}, z.media = function (a, b, c) {
			if ("(" == a)return u(c, b, "media_parens");
			if ("}" == a)return x(a, b, c);
			if ("{" == a)return v(c) && u(c, b, m ? "block" : "top");
			if ("word" == a) {
				var d = b.current().toLowerCase();
				o = "only" == d || "not" == d || "and" == d ? "keyword" : f.hasOwnProperty(d) ? "attribute" : g.hasOwnProperty(d) ? "property" : "error"
			}
			return c.context.type
		}, z.media_parens = function (a, b, c) {
			return ")" == a ? v(c) : "{" == a || "}" == a ? x(a, b, c, 2) : z.media(a, b, c)
		}, z.font_face_before = function (a, b, c) {
			return "{" == a ? u(c, b, "font_face") : w(a, b, c)
		}, z.font_face = function (a, b, c) {
			return "}" == a ? v(c) : "word" == a ? (o = l.hasOwnProperty(b.current().toLowerCase()) ? "property" : "error", "maybeprop") : "font_face"
		}, z.keyframes = function (a, b, c) {
			return "word" == a ? (o = "variable", "keyframes") : "{" == a ? u(c, b, "top") : w(a, b, c)
		}, z.at = function (a, b, c) {
			return ";" == a ? v(c) : "{" == a || "}" == a ? x(a, b, c) : ("word" == a ? o = "tag" : "hash" == a && (o = "builtin"), "at")
		}, z.interpolation = function (a, b, c) {
			return "}" == a ? v(c) : "{" == a || ";" == a ? x(a, b, c) : ("variable" != a && (o = "error"), "interpolation")
		}, {
			startState:       function (a) {
				return {tokenize: null, state: "top", context: new t("top", a || 0, null)}
			}, token:         function (a, b) {
				if (!b.tokenize && a.eatSpace())return null;
				var c = (b.tokenize || q)(a, b);
				return c && "object" == typeof c && (n = c[1], c = c[0]), o = c, b.state = z[b.state](n, a, b), o
			}, indent:        function (a, b) {
				var c = a.context, e = b && b.charAt(0), f = c.indent;
				return "prop" != c.type || "}" != e && ")" != e || (c = c.prev), !c.prev || ("}" != e || "block" != c.type && "top" != c.type && "interpolation" != c.type && "font_face" != c.type) && (")" != e || "parens" != c.type && "media_parens" != c.type) && ("{" != e || "at" != c.type && "media" != c.type) || (f = c.indent - d, c = c.prev), f
			}, electricChars: "}", blockCommentStart: "/*", blockCommentEnd: "*/", fold: "brace"
		}
	});
	var c = ["all", "aural", "braille", "handheld", "print", "projection", "screen", "tty", "tv", "embossed"], d = b(c), e = ["width", "min-width", "max-width", "height", "min-height", "max-height", "device-width", "min-device-width", "max-device-width", "device-height", "min-device-height", "max-device-height", "aspect-ratio", "min-aspect-ratio", "max-aspect-ratio", "device-aspect-ratio", "min-device-aspect-ratio", "max-device-aspect-ratio", "color", "min-color", "max-color", "color-index", "min-color-index", "max-color-index", "monochrome", "min-monochrome", "max-monochrome", "resolution", "min-resolution", "max-resolution", "scan", "grid"], f = b(e), g = ["align-content", "align-items", "align-self", "alignment-adjust", "alignment-baseline", "anchor-point", "animation", "animation-delay", "animation-direction", "animation-duration", "animation-fill-mode", "animation-iteration-count", "animation-name", "animation-play-state", "animation-timing-function", "appearance", "azimuth", "backface-visibility", "background", "background-attachment", "background-clip", "background-color", "background-image", "background-origin", "background-position", "background-repeat", "background-size", "baseline-shift", "binding", "bleed", "bookmark-label", "bookmark-level", "bookmark-state", "bookmark-target", "border", "border-bottom", "border-bottom-color", "border-bottom-left-radius", "border-bottom-right-radius", "border-bottom-style", "border-bottom-width", "border-collapse", "border-color", "border-image", "border-image-outset", "border-image-repeat", "border-image-slice", "border-image-source", "border-image-width", "border-left", "border-left-color", "border-left-style", "border-left-width", "border-radius", "border-right", "border-right-color", "border-right-style", "border-right-width", "border-spacing", "border-style", "border-top", "border-top-color", "border-top-left-radius", "border-top-right-radius", "border-top-style", "border-top-width", "border-width", "bottom", "box-decoration-break", "box-shadow", "box-sizing", "break-after", "break-before", "break-inside", "caption-side", "clear", "clip", "color", "color-profile", "column-count", "column-fill", "column-gap", "column-rule", "column-rule-color", "column-rule-style", "column-rule-width", "column-span", "column-width", "columns", "content", "counter-increment", "counter-reset", "crop", "cue", "cue-after", "cue-before", "cursor", "direction", "display", "dominant-baseline", "drop-initial-after-adjust", "drop-initial-after-align", "drop-initial-before-adjust", "drop-initial-before-align", "drop-initial-size", "drop-initial-value", "elevation", "empty-cells", "fit", "fit-position", "flex", "flex-basis", "flex-direction", "flex-flow", "flex-grow", "flex-shrink", "flex-wrap", "float", "float-offset", "flow-from", "flow-into", "font", "font-feature-settings", "font-family", "font-kerning", "font-language-override", "font-size", "font-size-adjust", "font-stretch", "font-style", "font-synthesis", "font-variant", "font-variant-alternates", "font-variant-caps", "font-variant-east-asian", "font-variant-ligatures", "font-variant-numeric", "font-variant-position", "font-weight", "grid", "grid-area", "grid-auto-columns", "grid-auto-flow", "grid-auto-position", "grid-auto-rows", "grid-column", "grid-column-end", "grid-column-start", "grid-row", "grid-row-end", "grid-row-start", "grid-template", "grid-template-areas", "grid-template-columns", "grid-template-rows", "hanging-punctuation", "height", "hyphens", "icon", "image-orientation", "image-rendering", "image-resolution", "inline-box-align", "justify-content", "left", "letter-spacing", "line-break", "line-height", "line-stacking", "line-stacking-ruby", "line-stacking-shift", "line-stacking-strategy", "list-style", "list-style-image", "list-style-position", "list-style-type", "margin", "margin-bottom", "margin-left", "margin-right", "margin-top", "marker-offset", "marks", "marquee-direction", "marquee-loop", "marquee-play-count", "marquee-speed", "marquee-style", "max-height", "max-width", "min-height", "min-width", "move-to", "nav-down", "nav-index", "nav-left", "nav-right", "nav-up", "object-fit", "object-position", "opacity", "order", "orphans", "outline", "outline-color", "outline-offset", "outline-style", "outline-width", "overflow", "overflow-style", "overflow-wrap", "overflow-x", "overflow-y", "padding", "padding-bottom", "padding-left", "padding-right", "padding-top", "page", "page-break-after", "page-break-before", "page-break-inside", "page-policy", "pause", "pause-after", "pause-before", "perspective", "perspective-origin", "pitch", "pitch-range", "play-during", "position", "presentation-level", "punctuation-trim", "quotes", "region-break-after", "region-break-before", "region-break-inside", "region-fragment", "rendering-intent", "resize", "rest", "rest-after", "rest-before", "richness", "right", "rotation", "rotation-point", "ruby-align", "ruby-overhang", "ruby-position", "ruby-span", "shape-image-threshold", "shape-inside", "shape-margin", "shape-outside", "size", "speak", "speak-as", "speak-header", "speak-numeral", "speak-punctuation", "speech-rate", "stress", "string-set", "tab-size", "table-layout", "target", "target-name", "target-new", "target-position", "text-align", "text-align-last", "text-decoration", "text-decoration-color", "text-decoration-line", "text-decoration-skip", "text-decoration-style", "text-emphasis", "text-emphasis-color", "text-emphasis-position", "text-emphasis-style", "text-height", "text-indent", "text-justify", "text-outline", "text-overflow", "text-shadow", "text-size-adjust", "text-space-collapse", "text-transform", "text-underline-position", "text-wrap", "top", "transform", "transform-origin", "transform-style", "transition", "transition-delay", "transition-duration", "transition-property", "transition-timing-function", "unicode-bidi", "vertical-align", "visibility", "voice-balance", "voice-duration", "voice-family", "voice-pitch", "voice-range", "voice-rate", "voice-stress", "voice-volume", "volume", "white-space", "widows", "width", "word-break", "word-spacing", "word-wrap", "z-index", "clip-path", "clip-rule", "mask", "enable-background", "filter", "flood-color", "flood-opacity", "lighting-color", "stop-color", "stop-opacity", "pointer-events", "color-interpolation", "color-interpolation-filters", "color-rendering", "fill", "fill-opacity", "fill-rule", "image-rendering", "marker", "marker-end", "marker-mid", "marker-start", "shape-rendering", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "text-rendering", "baseline-shift", "dominant-baseline", "glyph-orientation-horizontal", "glyph-orientation-vertical", "text-anchor", "writing-mode"], h = b(g), i = ["scrollbar-arrow-color", "scrollbar-base-color", "scrollbar-dark-shadow-color", "scrollbar-face-color", "scrollbar-highlight-color", "scrollbar-shadow-color", "scrollbar-3d-light-color", "scrollbar-track-color", "shape-inside", "searchfield-cancel-button", "searchfield-decoration", "searchfield-results-button", "searchfield-results-decoration", "zoom"], i = b(i), j = ["aliceblue", "antiquewhite", "aqua", "aquamarine", "azure", "beige", "bisque", "black", "blanchedalmond", "blue", "blueviolet", "brown", "burlywood", "cadetblue", "chartreuse", "chocolate", "coral", "cornflowerblue", "cornsilk", "crimson", "cyan", "darkblue", "darkcyan", "darkgoldenrod", "darkgray", "darkgreen", "darkkhaki", "darkmagenta", "darkolivegreen", "darkorange", "darkorchid", "darkred", "darksalmon", "darkseagreen", "darkslateblue", "darkslategray", "darkturquoise", "darkviolet", "deeppink", "deepskyblue", "dimgray", "dodgerblue", "firebrick", "floralwhite", "forestgreen", "fuchsia", "gainsboro", "ghostwhite", "gold", "goldenrod", "gray", "grey", "green", "greenyellow", "honeydew", "hotpink", "indianred", "indigo", "ivory", "khaki", "lavender", "lavenderblush", "lawngreen", "lemonchiffon", "lightblue", "lightcoral", "lightcyan", "lightgoldenrodyellow", "lightgray", "lightgreen", "lightpink", "lightsalmon", "lightseagreen", "lightskyblue", "lightslategray", "lightsteelblue", "lightyellow", "lime", "limegreen", "linen", "magenta", "maroon", "mediumaquamarine", "mediumblue", "mediumorchid", "mediumpurple", "mediumseagreen", "mediumslateblue", "mediumspringgreen", "mediumturquoise", "mediumvioletred", "midnightblue", "mintcream", "mistyrose", "moccasin", "navajowhite", "navy", "oldlace", "olive", "olivedrab", "orange", "orangered", "orchid", "palegoldenrod", "palegreen", "paleturquoise", "palevioletred", "papayawhip", "peachpuff", "peru", "pink", "plum", "powderblue", "purple", "red", "rosybrown", "royalblue", "saddlebrown", "salmon", "sandybrown", "seagreen", "seashell", "sienna", "silver", "skyblue", "slateblue", "slategray", "snow", "springgreen", "steelblue", "tan", "teal", "thistle", "tomato", "turquoise", "violet", "wheat", "white", "whitesmoke", "yellow", "yellowgreen"], k = b(j), l = ["above", "absolute", "activeborder", "activecaption", "afar", "after-white-space", "ahead", "alias", "all", "all-scroll", "alternate", "always", "amharic", "amharic-abegede", "antialiased", "appworkspace", "arabic-indic", "armenian", "asterisks", "auto", "avoid", "avoid-column", "avoid-page", "avoid-region", "background", "backwards", "baseline", "below", "bidi-override", "binary", "bengali", "blink", "block", "block-axis", "bold", "bolder", "border", "border-box", "both", "bottom", "break", "break-all", "break-word", "button", "button-bevel", "buttonface", "buttonhighlight", "buttonshadow", "buttontext", "cambodian", "capitalize", "caps-lock-indicator", "caption", "captiontext", "caret", "cell", "center", "checkbox", "circle", "cjk-earthly-branch", "cjk-heavenly-stem", "cjk-ideographic", "clear", "clip", "close-quote", "col-resize", "collapse", "column", "compact", "condensed", "contain", "content", "content-box", "context-menu", "continuous", "copy", "cover", "crop", "cross", "crosshair", "currentcolor", "cursive", "dashed", "decimal", "decimal-leading-zero", "default", "default-button", "destination-atop", "destination-in", "destination-out", "destination-over", "devanagari", "disc", "discard", "document", "dot-dash", "dot-dot-dash", "dotted", "double", "down", "e-resize", "ease", "ease-in", "ease-in-out", "ease-out", "element", "ellipse", "ellipsis", "embed", "end", "ethiopic", "ethiopic-abegede", "ethiopic-abegede-am-et", "ethiopic-abegede-gez", "ethiopic-abegede-ti-er", "ethiopic-abegede-ti-et", "ethiopic-halehame-aa-er", "ethiopic-halehame-aa-et", "ethiopic-halehame-am-et", "ethiopic-halehame-gez", "ethiopic-halehame-om-et", "ethiopic-halehame-sid-et", "ethiopic-halehame-so-et", "ethiopic-halehame-ti-er", "ethiopic-halehame-ti-et", "ethiopic-halehame-tig", "ew-resize", "expanded", "extra-condensed", "extra-expanded", "fantasy", "fast", "fill", "fixed", "flat", "footnotes", "forwards", "from", "geometricPrecision", "georgian", "graytext", "groove", "gujarati", "gurmukhi", "hand", "hangul", "hangul-consonant", "hebrew", "help", "hidden", "hide", "higher", "highlight", "highlighttext", "hiragana", "hiragana-iroha", "horizontal", "hsl", "hsla", "icon", "ignore", "inactiveborder", "inactivecaption", "inactivecaptiontext", "infinite", "infobackground", "infotext", "inherit", "initial", "inline", "inline-axis", "inline-block", "inline-table", "inset", "inside", "intrinsic", "invert", "italic", "justify", "kannada", "katakana", "katakana-iroha", "keep-all", "khmer", "landscape", "lao", "large", "larger", "left", "level", "lighter", "line-through", "linear", "lines", "list-item", "listbox", "listitem", "local", "logical", "loud", "lower", "lower-alpha", "lower-armenian", "lower-greek", "lower-hexadecimal", "lower-latin", "lower-norwegian", "lower-roman", "lowercase", "ltr", "malayalam", "match", "media-controls-background", "media-current-time-display", "media-fullscreen-button", "media-mute-button", "media-play-button", "media-return-to-realtime-button", "media-rewind-button", "media-seek-back-button", "media-seek-forward-button", "media-slider", "media-sliderthumb", "media-time-remaining-display", "media-volume-slider", "media-volume-slider-container", "media-volume-sliderthumb", "medium", "menu", "menulist", "menulist-button", "menulist-text", "menulist-textfield", "menutext", "message-box", "middle", "min-intrinsic", "mix", "mongolian", "monospace", "move", "multiple", "myanmar", "n-resize", "narrower", "ne-resize", "nesw-resize", "no-close-quote", "no-drop", "no-open-quote", "no-repeat", "none", "normal", "not-allowed", "nowrap", "ns-resize", "nw-resize", "nwse-resize", "oblique", "octal", "open-quote", "optimizeLegibility", "optimizeSpeed", "oriya", "oromo", "outset", "outside", "outside-shape", "overlay", "overline", "padding", "padding-box", "painted", "page", "paused", "persian", "plus-darker", "plus-lighter", "pointer", "polygon", "portrait", "pre", "pre-line", "pre-wrap", "preserve-3d", "progress", "push-button", "radio", "read-only", "read-write", "read-write-plaintext-only", "rectangle", "region", "relative", "repeat", "repeat-x", "repeat-y", "reset", "reverse", "rgb", "rgba", "ridge", "right", "round", "row-resize", "rtl", "run-in", "running", "s-resize", "sans-serif", "scroll", "scrollbar", "se-resize", "searchfield", "searchfield-cancel-button", "searchfield-decoration", "searchfield-results-button", "searchfield-results-decoration", "semi-condensed", "semi-expanded", "separate", "serif", "show", "sidama", "single", "skip-white-space", "slide", "slider-horizontal", "slider-vertical", "sliderthumb-horizontal", "sliderthumb-vertical", "slow", "small", "small-caps", "small-caption", "smaller", "solid", "somali", "source-atop", "source-in", "source-out", "source-over", "space", "square", "square-button", "start", "static", "status-bar", "stretch", "stroke", "sub", "subpixel-antialiased", "super", "sw-resize", "table", "table-caption", "table-cell", "table-column", "table-column-group", "table-footer-group", "table-header-group", "table-row", "table-row-group", "telugu", "text", "text-bottom", "text-top", "textarea", "textfield", "thai", "thick", "thin", "threeddarkshadow", "threedface", "threedhighlight", "threedlightshadow", "threedshadow", "tibetan", "tigre", "tigrinya-er", "tigrinya-er-abegede", "tigrinya-et", "tigrinya-et-abegede", "to", "top", "transparent", "ultra-condensed", "ultra-expanded", "underline", "up", "upper-alpha", "upper-armenian", "upper-greek", "upper-hexadecimal", "upper-latin", "upper-norwegian", "upper-roman", "uppercase", "urdu", "url", "vertical", "vertical-text", "visible", "visibleFill", "visiblePainted", "visibleStroke", "visual", "w-resize", "wait", "wave", "wider", "window", "windowframe", "windowtext", "x-large", "x-small", "xor", "xx-large", "xx-small"], m = b(l), n = ["font-family", "src", "unicode-range", "font-variant", "font-feature-settings", "font-stretch", "font-weight", "font-style"], o = b(n), p = c.concat(e).concat(g).concat(i).concat(j).concat(l);
	a.registerHelper("hintWords", "css", p), a.defineMIME("text/css", {
		mediaTypes:                  d,
		mediaFeatures:               f,
		propertyKeywords:            h,
		nonStandardPropertyKeywords: i,
		colorKeywords:               k,
		valueKeywords:               m,
		fontProperties:              o,
		tokenHooks:                  {
			"<":    function (a, b) {
				return a.match("!--") ? (b.tokenize = r, r(a, b)) : !1
			}, "/": function (a, b) {
				return a.eat("*") ? (b.tokenize = q, q(a, b)) : !1
			}
		},
		name:                        "css"
	}), a.defineMIME("text/x-scss", {
		mediaTypes:                  d,
		mediaFeatures:               f,
		propertyKeywords:            h,
		nonStandardPropertyKeywords: i,
		colorKeywords:               k,
		valueKeywords:               m,
		fontProperties:              o,
		allowNested:                 !0,
		tokenHooks:                  {
			"/":    function (a, b) {
				return a.eat("/") ? (a.skipToEnd(), ["comment", "comment"]) : a.eat("*") ? (b.tokenize = q, q(a, b)) : ["operator", "operator"]
			}, ":": function (a) {
				return a.match(/\s*{/) ? [null, "{"] : !1
			}, $:   function (a) {
				return a.match(/^[\w-]+/), a.match(/^\s*:/, !1) ? ["variable-2", "variable-definition"] : ["variable-2", "variable"]
			}, "#": function (a) {
				return a.eat("{") ? [null, "interpolation"] : !1
			}
		},
		name:                        "css",
		helperType:                  "scss"
	}), a.defineMIME("text/x-less", {
		mediaTypes:                  d,
		mediaFeatures:               f,
		propertyKeywords:            h,
		nonStandardPropertyKeywords: i,
		colorKeywords:               k,
		valueKeywords:               m,
		fontProperties:              o,
		allowNested:                 !0,
		tokenHooks:                  {
			"/":    function (a, b) {
				return a.eat("/") ? (a.skipToEnd(), ["comment", "comment"]) : a.eat("*") ? (b.tokenize = q, q(a, b)) : ["operator", "operator"]
			}, "@": function (a) {
				return a.match(/^(charset|document|font-face|import|(-(moz|ms|o|webkit)-)?keyframes|media|namespace|page|supports)\b/, !1) ? !1 : (a.eatWhile(/[\w\\\-]/), a.match(/^\s*:/, !1) ? ["variable-2", "variable-definition"] : ["variable-2", "variable"])
			}, "&": function () {
				return ["atom", "atom"]
			}
		},
		name:                        "css",
		helperType:                  "less"
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror"), require("../htmlmixed/htmlmixed")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../htmlmixed/htmlmixed"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("htmlembedded", function (b, c) {
		function h(a, b) {
			return a.match(d, !1) ? (b.token = i, f.token(a, b.scriptState)) : g.token(a, b.htmlState)
		}

		function i(a, b) {
			return a.match(e, !1) ? (b.token = h, g.token(a, b.htmlState)) : f.token(a, b.scriptState)
		}

		var f, g, d = c.scriptStartRegex || /^<%/i, e = c.scriptEndRegex || /^%>/i;
		return {
			startState:   function () {
				return f = f || a.getMode(b, c.scriptingModeSpec), g = g || a.getMode(b, "htmlmixed"), {
					token:       c.startOpen ? i : h,
					htmlState:   a.startState(g),
					scriptState: a.startState(f)
				}
			}, token:     function (a, b) {
				return b.token(a, b)
			}, indent:    function (a, b) {
				return a.token == h ? g.indent(a.htmlState, b) : f.indent ? f.indent(a.scriptState, b) : void 0
			}, copyState: function (b) {
				return {token: b.token, htmlState: a.copyState(g, b.htmlState), scriptState: a.copyState(f, b.scriptState)}
			}, innerMode: function (a) {
				return a.token == i ? {state: a.scriptState, mode: f} : {state: a.htmlState, mode: g}
			}
		}
	}, "htmlmixed"), a.defineMIME("application/x-ejs", {
		name:              "htmlembedded",
		scriptingModeSpec: "javascript"
	}), a.defineMIME("application/x-aspx", {
		name:              "htmlembedded",
		scriptingModeSpec: "text/x-csharp"
	}), a.defineMIME("application/x-jsp", {
		name:              "htmlembedded",
		scriptingModeSpec: "text/x-java"
	}), a.defineMIME("application/x-erb", {name: "htmlembedded", scriptingModeSpec: "ruby"})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror"), require("../xml/xml"), require("../javascript/javascript"), require("../css/css")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../xml/xml", "../javascript/javascript", "../css/css"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("htmlmixed", function (b, c) {
		function j(a, b) {
			var c = b.htmlState.tagName, g = d.token(a, b.htmlState);
			if ("script" == c && /\btag\b/.test(g) && ">" == a.current()) {
				var h = a.string.slice(Math.max(0, a.pos - 100), a.pos).match(/\btype\s*=\s*("[^"]+"|'[^']+'|\S+)[^<]*$/i);
				h = h ? h[1] : "", h && /[\"\']/.test(h.charAt(0)) && (h = h.slice(1, h.length - 1));
				for (var i = 0; i < f.length; ++i) {
					var j = f[i];
					if ("string" == typeof j.matches ? h == j.matches : j.matches.test(h)) {
						j.mode && (b.token = l, b.localMode = j.mode, b.localState = j.mode.startState && j.mode.startState(d.indent(b.htmlState, "")));
						break
					}
				}
			} else"style" == c && /\btag\b/.test(g) && ">" == a.current() && (b.token = m, b.localMode = e, b.localState = e.startState(d.indent(b.htmlState, "")));
			return g
		}

		function k(a, b, c) {
			var f, d = a.current(), e = d.search(b);
			return e > -1 ? a.backUp(d.length - e) : (f = d.match(/<\/?$/)) && (a.backUp(d.length), a.match(b, !1) || a.match(d)), c
		}

		function l(a, b) {
			return a.match(/^<\/\s*script\s*>/i, !1) ? (b.token = j, b.localState = b.localMode = null, j(a, b)) : k(a, /<\/\s*script\s*>/, b.localMode.token(a, b.localState))
		}

		function m(a, b) {
			return a.match(/^<\/\s*style\s*>/i, !1) ? (b.token = j, b.localState = b.localMode = null, j(a, b)) : k(a, /<\/\s*style\s*>/, e.token(a, b.localState))
		}

		var d = a.getMode(b, {
			name:                      "xml",
			htmlMode:                  !0,
			multilineTagIndentFactor:  c.multilineTagIndentFactor,
			multilineTagIndentPastTag: c.multilineTagIndentPastTag
		}), e = a.getMode(b, "css"), f = [], g = c && c.scriptTypes;
		if (f.push({
				matches: /^(?:text|application)\/(?:x-)?(?:java|ecma)script$|^$/i,
				mode:    a.getMode(b, "javascript")
			}), g)for (var h = 0; h < g.length; ++h) {
			var i = g[h];
			f.push({matches: i.matches, mode: i.mode && a.getMode(b, i.mode)})
		}
		return f.push({matches: /./, mode: a.getMode(b, "text/plain")}), {
			startState:   function () {
				var a = d.startState();
				return {token: j, localMode: null, localState: null, htmlState: a}
			}, copyState: function (b) {
				if (b.localState)var c = a.copyState(b.localMode, b.localState);
				return {token: b.token, localMode: b.localMode, localState: c, htmlState: a.copyState(d, b.htmlState)}
			}, token:     function (a, b) {
				return b.token(a, b)
			}, indent:    function (b, c) {
				return !b.localMode || /^\s*<\//.test(c) ? d.indent(b.htmlState, c) : b.localMode.indent ? b.localMode.indent(b.localState, c) : a.Pass
			}, innerMode: function (a) {
				return {state: a.localState || a.htmlState, mode: a.localMode || d}
			}
		}
	}, "xml", "javascript", "css"), a.defineMIME("text/html", "htmlmixed")
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("http", function () {
		function a(a, b) {
			return a.skipToEnd(), b.cur = g, "error"
		}

		function b(b, d) {
			return b.match(/^HTTP\/\d\.\d/) ? (d.cur = c, "keyword") : b.match(/^[A-Z]+/) && /[ \t]/.test(b.peek()) ? (d.cur = e, "keyword") : a(b, d)
		}

		function c(b, c) {
			var e = b.match(/^\d+/);
			if (!e)return a(b, c);
			c.cur = d;
			var f = Number(e[0]);
			return f >= 100 && 200 > f ? "positive informational" : f >= 200 && 300 > f ? "positive success" : f >= 300 && 400 > f ? "positive redirect" : f >= 400 && 500 > f ? "negative client-error" : f >= 500 && 600 > f ? "negative server-error" : "error"
		}

		function d(a, b) {
			return a.skipToEnd(), b.cur = g, null
		}

		function e(a, b) {
			return a.eatWhile(/\S/), b.cur = f, "string-2"
		}

		function f(b, c) {
			return b.match(/^HTTP\/\d\.\d$/) ? (c.cur = g, "keyword") : a(b, c)
		}

		function g(a) {
			return a.sol() && !a.eat(/[ \t]/) ? a.match(/^.*?:/) ? "atom" : (a.skipToEnd(), "error") : (a.skipToEnd(), "string")
		}

		function h(a) {
			return a.skipToEnd(), null
		}

		return {
			token:         function (a, b) {
				var c = b.cur;
				return c != g && c != h && a.eatSpace() ? null : c(a, b)
			}, blankLine:  function (a) {
				a.cur = h
			}, startState: function () {
				return {cur: b}
			}
		}
	}), a.defineMIME("message/http", "http")
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("javascript", function (b, c) {
		function l(a) {
			for (var c, b = !1, d = !1; null != (c = a.next());) {
				if (!b) {
					if ("/" == c && !d)return;
					"[" == c ? d = !0 : d && "]" == c && (d = !1)
				}
				b = !b && "\\" == c
			}
		}

		function o(a, b, c) {
			return m = a, n = c, b
		}

		function p(a, b) {
			var c = a.next();
			if ('"' == c || "'" == c)return b.tokenize = q(c), b.tokenize(a, b);
			if ("." == c && a.match(/^\d+(?:[eE][+\-]?\d+)?/))return o("number", "number");
			if ("." == c && a.match(".."))return o("spread", "meta");
			if (/[\[\]{}\(\),;\:\.]/.test(c))return o(c);
			if ("=" == c && a.eat(">"))return o("=>", "operator");
			if ("0" == c && a.eat(/x/i))return a.eatWhile(/[\da-f]/i), o("number", "number");
			if (/\d/.test(c))return a.match(/^\d*(?:\.\d*)?(?:[eE][+\-]?\d+)?/), o("number", "number");
			if ("/" == c)return a.eat("*") ? (b.tokenize = r, r(a, b)) : a.eat("/") ? (a.skipToEnd(), o("comment", "comment")) : "operator" == b.lastType || "keyword c" == b.lastType || "sof" == b.lastType || /^[\[{}\(,;:]$/.test(b.lastType) ? (l(a), a.eatWhile(/[gimy]/), o("regexp", "string-2")) : (a.eatWhile(j), o("operator", "operator", a.current()));
			if ("`" == c)return b.tokenize = s, s(a, b);
			if ("#" == c)return a.skipToEnd(), o("error", "error");
			if (j.test(c))return a.eatWhile(j), o("operator", "operator", a.current());
			a.eatWhile(/[\w\$_]/);
			var d = a.current(), e = i.propertyIsEnumerable(d) && i[d];
			return e && "." != b.lastType ? o(e.type, e.style, d) : o("variable", "variable", d)
		}

		function q(a) {
			return function (b, c) {
				var e, d = !1;
				if (f && "@" == b.peek() && b.match(k))return c.tokenize = p, o("jsonld-keyword", "meta");
				for (; null != (e = b.next()) && (e != a || d);)d = !d && "\\" == e;
				return d || (c.tokenize = p), o("string", "string")
			}
		}

		function r(a, b) {
			for (var d, c = !1; d = a.next();) {
				if ("/" == d && c) {
					b.tokenize = p;
					break
				}
				c = "*" == d
			}
			return o("comment", "comment")
		}

		function s(a, b) {
			for (var d, c = !1; null != (d = a.next());) {
				if (!c && ("`" == d || "$" == d && a.eat("{"))) {
					b.tokenize = p;
					break
				}
				c = !c && "\\" == d
			}
			return o("quasi", "string-2", a.current())
		}

		function u(a, b) {
			b.fatArrowAt && (b.fatArrowAt = null);
			var c = a.string.indexOf("=>", a.start);
			if (!(0 > c)) {
				for (var d = 0, e = !1, f = c - 1; f >= 0; --f) {
					var g = a.string.charAt(f), h = t.indexOf(g);
					if (h >= 0 && 3 > h) {
						if (!d) {
							++f;
							break
						}
						if (0 == --d)break
					} else if (h >= 3 && 6 > h)++d; else if (/[$\w]/.test(g))e = !0; else if (e && !d) {
						++f;
						break
					}
				}
				e && !d && (b.fatArrowAt = f)
			}
		}

		function w(a, b, c, d, e, f) {
			this.indented = a, this.column = b, this.type = c, this.prev = e, this.info = f, null != d && (this.align = d)
		}

		function x(a, b) {
			for (var c = a.localVars; c; c = c.next)if (c.name == b)return !0;
			for (var d = a.context; d; d = d.prev)for (var c = d.vars; c; c = c.next)if (c.name == b)return !0
		}

		function y(a, b, c, d, e) {
			var f = a.cc;
			for (z.state = a, z.stream = e, z.marked = null, z.cc = f, z.style = b, a.lexical.hasOwnProperty("align") || (a.lexical.align = !0); ;) {
				var h = f.length ? f.pop() : g ? K : J;
				if (h(c, d)) {
					for (; f.length && f[f.length - 1].lex;)f.pop()();
					return z.marked ? z.marked : "variable" == c && x(a, d) ? "variable-2" : b
				}
			}
		}

		function A() {
			for (var a = arguments.length - 1; a >= 0; a--)z.cc.push(arguments[a])
		}

		function B() {
			return A.apply(null, arguments), !0
		}

		function C(a) {
			function b(b) {
				for (var c = b; c; c = c.next)if (c.name == a)return !0;
				return !1
			}

			var d = z.state;
			if (d.context) {
				if (z.marked = "def", b(d.localVars))return;
				d.localVars = {name: a, next: d.localVars}
			} else {
				if (b(d.globalVars))return;
				c.globalVars && (d.globalVars = {name: a, next: d.globalVars})
			}
		}

		function E() {
			z.state.context = {prev: z.state.context, vars: z.state.localVars}, z.state.localVars = D
		}

		function F() {
			z.state.localVars = z.state.context.vars, z.state.context = z.state.context.prev
		}

		function G(a, b) {
			var c = function () {
				var c = z.state, d = c.indented;
				"stat" == c.lexical.type && (d = c.lexical.indented), c.lexical = new w(d, z.stream.column(), a, null, c.lexical, b)
			};
			return c.lex = !0, c
		}

		function H() {
			var a = z.state;
			a.lexical.prev && (")" == a.lexical.type && (a.indented = a.lexical.indented), a.lexical = a.lexical.prev)
		}

		function I(a) {
			function b(c) {
				return c == a ? B() : ";" == a ? A() : B(b)
			}

			return b
		}

		function J(a, b) {
			return "var" == a ? B(G("vardef", b.length), db, I(";"), H) : "keyword a" == a ? B(G("form"), K, J, H) : "keyword b" == a ? B(G("form"), J, H) : "{" == a ? B(G("}"), ab, H) : ";" == a ? B() : "if" == a ? ("else" == z.state.lexical.info && z.state.cc[z.state.cc.length - 1] == H && z.state.cc.pop()(), B(G("form"), K, J, H, ib)) : "function" == a ? B(ob) : "for" == a ? B(G("form"), jb, J, H) : "variable" == a ? B(G("stat"), V) : "switch" == a ? B(G("form"), K, G("}", "switch"), I("{"), ab, H, H) : "case" == a ? B(K, I(":")) : "default" == a ? B(I(":")) : "catch" == a ? B(G("form"), E, I("("), pb, I(")"), J, H, F) : "module" == a ? B(G("form"), E, ub, F, H) : "class" == a ? B(G("form"), qb, H) : "export" == a ? B(G("form"), vb, H) : "import" == a ? B(G("form"), wb, H) : A(G("stat"), K, I(";"), H)
		}

		function K(a) {
			return M(a, !1)
		}

		function L(a) {
			return M(a, !0)
		}

		function M(a, b) {
			if (z.state.fatArrowAt == z.stream.start) {
				var c = b ? U : T;
				if ("(" == a)return B(E, G(")"), $(eb, ")"), H, I("=>"), c, F);
				if ("variable" == a)return A(E, eb, I("=>"), c, F)
			}
			var d = b ? Q : P;
			return v.hasOwnProperty(a) ? B(d) : "function" == a ? B(ob, d) : "keyword c" == a ? B(b ? O : N) : "(" == a ? B(G(")"), N, Bb, I(")"), H, d) : "operator" == a || "spread" == a ? B(b ? L : K) : "[" == a ? B(G("]"), zb, H, d) : "{" == a ? _(X, "}", null, d) : "quasi" == a ? A(R, d) : B()
		}

		function N(a) {
			return a.match(/[;\}\)\],]/) ? A() : A(K)
		}

		function O(a) {
			return a.match(/[;\}\)\],]/) ? A() : A(L)
		}

		function P(a, b) {
			return "," == a ? B(K) : Q(a, b, !1)
		}

		function Q(a, b, c) {
			var d = 0 == c ? P : Q, e = 0 == c ? K : L;
			return "=>" == b ? B(E, c ? U : T, F) : "operator" == a ? /\+\+|--/.test(b) ? B(d) : "?" == b ? B(K, I(":"), e) : B(e) : "quasi" == a ? A(R, d) : ";" != a ? "(" == a ? _(L, ")", "call", d) : "." == a ? B(W, d) : "[" == a ? B(G("]"), N, I("]"), H, d) : void 0 : void 0
		}

		function R(a, b) {
			return "quasi" != a ? A() : "${" != b.slice(b.length - 2) ? B(R) : B(K, S)
		}

		function S(a) {
			return "}" == a ? (z.marked = "string-2", z.state.tokenize = s, B(R)) : void 0
		}

		function T(a) {
			return u(z.stream, z.state), "{" == a ? A(J) : A(K)
		}

		function U(a) {
			return u(z.stream, z.state), "{" == a ? A(J) : A(L)
		}

		function V(a) {
			return ":" == a ? B(H, J) : A(P, I(";"), H)
		}

		function W(a) {
			return "variable" == a ? (z.marked = "property", B()) : void 0
		}

		function X(a, b) {
			return "variable" == a || "keyword" == z.style ? (z.marked = "property", "get" == b || "set" == b ? B(Y) : B(Z)) : "number" == a || "string" == a ? (z.marked = f ? "property" : z.style + " property", B(Z)) : "jsonld-keyword" == a ? B(Z) : "[" == a ? B(K, I("]"), Z) : void 0
		}

		function Y(a) {
			return "variable" != a ? A(Z) : (z.marked = "property", B(ob))
		}

		function Z(a) {
			return ":" == a ? B(L) : "(" == a ? A(ob) : void 0
		}

		function $(a, b) {
			function c(d) {
				if ("," == d) {
					var e = z.state.lexical;
					return "call" == e.info && (e.pos = (e.pos || 0) + 1), B(a, c)
				}
				return d == b ? B() : B(I(b))
			}

			return function (d) {
				return d == b ? B() : A(a, c)
			}
		}

		function _(a, b, c) {
			for (var d = 3; d < arguments.length; d++)z.cc.push(arguments[d]);
			return B(G(b, c), $(a, b), H)
		}

		function ab(a) {
			return "}" == a ? B() : A(J, ab)
		}

		function bb(a) {
			return h && ":" == a ? B(cb) : void 0
		}

		function cb(a) {
			return "variable" == a ? (z.marked = "variable-3", B()) : void 0
		}

		function db() {
			return A(eb, bb, gb, hb)
		}

		function eb(a, b) {
			return "variable" == a ? (C(b), B()) : "[" == a ? _(eb, "]") : "{" == a ? _(fb, "}") : void 0
		}

		function fb(a, b) {
			return "variable" != a || z.stream.match(/^\s*:/, !1) ? ("variable" == a && (z.marked = "property"), B(I(":"), eb, gb)) : (C(b), B(gb))
		}

		function gb(a, b) {
			return "=" == b ? B(L) : void 0
		}

		function hb(a) {
			return "," == a ? B(db) : void 0
		}

		function ib(a, b) {
			return "keyword b" == a && "else" == b ? B(G("form", "else"), J, H) : void 0
		}

		function jb(a) {
			return "(" == a ? B(G(")"), kb, I(")"), H) : void 0
		}

		function kb(a) {
			return "var" == a ? B(db, I(";"), mb) : ";" == a ? B(mb) : "variable" == a ? B(lb) : A(K, I(";"), mb)
		}

		function lb(a, b) {
			return "in" == b || "of" == b ? (z.marked = "keyword", B(K)) : B(P, mb)
		}

		function mb(a, b) {
			return ";" == a ? B(nb) : "in" == b || "of" == b ? (z.marked = "keyword", B(K)) : A(K, I(";"), nb)
		}

		function nb(a) {
			")" != a && B(K)
		}

		function ob(a, b) {
			return "*" == b ? (z.marked = "keyword", B(ob)) : "variable" == a ? (C(b), B(ob)) : "(" == a ? B(E, G(")"), $(pb, ")"), H, J, F) : void 0
		}

		function pb(a) {
			return "spread" == a ? B(pb) : A(eb, bb)
		}

		function qb(a, b) {
			return "variable" == a ? (C(b), B(rb)) : void 0
		}

		function rb(a, b) {
			return "extends" == b ? B(K, rb) : "{" == a ? B(G("}"), sb, H) : void 0
		}

		function sb(a, b) {
			return "variable" == a || "keyword" == z.style ? (z.marked = "property", "get" == b || "set" == b ? B(tb, ob, sb) : B(ob, sb)) : "*" == b ? (z.marked = "keyword", B(sb)) : ";" == a ? B(sb) : "}" == a ? B() : void 0
		}

		function tb(a) {
			return "variable" != a ? A() : (z.marked = "property", B())
		}

		function ub(a, b) {
			return "string" == a ? B(J) : "variable" == a ? (C(b), B(yb)) : void 0
		}

		function vb(a, b) {
			return "*" == b ? (z.marked = "keyword", B(yb, I(";"))) : "default" == b ? (z.marked = "keyword", B(K, I(";"))) : A(J)
		}

		function wb(a) {
			return "string" == a ? B() : A(xb, yb)
		}

		function xb(a, b) {
			return "{" == a ? _(xb, "}") : ("variable" == a && C(b), B())
		}

		function yb(a, b) {
			return "from" == b ? (z.marked = "keyword", B(K)) : void 0
		}

		function zb(a) {
			return "]" == a ? B() : A(L, Ab)
		}

		function Ab(a) {
			return "for" == a ? A(Bb, I("]")) : "," == a ? B($(L, "]")) : A($(L, "]"))
		}

		function Bb(a) {
			return "for" == a ? B(jb, Bb) : "if" == a ? B(K, Bb) : void 0
		}

		var m, n, d = b.indentUnit, e = c.statementIndent, f = c.jsonld, g = c.json || f, h = c.typescript, i = function () {
			function a(a) {
				return {type: a, style: "keyword"}
			}

			var b = a("keyword a"), c = a("keyword b"), d = a("keyword c"), e = a("operator"), f = {
				type:  "atom",
				style: "atom"
			}, g = {
				"if":         a("if"),
				"while":      b,
				"with":       b,
				"else":       c,
				"do":         c,
				"try":        c,
				"finally":    c,
				"return":     d,
				"break":      d,
				"continue":   d,
				"new":        d,
				"delete":     d,
				"throw":      d,
				"debugger":   d,
				"var":        a("var"),
				"const":      a("var"),
				let:          a("var"),
				"function":   a("function"),
				"catch":      a("catch"),
				"for":        a("for"),
				"switch":     a("switch"),
				"case":       a("case"),
				"default":    a("default"),
				"in":         e,
				"typeof":     e,
				"instanceof": e,
				"true":       f,
				"false":      f,
				"null":       f,
				undefined:    f,
				NaN:          f,
				Infinity:     f,
				"this":       a("this"),
				module:       a("module"),
				"class":      a("class"),
				"super":      a("atom"),
				yield:        d,
				"export":     a("export"),
				"import":     a("import"),
				"extends":    d
			};
			if (h) {
				var i = {type: "variable", style: "variable-3"}, j = {
					"interface": a("interface"),
					"extends":   a("extends"),
					constructor: a("constructor"),
					"public":    a("public"),
					"private":   a("private"),
					"protected": a("protected"),
					"static":    a("static"),
					string:      i,
					number:      i,
					bool:        i,
					any:         i
				};
				for (var k in j)g[k] = j[k]
			}
			return g
		}(), j = /[+\-*&%=<>!?|~^]/, k = /^@(context|id|value|language|type|container|list|set|reverse|index|base|vocab|graph)"/, t = "([{}])", v = {
			atom: !0,
			number: !0,
			variable: !0,
			string: !0,
			regexp: !0,
			"this": !0,
			"jsonld-keyword": !0
		}, z = {state: null, column: null, marked: null, cc: null}, D = {name: "this", next: {name: "arguments"}};
		return H.lex = !0, {
			startState:        function (a) {
				var b = {
					tokenize:  p,
					lastType:  "sof",
					cc:        [],
					lexical:   new w((a || 0) - d, 0, "block", !1),
					localVars: c.localVars,
					context:   c.localVars && {vars: c.localVars},
					indented:  0
				};
				return c.globalVars && "object" == typeof c.globalVars && (b.globalVars = c.globalVars), b
			},
			token:             function (a, b) {
				if (a.sol() && (b.lexical.hasOwnProperty("align") || (b.lexical.align = !1), b.indented = a.indentation(), u(a, b)), b.tokenize != r && a.eatSpace())return null;
				var c = b.tokenize(a, b);
				return "comment" == m ? c : (b.lastType = "operator" != m || "++" != n && "--" != n ? m : "incdec", y(b, c, m, n, a))
			},
			indent:            function (b, f) {
				if (b.tokenize == r)return a.Pass;
				if (b.tokenize != p)return 0;
				var g = f && f.charAt(0), h = b.lexical;
				if (!/^\s*else\b/.test(f))for (var i = b.cc.length - 1; i >= 0; --i) {
					var j = b.cc[i];
					if (j == H)h = h.prev; else if (j != ib)break
				}
				"stat" == h.type && "}" == g && (h = h.prev), e && ")" == h.type && "stat" == h.prev.type && (h = h.prev);
				var k = h.type, l = g == k;
				return "vardef" == k ? h.indented + ("operator" == b.lastType || "," == b.lastType ? h.info + 1 : 0) : "form" == k && "{" == g ? h.indented : "form" == k ? h.indented + d : "stat" == k ? h.indented + ("operator" == b.lastType || "," == b.lastType ? e || d : 0) : "switch" != h.info || l || 0 == c.doubleIndentSwitch ? h.align ? h.column + (l ? 0 : 1) : h.indented + (l ? 0 : d) : h.indented + (/^(?:case|default)\b/.test(f) ? d : 2 * d)
			},
			electricChars:     ":{}",
			blockCommentStart: g ? null : "/*",
			blockCommentEnd:   g ? null : "*/",
			lineComment:       g ? null : "//",
			fold:              "brace",
			helperType:        g ? "json" : "javascript",
			jsonldMode:        f,
			jsonMode:          g
		}
	}), a.registerHelper("wordChars", "javascript", /[\\w$]/), a.defineMIME("text/javascript", "javascript"), a.defineMIME("text/ecmascript", "javascript"), a.defineMIME("application/javascript", "javascript"), a.defineMIME("application/ecmascript", "javascript"), a.defineMIME("application/json", {
		name: "javascript",
		json: !0
	}), a.defineMIME("application/x-json", {
		name: "javascript",
		json: !0
	}), a.defineMIME("application/ld+json", {
		name:   "javascript",
		jsonld: !0
	}), a.defineMIME("text/typescript", {
		name:       "javascript",
		typescript: !0
	}), a.defineMIME("application/typescript", {name: "javascript", typescript: !0})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a, b) {
		return a.string.charAt(a.pos + (b || 0))
	}

	function c(a, b) {
		if (b) {
			var c = a.pos - b;
			return a.string.substr(c >= 0 ? c : 0, b)
		}
		return a.string.substr(0, a.pos - 1)
	}

	function d(a, b) {
		var c = a.string.length, d = c - a.pos + 1;
		return a.string.substr(a.pos, b && c > b ? b : d)
	}

	function e(a, b) {
		var d, c = a.pos + b;
		a.pos = 0 >= c ? 0 : c >= (d = a.string.length - 1) ? d : c
	}

	a.defineMode("perl", function () {
		function h(a, b, c, d, e) {
			return b.chain = null, b.style = null, b.tail = null, b.tokenize = function (a, b) {
				for (var g, f = !1, h = 0; g = a.next();) {
					if (g === c[h] && !f)return void 0 !== c[++h] ? (b.chain = c[h], b.style = d, b.tail = e) : e && a.eatWhile(e), b.tokenize = j, d;
					f = !f && "\\" == g
				}
				return d
			}, b.tokenize(a, b)
		}

		function i(a, b, c) {
			return b.tokenize = function (a, b) {
				return a.string == c && (b.tokenize = j), a.skipToEnd(), "string"
			}, b.tokenize(a, b)
		}

		function j(j, k) {
			if (j.eatSpace())return null;
			if (k.chain)return h(j, k, k.chain, k.style, k.tail);
			if (j.match(/^\-?[\d\.]/, !1) && j.match(/^(\-?(\d*\.\d+(e[+-]?\d+)?|\d+\.\d*)|0x[\da-fA-F]+|0b[01]+|\d+(e[+-]?\d+)?)/))return "number";
			if (j.match(/^<<(?=\w)/))return j.eatWhile(/\w/), i(j, k, j.current().substr(2));
			if (j.sol() && j.match(/^\=item(?!\w)/))return i(j, k, "=cut");
			var l = j.next();
			if ('"' == l || "'" == l) {
				if (c(j, 3) == "<<" + l) {
					var m = j.pos;
					j.eatWhile(/\w/);
					var n = j.current().substr(1);
					if (n && j.eat(l))return i(j, k, n);
					j.pos = m
				}
				return h(j, k, [l], "string")
			}
			if ("q" == l) {
				var o = b(j, -2);
				if (!o || !/\w/.test(o))if (o = b(j, 0), "x" == o) {
					if (o = b(j, 1), "(" == o)return e(j, 2), h(j, k, [")"], f, g);
					if ("[" == o)return e(j, 2), h(j, k, ["]"], f, g);
					if ("{" == o)return e(j, 2), h(j, k, ["}"], f, g);
					if ("<" == o)return e(j, 2), h(j, k, [">"], f, g);
					if (/[\^'"!~\/]/.test(o))return e(j, 1), h(j, k, [j.eat(o)], f, g)
				} else if ("q" == o) {
					if (o = b(j, 1), "(" == o)return e(j, 2), h(j, k, [")"], "string");
					if ("[" == o)return e(j, 2), h(j, k, ["]"], "string");
					if ("{" == o)return e(j, 2), h(j, k, ["}"], "string");
					if ("<" == o)return e(j, 2), h(j, k, [">"], "string");
					if (/[\^'"!~\/]/.test(o))return e(j, 1), h(j, k, [j.eat(o)], "string")
				} else if ("w" == o) {
					if (o = b(j, 1), "(" == o)return e(j, 2), h(j, k, [")"], "bracket");
					if ("[" == o)return e(j, 2), h(j, k, ["]"], "bracket");
					if ("{" == o)return e(j, 2), h(j, k, ["}"], "bracket");
					if ("<" == o)return e(j, 2), h(j, k, [">"], "bracket");
					if (/[\^'"!~\/]/.test(o))return e(j, 1), h(j, k, [j.eat(o)], "bracket")
				} else if ("r" == o) {
					if (o = b(j, 1), "(" == o)return e(j, 2), h(j, k, [")"], f, g);
					if ("[" == o)return e(j, 2), h(j, k, ["]"], f, g);
					if ("{" == o)return e(j, 2), h(j, k, ["}"], f, g);
					if ("<" == o)return e(j, 2), h(j, k, [">"], f, g);
					if (/[\^'"!~\/]/.test(o))return e(j, 1), h(j, k, [j.eat(o)], f, g)
				} else if (/[\^'"!~\/(\[{<]/.test(o)) {
					if ("(" == o)return e(j, 1), h(j, k, [")"], "string");
					if ("[" == o)return e(j, 1), h(j, k, ["]"], "string");
					if ("{" == o)return e(j, 1), h(j, k, ["}"], "string");
					if ("<" == o)return e(j, 1), h(j, k, [">"], "string");
					if (/[\^'"!~\/]/.test(o))return h(j, k, [j.eat(o)], "string")
				}
			}
			if ("m" == l) {
				var o = b(j, -2);
				if ((!o || !/\w/.test(o)) && (o = j.eat(/[(\[{<\^'"!~\/]/))) {
					if (/[\^'"!~\/]/.test(o))return h(j, k, [o], f, g);
					if ("(" == o)return h(j, k, [")"], f, g);
					if ("[" == o)return h(j, k, ["]"], f, g);
					if ("{" == o)return h(j, k, ["}"], f, g);
					if ("<" == o)return h(j, k, [">"], f, g)
				}
			}
			if ("s" == l) {
				var o = /[\/>\]})\w]/.test(b(j, -2));
				if (!o && (o = j.eat(/[(\[{<\^'"!~\/]/)))return "[" == o ? h(j, k, ["]", "]"], f, g) : "{" == o ? h(j, k, ["}", "}"], f, g) : "<" == o ? h(j, k, [">", ">"], f, g) : "(" == o ? h(j, k, [")", ")"], f, g) : h(j, k, [o, o], f, g)
			}
			if ("y" == l) {
				var o = /[\/>\]})\w]/.test(b(j, -2));
				if (!o && (o = j.eat(/[(\[{<\^'"!~\/]/)))return "[" == o ? h(j, k, ["]", "]"], f, g) : "{" == o ? h(j, k, ["}", "}"], f, g) : "<" == o ? h(j, k, [">", ">"], f, g) : "(" == o ? h(j, k, [")", ")"], f, g) : h(j, k, [o, o], f, g)
			}
			if ("t" == l) {
				var o = /[\/>\]})\w]/.test(b(j, -2));
				if (!o && (o = j.eat("r"), o && (o = j.eat(/[(\[{<\^'"!~\/]/))))return "[" == o ? h(j, k, ["]", "]"], f, g) : "{" == o ? h(j, k, ["}", "}"], f, g) : "<" == o ? h(j, k, [">", ">"], f, g) : "(" == o ? h(j, k, [")", ")"], f, g) : h(j, k, [o, o], f, g)
			}
			if ("`" == l)return h(j, k, [l], "variable-2");
			if ("/" == l)return /~\s*$/.test(c(j)) ? h(j, k, [l], f, g) : "operator";
			if ("$" == l) {
				var m = j.pos;
				if (j.eatWhile(/\d/) || j.eat("{") && j.eatWhile(/\d/) && j.eat("}"))return "variable-2";
				j.pos = m
			}
			if (/[$@%]/.test(l)) {
				var m = j.pos;
				if (j.eat("^") && j.eat(/[A-Z]/) || !/[@$%&]/.test(b(j, -2)) && j.eat(/[=|\\\-#?@;:&`~\^!\[\]*'"$+.,\/<>()]/)) {
					var o = j.current();
					if (a[o])return "variable-2"
				}
				j.pos = m
			}
			if (/[$@%&]/.test(l) && (j.eatWhile(/[\w$\[\]]/) || j.eat("{") && j.eatWhile(/[\w$\[\]]/) && j.eat("}"))) {
				var o = j.current();
				return a[o] ? "variable-2" : "variable"
			}
			if ("#" == l && "$" != b(j, -2))return j.skipToEnd(), "comment";
			if (/[:+\-\^*$&%@=<>!?|\/~\.]/.test(l)) {
				var m = j.pos;
				if (j.eatWhile(/[:+\-\^*$&%@=<>!?|\/~\.]/), a[j.current()])return "operator";
				j.pos = m
			}
			if ("_" == l && 1 == j.pos) {
				if ("_END__" == d(j, 6))return h(j, k, ["\0"], "comment");
				if ("_DATA__" == d(j, 7))return h(j, k, ["\0"], "variable-2");
				if ("_C__" == d(j, 7))return h(j, k, ["\0"], "string")
			}
			if (/\w/.test(l)) {
				var m = j.pos;
				if ("{" == b(j, -2) && ("}" == b(j, 0) || j.eatWhile(/\w/) && "}" == b(j, 0)))return "string";
				j.pos = m
			}
			if (/[A-Z]/.test(l)) {
				var p = b(j, -2), m = j.pos;
				if (j.eatWhile(/[A-Z_]/), !/[\da-z]/.test(b(j, 0))) {
					var o = a[j.current()];
					return o ? (o[1] && (o = o[0]), ":" != p ? 1 == o ? "keyword" : 2 == o ? "def" : 3 == o ? "atom" : 4 == o ? "operator" : 5 == o ? "variable-2" : "meta" : "meta") : "meta"
				}
				j.pos = m
			}
			if (/[a-zA-Z_]/.test(l)) {
				var p = b(j, -2);
				j.eatWhile(/\w/);
				var o = a[j.current()];
				return o ? (o[1] && (o = o[0]), ":" != p ? 1 == o ? "keyword" : 2 == o ? "def" : 3 == o ? "atom" : 4 == o ? "operator" : 5 == o ? "variable-2" : "meta" : "meta") : "meta"
			}
			return null
		}

		var a = {
			"->":                          4,
			"++":                          4,
			"--":                          4,
			"**":                          4,
			"=~":                          4,
			"!~":                          4,
			"*":                           4,
			"/":                           4,
			"%":                           4,
			x:                             4,
			"+":                           4,
			"-":                           4,
			".":                           4,
			"<<":                          4,
			">>":                          4,
			"<":                           4,
			">":                           4,
			"<=":                          4,
			">=":                          4,
			lt:                            4,
			gt:                            4,
			le:                            4,
			ge:                            4,
			"==":                          4,
			"!=":                          4,
			"<=>":                         4,
			eq:                            4,
			ne:                            4,
			cmp:                           4,
			"~~":                          4,
			"&":                           4,
			"|":                           4,
			"^":                           4,
			"&&":                          4,
			"||":                          4,
			"//":                          4,
			"..":                          4,
			"...":                         4,
			"?":                           4,
			":":                           4,
			"=":                           4,
			"+=":                          4,
			"-=":                          4,
			"*=":                          4,
			",":                           4,
			"=>":                          4,
			"::":                          4,
			not:                           4,
			and:                           4,
			or:                            4,
			xor:                           4,
			BEGIN:                         [5, 1],
			END:                           [5, 1],
			PRINT:                         [5, 1],
			PRINTF:                        [5, 1],
			GETC:                          [5, 1],
			READ:                          [5, 1],
			READLINE:                      [5, 1],
			DESTROY:                       [5, 1],
			TIE:                           [5, 1],
			TIEHANDLE:                     [5, 1],
			UNTIE:                         [5, 1],
			STDIN:                         5,
			STDIN_TOP:                     5,
			STDOUT:                        5,
			STDOUT_TOP:                    5,
			STDERR:                        5,
			STDERR_TOP:                    5,
			$ARG:                          5,
			$_:                            5,
			"@ARG":                        5,
			"@_":                          5,
			$LIST_SEPARATOR:               5,
			'$"':                          5,
			$PROCESS_ID:                   5,
			$PID:                          5,
			$$:                            5,
			$REAL_GROUP_ID:                5,
			$GID:                          5,
			"$(":                          5,
			$EFFECTIVE_GROUP_ID:           5,
			$EGID:                         5,
			"$)":                          5,
			$PROGRAM_NAME:                 5,
			$0:                            5,
			$SUBSCRIPT_SEPARATOR:          5,
			$SUBSEP:                       5,
			"$;":                          5,
			$REAL_USER_ID:                 5,
			$UID:                          5,
			"$<":                          5,
			$EFFECTIVE_USER_ID:            5,
			$EUID:                         5,
			"$>":                          5,
			$a:                            5,
			$b:                            5,
			$COMPILING:                    5,
			"$^C":                         5,
			$DEBUGGING:                    5,
			"$^D":                         5,
			"${^ENCODING}":                5,
			$ENV:                          5,
			"%ENV":                        5,
			$SYSTEM_FD_MAX:                5,
			"$^F":                         5,
			"@F":                          5,
			"${^GLOBAL_PHASE}":            5,
			"$^H":                         5,
			"%^H":                         5,
			"@INC":                        5,
			"%INC":                        5,
			$INPLACE_EDIT:                 5,
			"$^I":                         5,
			"$^M":                         5,
			$OSNAME:                       5,
			"$^O":                         5,
			"${^OPEN}":                    5,
			$PERLDB:                       5,
			"$^P":                         5,
			$SIG:                          5,
			"%SIG":                        5,
			$BASETIME:                     5,
			"$^T":                         5,
			"${^TAINT}":                   5,
			"${^UNICODE}":                 5,
			"${^UTF8CACHE}":               5,
			"${^UTF8LOCALE}":              5,
			$PERL_VERSION:                 5,
			"$^V":                         5,
			"${^WIN32_SLOPPY_STAT}":       5,
			$EXECUTABLE_NAME:              5,
			"$^X":                         5,
			$1:                            5,
			$MATCH:                        5,
			"$&":                          5,
			"${^MATCH}":                   5,
			$PREMATCH:                     5,
			"$`":                          5,
			"${^PREMATCH}":                5,
			$POSTMATCH:                    5,
			"$'":                          5,
			"${^POSTMATCH}":               5,
			$LAST_PAREN_MATCH:             5,
			"$+":                          5,
			$LAST_SUBMATCH_RESULT:         5,
			"$^N":                         5,
			"@LAST_MATCH_END":             5,
			"@+":                          5,
			"%LAST_PAREN_MATCH":           5,
			"%+":                          5,
			"@LAST_MATCH_START":           5,
			"@-":                          5,
			"%LAST_MATCH_START":           5,
			"%-":                          5,
			$LAST_REGEXP_CODE_RESULT:      5,
			"$^R":                         5,
			"${^RE_DEBUG_FLAGS}":          5,
			"${^RE_TRIE_MAXBUF}":          5,
			$ARGV:                         5,
			"@ARGV":                       5,
			ARGV:                          5,
			ARGVOUT:                       5,
			$OUTPUT_FIELD_SEPARATOR:       5,
			$OFS:                          5,
			"$,":                          5,
			$INPUT_LINE_NUMBER:            5,
			$NR:                           5,
			"$.":                          5,
			$INPUT_RECORD_SEPARATOR:       5,
			$RS:                           5,
			"$/":                          5,
			$OUTPUT_RECORD_SEPARATOR:      5,
			$ORS:                          5,
			"$\\":                         5,
			$OUTPUT_AUTOFLUSH:             5,
			"$|":                          5,
			$ACCUMULATOR:                  5,
			"$^A":                         5,
			$FORMAT_FORMFEED:              5,
			"$^L":                         5,
			$FORMAT_PAGE_NUMBER:           5,
			"$%":                          5,
			$FORMAT_LINES_LEFT:            5,
			"$-":                          5,
			$FORMAT_LINE_BREAK_CHARACTERS: 5,
			"$:":                          5,
			$FORMAT_LINES_PER_PAGE:        5,
			"$=":                          5,
			$FORMAT_TOP_NAME:              5,
			"$^":                          5,
			$FORMAT_NAME:                  5,
			"$~":                          5,
			"${^CHILD_ERROR_NATIVE}":      5,
			$EXTENDED_OS_ERROR:            5,
			"$^E":                         5,
			$EXCEPTIONS_BEING_CAUGHT:      5,
			"$^S":                         5,
			$WARNING:                      5,
			"$^W":                         5,
			"${^WARNING_BITS}":            5,
			$OS_ERROR:                     5,
			$ERRNO:                        5,
			"$!":                          5,
			"%OS_ERROR":                   5,
			"%ERRNO":                      5,
			"%!":                          5,
			$CHILD_ERROR:                  5,
			"$?":                          5,
			$EVAL_ERROR:                   5,
			"$@":                          5,
			$OFMT:                         5,
			"$#":                          5,
			"$*":                          5,
			$ARRAY_BASE:                   5,
			"$[":                          5,
			$OLD_PERL_VERSION:             5,
			"$]":                          5,
			"if":                          [1, 1],
			elsif:                         [1, 1],
			"else":                        [1, 1],
			"while":                       [1, 1],
			unless:                        [1, 1],
			"for":                         [1, 1],
			foreach:                       [1, 1],
			abs:                           1,
			accept:                        1,
			alarm:                         1,
			atan2:                         1,
			bind:                          1,
			binmode:                       1,
			bless:                         1,
			bootstrap:                     1,
			"break":                       1,
			caller:                        1,
			chdir:                         1,
			chmod:                         1,
			chomp:                         1,
			chop:                          1,
			chown:                         1,
			chr:                           1,
			chroot:                        1,
			close:                         1,
			closedir:                      1,
			connect:                       1,
			"continue":                    [1, 1],
			cos:                           1,
			crypt:                         1,
			dbmclose:                      1,
			dbmopen:                       1,
			"default":                     1,
			defined:                       1,
			"delete":                      1,
			die:                           1,
			"do":                          1,
			dump:                          1,
			each:                          1,
			endgrent:                      1,
			endhostent:                    1,
			endnetent:                     1,
			endprotoent:                   1,
			endpwent:                      1,
			endservent:                    1,
			eof:                           1,
			eval:                          1,
			exec:                          1,
			exists:                        1,
			exit:                          1,
			exp:                           1,
			fcntl:                         1,
			fileno:                        1,
			flock:                         1,
			fork:                          1,
			format:                        1,
			formline:                      1,
			getc:                          1,
			getgrent:                      1,
			getgrgid:                      1,
			getgrnam:                      1,
			gethostbyaddr:                 1,
			gethostbyname:                 1,
			gethostent:                    1,
			getlogin:                      1,
			getnetbyaddr:                  1,
			getnetbyname:                  1,
			getnetent:                     1,
			getpeername:                   1,
			getpgrp:                       1,
			getppid:                       1,
			getpriority:                   1,
			getprotobyname:                1,
			getprotobynumber:              1,
			getprotoent:                   1,
			getpwent:                      1,
			getpwnam:                      1,
			getpwuid:                      1,
			getservbyname:                 1,
			getservbyport:                 1,
			getservent:                    1,
			getsockname:                   1,
			getsockopt:                    1,
			given:                         1,
			glob:                          1,
			gmtime:                        1,
			"goto":                        1,
			grep:                          1,
			hex:                           1,
			"import":                      1,
			index:                         1,
			"int":                         1,
			ioctl:                         1,
			join:                          1,
			keys:                          1,
			kill:                          1,
			last:                          1,
			lc:                            1,
			lcfirst:                       1,
			length:                        1,
			link:                          1,
			listen:                        1,
			local:                         2,
			localtime:                     1,
			lock:                          1,
			log:                           1,
			lstat:                         1,
			m:                             null,
			map:                           1,
			mkdir:                         1,
			msgctl:                        1,
			msgget:                        1,
			msgrcv:                        1,
			msgsnd:                        1,
			my:                            2,
			"new":                         1,
			next:                          1,
			no:                            1,
			oct:                           1,
			open:                          1,
			opendir:                       1,
			ord:                           1,
			our:                           2,
			pack:                          1,
			"package":                     1,
			pipe:                          1,
			pop:                           1,
			pos:                           1,
			print:                         1,
			printf:                        1,
			prototype:                     1,
			push:                          1,
			q:                             null,
			qq:                            null,
			qr:                            null,
			quotemeta:                     null,
			qw:                            null,
			qx:                            null,
			rand:                          1,
			read:                          1,
			readdir:                       1,
			readline:                      1,
			readlink:                      1,
			readpipe:                      1,
			recv:                          1,
			redo:                          1,
			ref:                           1,
			rename:                        1,
			require:                       1,
			reset:                         1,
			"return":                      1,
			reverse:                       1,
			rewinddir:                     1,
			rindex:                        1,
			rmdir:                         1,
			s:                             null,
			say:                           1,
			scalar:                        1,
			seek:                          1,
			seekdir:                       1,
			select:                        1,
			semctl:                        1,
			semget:                        1,
			semop:                         1,
			send:                          1,
			setgrent:                      1,
			sethostent:                    1,
			setnetent:                     1,
			setpgrp:                       1,
			setpriority:                   1,
			setprotoent:                   1,
			setpwent:                      1,
			setservent:                    1,
			setsockopt:                    1,
			shift:                         1,
			shmctl:                        1,
			shmget:                        1,
			shmread:                       1,
			shmwrite:                      1,
			shutdown:                      1,
			sin:                           1,
			sleep:                         1,
			socket:                        1,
			socketpair:                    1,
			sort:                          1,
			splice:                        1,
			split:                         1,
			sprintf:                       1,
			sqrt:                          1,
			srand:                         1,
			stat:                          1,
			state:                         1,
			study:                         1,
			sub:                           1,
			substr:                        1,
			symlink:                       1,
			syscall:                       1,
			sysopen:                       1,
			sysread:                       1,
			sysseek:                       1,
			system:                        1,
			syswrite:                      1,
			tell:                          1,
			telldir:                       1,
			tie:                           1,
			tied:                          1,
			time:                          1,
			times:                         1,
			tr:                            null,
			truncate:                      1,
			uc:                            1,
			ucfirst:                       1,
			umask:                         1,
			undef:                         1,
			unlink:                        1,
			unpack:                        1,
			unshift:                       1,
			untie:                         1,
			use:                           1,
			utime:                         1,
			values:                        1,
			vec:                           1,
			wait:                          1,
			waitpid:                       1,
			wantarray:                     1,
			warn:                          1,
			when:                          1,
			write:                         1,
			y:                             null
		}, f = "string-2", g = /[goseximacplud]/;
		return {
			startState:       function () {
				return {tokenize: j, chain: null, style: null, tail: null}
			}, token:         function (a, b) {
				return (b.tokenize || j)(a, b)
			}, electricChars: "{}"
		}
	}), a.registerHelper("wordChars", "perl", /[\\w$]/), a.defineMIME("text/x-perl", "perl")
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror"), require("../htmlmixed/htmlmixed"), require("../clike/clike")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../htmlmixed/htmlmixed", "../clike/clike"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a) {
		for (var b = {}, c = a.split(" "), d = 0; d < c.length; ++d)b[c[d]] = !0;
		return b
	}

	function c(a) {
		return function (b, c) {
			return b.match(a) ? c.tokenize = null : b.skipToEnd(), "string"
		}
	}

	function d(a) {
		return 0 == a.length ? e : function (b, c) {
			for (var f = a[0], g = 0; g < f.length; g++)if (b.match(f[g][0]))return c.tokenize = d(a.slice(1)), f[g][1];
			return c.tokenize = e, "string"
		}
	}

	function e(a, b) {
		var e, c = !1, f = !1;
		if ('"' == a.current())return "string";
		if (a.match("${", !1) || a.match("{$", !1))return b.tokenize = null, "string";
		if (a.match(/\$[a-zA-Z_][a-zA-Z0-9_]*/))return a.match("[", !1) && (b.tokenize = d([[["[", null]], [[/\d[\w\.]*/, "number"], [/\$[a-zA-Z_][a-zA-Z0-9_]*/, "variable-2"], [/[\w\$]+/, "variable"]], [["]", null]]])), a.match(/\-\>\w/, !1) && (b.tokenize = d([[["->", null]], [[/[\w]+/, "variable"]]])), "variable-2";
		for (; !(a.eol() || a.match("{$", !1) || a.match(/(\$[a-zA-Z_][a-zA-Z0-9_]*|\$\{)/, !1) && !c);) {
			if (e = a.next(), !c && '"' == e) {
				f = !0;
				break
			}
			c = !c && "\\" == e
		}
		return f && (b.tokenize = null, b.phpEncapsStack.pop()), "string"
	}

	var f = "abstract and array as break case catch class clone const continue declare default do else elseif enddeclare endfor endforeach endif endswitch endwhile extends final for foreach function global goto if implements interface instanceof namespace new or private protected public static switch throw trait try use var while xor die echo empty exit eval include include_once isset list require require_once return print unset __halt_compiler self static parent yield insteadof finally", g = "true false null TRUE FALSE NULL __CLASS__ __DIR__ __FILE__ __LINE__ __METHOD__ __FUNCTION__ __NAMESPACE__ __TRAIT__", h = "func_num_args func_get_arg func_get_args strlen strcmp strncmp strcasecmp strncasecmp each error_reporting define defined trigger_error user_error set_error_handler restore_error_handler get_declared_classes get_loaded_extensions extension_loaded get_extension_funcs debug_backtrace constant bin2hex hex2bin sleep usleep time mktime gmmktime strftime gmstrftime strtotime date gmdate getdate localtime checkdate flush wordwrap htmlspecialchars htmlentities html_entity_decode md5 md5_file crc32 getimagesize image_type_to_mime_type phpinfo phpversion phpcredits strnatcmp strnatcasecmp substr_count strspn strcspn strtok strtoupper strtolower strpos strrpos strrev hebrev hebrevc nl2br basename dirname pathinfo stripslashes stripcslashes strstr stristr strrchr str_shuffle str_word_count strcoll substr substr_replace quotemeta ucfirst ucwords strtr addslashes addcslashes rtrim str_replace str_repeat count_chars chunk_split trim ltrim strip_tags similar_text explode implode setlocale localeconv parse_str str_pad chop strchr sprintf printf vprintf vsprintf sscanf fscanf parse_url urlencode urldecode rawurlencode rawurldecode readlink linkinfo link unlink exec system escapeshellcmd escapeshellarg passthru shell_exec proc_open proc_close rand srand getrandmax mt_rand mt_srand mt_getrandmax base64_decode base64_encode abs ceil floor round is_finite is_nan is_infinite bindec hexdec octdec decbin decoct dechex base_convert number_format fmod ip2long long2ip getenv putenv getopt microtime gettimeofday getrusage uniqid quoted_printable_decode set_time_limit get_cfg_var magic_quotes_runtime set_magic_quotes_runtime get_magic_quotes_gpc get_magic_quotes_runtime import_request_variables error_log serialize unserialize memory_get_usage var_dump var_export debug_zval_dump print_r highlight_file show_source highlight_string ini_get ini_get_all ini_set ini_alter ini_restore get_include_path set_include_path restore_include_path setcookie header headers_sent connection_aborted connection_status ignore_user_abort parse_ini_file is_uploaded_file move_uploaded_file intval floatval doubleval strval gettype settype is_null is_resource is_bool is_long is_float is_int is_integer is_double is_real is_numeric is_string is_array is_object is_scalar ereg ereg_replace eregi eregi_replace split spliti join sql_regcase dl pclose popen readfile rewind rmdir umask fclose feof fgetc fgets fgetss fread fopen fpassthru ftruncate fstat fseek ftell fflush fwrite fputs mkdir rename copy tempnam tmpfile file file_get_contents stream_select stream_context_create stream_context_set_params stream_context_set_option stream_context_get_options stream_filter_prepend stream_filter_append fgetcsv flock get_meta_tags stream_set_write_buffer set_file_buffer set_socket_blocking stream_set_blocking socket_set_blocking stream_get_meta_data stream_register_wrapper stream_wrapper_register stream_set_timeout socket_set_timeout socket_get_status realpath fnmatch fsockopen pfsockopen pack unpack get_browser crypt opendir closedir chdir getcwd rewinddir readdir dir glob fileatime filectime filegroup fileinode filemtime fileowner fileperms filesize filetype file_exists is_writable is_writeable is_readable is_executable is_file is_dir is_link stat lstat chown touch clearstatcache mail ob_start ob_flush ob_clean ob_end_flush ob_end_clean ob_get_flush ob_get_clean ob_get_length ob_get_level ob_get_status ob_get_contents ob_implicit_flush ob_list_handlers ksort krsort natsort natcasesort asort arsort sort rsort usort uasort uksort shuffle array_walk count end prev next reset current key min max in_array array_search extract compact array_fill range array_multisort array_push array_pop array_shift array_unshift array_splice array_slice array_merge array_merge_recursive array_keys array_values array_count_values array_reverse array_reduce array_pad array_flip array_change_key_case array_rand array_unique array_intersect array_intersect_assoc array_diff array_diff_assoc array_sum array_filter array_map array_chunk array_key_exists pos sizeof key_exists assert assert_options version_compare ftok str_rot13 aggregate session_name session_module_name session_save_path session_id session_regenerate_id session_decode session_register session_unregister session_is_registered session_encode session_start session_destroy session_unset session_set_save_handler session_cache_limiter session_cache_expire session_set_cookie_params session_get_cookie_params session_write_close preg_match preg_match_all preg_replace preg_replace_callback preg_split preg_quote preg_grep overload ctype_alnum ctype_alpha ctype_cntrl ctype_digit ctype_lower ctype_graph ctype_print ctype_punct ctype_space ctype_upper ctype_xdigit virtual apache_request_headers apache_note apache_lookup_uri apache_child_terminate apache_setenv apache_response_headers apache_get_version getallheaders mysql_connect mysql_pconnect mysql_close mysql_select_db mysql_create_db mysql_drop_db mysql_query mysql_unbuffered_query mysql_db_query mysql_list_dbs mysql_list_tables mysql_list_fields mysql_list_processes mysql_error mysql_errno mysql_affected_rows mysql_insert_id mysql_result mysql_num_rows mysql_num_fields mysql_fetch_row mysql_fetch_array mysql_fetch_assoc mysql_fetch_object mysql_data_seek mysql_fetch_lengths mysql_fetch_field mysql_field_seek mysql_free_result mysql_field_name mysql_field_table mysql_field_len mysql_field_type mysql_field_flags mysql_escape_string mysql_real_escape_string mysql_stat mysql_thread_id mysql_client_encoding mysql_get_client_info mysql_get_host_info mysql_get_proto_info mysql_get_server_info mysql_info mysql mysql_fieldname mysql_fieldtable mysql_fieldlen mysql_fieldtype mysql_fieldflags mysql_selectdb mysql_createdb mysql_dropdb mysql_freeresult mysql_numfields mysql_numrows mysql_listdbs mysql_listtables mysql_listfields mysql_db_name mysql_dbname mysql_tablename mysql_table_name pg_connect pg_pconnect pg_close pg_connection_status pg_connection_busy pg_connection_reset pg_host pg_dbname pg_port pg_tty pg_options pg_ping pg_query pg_send_query pg_cancel_query pg_fetch_result pg_fetch_row pg_fetch_assoc pg_fetch_array pg_fetch_object pg_fetch_all pg_affected_rows pg_get_result pg_result_seek pg_result_status pg_free_result pg_last_oid pg_num_rows pg_num_fields pg_field_name pg_field_num pg_field_size pg_field_type pg_field_prtlen pg_field_is_null pg_get_notify pg_get_pid pg_result_error pg_last_error pg_last_notice pg_put_line pg_end_copy pg_copy_to pg_copy_from pg_trace pg_untrace pg_lo_create pg_lo_unlink pg_lo_open pg_lo_close pg_lo_read pg_lo_write pg_lo_read_all pg_lo_import pg_lo_export pg_lo_seek pg_lo_tell pg_escape_string pg_escape_bytea pg_unescape_bytea pg_client_encoding pg_set_client_encoding pg_meta_data pg_convert pg_insert pg_update pg_delete pg_select pg_exec pg_getlastoid pg_cmdtuples pg_errormessage pg_numrows pg_numfields pg_fieldname pg_fieldsize pg_fieldtype pg_fieldnum pg_fieldprtlen pg_fieldisnull pg_freeresult pg_result pg_loreadall pg_locreate pg_lounlink pg_loopen pg_loclose pg_loread pg_lowrite pg_loimport pg_loexport http_response_code get_declared_traits getimagesizefromstring socket_import_stream stream_set_chunk_size trait_exists header_register_callback class_uses session_status session_register_shutdown echo print global static exit array empty eval isset unset die include require include_once require_once";
	a.registerHelper("hintWords", "php", [f, g, h].join(" ").split(" ")), a.registerHelper("wordChars", "php", /[\\w$]/);
	var i = {
		name:             "clike",
		helperType:       "php",
		keywords:         b(f),
		blockKeywords:    b("catch do else elseif for foreach if switch try while finally"),
		atoms:            b(g),
		builtin:          b(h),
		multiLineStrings: !0,
		hooks:            {
			$:      function (a) {
				return a.eatWhile(/[\w\$_]/), "variable-2"
			}, "<": function (a, b) {
				return a.match(/<</) ? (a.eatWhile(/[\w\.]/), b.tokenize = c(a.current().slice(3)), b.tokenize(a, b)) : !1
			}, "#": function (a) {
				for (; !a.eol() && !a.match("?>", !1);)a.next();
				return "comment"
			}, "/": function (a) {
				if (a.eat("/")) {
					for (; !a.eol() && !a.match("?>", !1);)a.next();
					return "comment"
				}
				return !1
			}, '"': function (a, b) {
				return b.phpEncapsStack || (b.phpEncapsStack = []), b.phpEncapsStack.push(0), b.tokenize = e, b.tokenize(a, b)
			}, "{": function (a, b) {
				return b.phpEncapsStack && b.phpEncapsStack.length > 0 && b.phpEncapsStack[b.phpEncapsStack.length - 1]++, !1
			}, "}": function (a, b) {
				return b.phpEncapsStack && b.phpEncapsStack.length > 0 && 0 == --b.phpEncapsStack[b.phpEncapsStack.length - 1] && (b.tokenize = e), !1
			}
		}
	};
	a.defineMode("php", function (b, c) {
		function f(a, b) {
			var c = b.curMode == e;
			if (a.sol() && b.pending && '"' != b.pending && "'" != b.pending && (b.pending = null), c)return c && null == b.php.tokenize && a.match("?>") ? (b.curMode = d, b.curState = b.html, "meta") : e.token(a, b.curState);
			if (a.match(/^<\?\w*/))return b.curMode = e, b.curState = b.php, "meta";
			if ('"' == b.pending || "'" == b.pending) {
				for (; !a.eol() && a.next() != b.pending;);
				var f = "string"
			} else if (b.pending && a.pos < b.pending.end) {
				a.pos = b.pending.end;
				var f = b.pending.style
			} else var f = d.token(a, b.curState);
			b.pending && (b.pending = null);
			var i, g = a.current(), h = g.search(/<\?/);
			return -1 != h && (b.pending = "string" == f && (i = g.match(/[\'\"]$/)) && !/\?>/.test(g) ? i[0] : {
				end:   a.pos,
				style: f
			}, a.backUp(g.length - h)), f
		}

		var d = a.getMode(b, "text/html"), e = a.getMode(b, i);
		return {
			startState:           function () {
				var b = a.startState(d), f = a.startState(e);
				return {html: b, php: f, curMode: c.startOpen ? e : d, curState: c.startOpen ? f : b, pending: null}
			}, copyState:         function (b) {
				var i, c = b.html, f = a.copyState(d, c), g = b.php, h = a.copyState(e, g);
				return i = b.curMode == d ? f : h, {html: f, php: h, curMode: b.curMode, curState: i, pending: b.pending}
			}, token:             f, indent: function (a, b) {
				return a.curMode != e && /^\s*<\//.test(b) || a.curMode == e && /^\?>/.test(b) ? d.indent(a.html, b) : a.curMode.indent(a.curState, b)
			}, blockCommentStart: "/*", blockCommentEnd: "*/", lineComment: "//", innerMode: function (a) {
				return {state: a.curState, mode: a.curMode}
			}
		}
	}, "htmlmixed", "clike"), a.defineMIME("application/x-httpd-php", "php"), a.defineMIME("application/x-httpd-php-open", {
		name:      "php",
		startOpen: !0
	}), a.defineMIME("text/x-php", i)
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("sass", function (a) {
		var b = function (a) {
			return new RegExp("^" + a.join("|"))
		}, c = ["true", "false", "null", "auto"], d = new RegExp("^" + c.join("|")), e = ["\\(", "\\)", "=", ">", "<", "==", ">=", "<=", "\\+", "-", "\\!=", "/", "\\*", "%", "and", "or", "not"], f = b(e), g = /^::?[\w\-]+/, h = function (a, b) {
			var c = a.peek();
			return ")" === c ? (a.next(), b.tokenizer = n, "operator") : "(" === c ? (a.next(), a.eatSpace(), "operator") : "'" === c || '"' === c ? (b.tokenizer = j(a.next()), "string") : (b.tokenizer = j(")", !1), "string")
		}, i = function (a, b) {
			return a.skipTo("*/") ? (a.next(), a.next(), b.tokenizer = n) : a.next(), "comment"
		}, j = function (a, b) {
			function c(d, e) {
				var f = d.next(), g = d.peek(), h = d.string.charAt(d.pos - 2), i = "\\" !== f && g === a || f === a && "\\" !== h;
				return i ? (f !== a && b && d.next(), e.tokenizer = n, "string") : "#" === f && "{" === g ? (e.tokenizer = k(c), d.next(), "operator") : "string"
			}

			return null == b && (b = !0), c
		}, k = function (a) {
			return function (b, c) {
				return "}" === b.peek() ? (b.next(), c.tokenizer = a, "operator") : n(b, c)
			}
		}, l = function (b) {
			if (0 == b.indentCount) {
				b.indentCount++;
				var c = b.scopes[0].offset, d = c + a.indentUnit;
				b.scopes.unshift({offset: d})
			}
		}, m = function (a) {
			1 != a.scopes.length && a.scopes.shift()
		}, n = function (a, b) {
			var c = a.peek();
			if (a.match("//"))return a.skipToEnd(), "comment";
			if (a.match("/*"))return b.tokenizer = i, b.tokenizer(a, b);
			if (a.match("#{"))return b.tokenizer = k(n), "operator";
			if ("." === c)return a.next(), a.match(/^[\w-]+/) ? (l(b), "atom") : "#" === a.peek() ? (l(b), "atom") : "operator";
			if ("#" === c) {
				if (a.next(), a.match(/[0-9a-fA-F]{6}|[0-9a-fA-F]{3}/))return "number";
				if (a.match(/^[\w-]+/))return l(b), "atom";
				if ("#" === a.peek())return l(b), "atom"
			}
			return a.match(/^-?[0-9\.]+/) ? "number" : a.match(/^(px|em|in)\b/) ? "unit" : a.match(d) ? "keyword" : a.match(/^url/) && "(" === a.peek() ? (b.tokenizer = h, "atom") : "$" === c ? (a.next(), a.eatWhile(/[\w-]/), ":" === a.peek() ? (a.next(), "variable-2") : "variable-3") : "!" === c ? (a.next(), a.match(/^[\w]+/) ? "keyword" : "operator") : "=" === c ? (a.next(), a.match(/^[\w-]+/) ? (l(b), "meta") : "operator") : "+" === c ? (a.next(), a.match(/^[\w-]+/) ? "variable-3" : "operator") : a.match(/^@(else if|if|media|else|for|each|while|mixin|function)/) ? (l(b), "meta") : "@" === c ? (a.next(), a.eatWhile(/[\w-]/), "meta") : '"' === c || "'" === c ? (a.next(), b.tokenizer = j(c), "string") : ":" == c && a.match(g) ? "keyword" : a.eatWhile(/[\w-&]/) ? ":" !== a.peek() || a.match(g, !1) ? "atom" : "property" : a.match(f) ? "operator" : (a.next(), null)
		}, o = function (b, c) {
			b.sol() && (c.indentCount = 0);
			var d = c.tokenizer(b, c), e = b.current();
			if ("@return" === e && m(c), "atom" === d && l(c), null !== d) {
				for (var f = b.pos - e.length, g = f + a.indentUnit * c.indentCount, h = [], i = 0; i < c.scopes.length; i++) {
					var j = c.scopes[i];
					j.offset <= g && h.push(j)
				}
				c.scopes = h
			}
			return d
		};
		return {
			startState: function () {
				return {tokenizer: n, scopes: [{offset: 0, type: "sass"}], definedVars: [], definedMixins: []}
			}, token:   function (a, b) {
				var c = o(a, b);
				return b.lastToken = {style: c, content: a.current()}, c
			}, indent:  function (a) {
				return a.scopes[0].offset
			}
		}
	}), a.defineMIME("text/x-sass", "sass")
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("sql", function (a, b) {
		function k(a, b) {
			var k = a.next();
			if (i[k]) {
				var n = i[k](a, b);
				if (n !== !1)return n
			}
			if (1 == h.hexNumber && ("0" == k && a.match(/^[xX][0-9a-fA-F]+/) || ("x" == k || "X" == k) && a.match(/^'[0-9a-fA-F]+'/)))return "number";
			if (1 == h.binaryNumber && (("b" == k || "B" == k) && a.match(/^'[01]+'/) || "0" == k && a.match(/^b[01]+/)))return "number";
			if (k.charCodeAt(0) > 47 && k.charCodeAt(0) < 58)return a.match(/^[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?/), 1 == h.decimallessFloat && a.eat("."), "number";
			if ("?" == k && (a.eatSpace() || a.eol() || a.eat(";")))return "variable-3";
			if ("'" == k || '"' == k && h.doubleQuote)return b.tokenize = l(k), b.tokenize(a, b);
			if ((1 == h.nCharCast && ("n" == k || "N" == k) || 1 == h.charsetCast && "_" == k && a.match(/[a-z][a-z0-9]*/i)) && ("'" == a.peek() || '"' == a.peek()))return "keyword";
			if (/^[\(\),\;\[\]]/.test(k))return null;
			if (h.commentSlashSlash && "/" == k && a.eat("/"))return a.skipToEnd(), "comment";
			if (h.commentHash && "#" == k || "-" == k && a.eat("-") && (!h.commentSpaceRequired || a.eat(" ")))return a.skipToEnd(), "comment";
			if ("/" == k && a.eat("*"))return b.tokenize = m, b.tokenize(a, b);
			if ("." != k) {
				if (g.test(k))return a.eatWhile(g), null;
				if ("{" == k && (a.match(/^( )*(d|D|t|T|ts|TS)( )*'[^']*'( )*}/) || a.match(/^( )*(d|D|t|T|ts|TS)( )*"[^"]*"( )*}/)))return "number";
				a.eatWhile(/^[_\w\d]/);
				var o = a.current().toLowerCase();
				return j.hasOwnProperty(o) && (a.match(/^( )+'[^']*'/) || a.match(/^( )+"[^"]*"/)) ? "number" : d.hasOwnProperty(o) ? "atom" : e.hasOwnProperty(o) ? "builtin" : f.hasOwnProperty(o) ? "keyword" : c.hasOwnProperty(o) ? "string-2" : null
			}
			return 1 == h.zerolessFloat && a.match(/^(?:\d+(?:e[+-]?\d+)?)/i) ? "number" : 1 == h.ODBCdotTable && a.match(/^[a-zA-Z_]+/) ? "variable-2" : void 0
		}

		function l(a) {
			return function (b, c) {
				for (var e, d = !1; null != (e = b.next());) {
					if (e == a && !d) {
						c.tokenize = k;
						break
					}
					d = !d && "\\" == e
				}
				return "string"
			}
		}

		function m(a, b) {
			for (; ;) {
				if (!a.skipTo("*")) {
					a.skipToEnd();
					break
				}
				if (a.next(), a.eat("/")) {
					b.tokenize = k;
					break
				}
			}
			return "comment"
		}

		function n(a, b, c) {
			b.context = {prev: b.context, indent: a.indentation(), col: a.column(), type: c}
		}

		function o(a) {
			a.indent = a.context.indent, a.context = a.context.prev
		}

		var c = b.client || {}, d = b.atoms || {
				"false": !0,
				"true":  !0,
				"null":  !0
			}, e = b.builtin || {}, f = b.keywords || {}, g = b.operatorChars || /^[*+\-%<>!=&|~^]/, h = b.support || {}, i = b.hooks || {}, j = b.dateSQL || {
				date: !0,
				time: !0,
				timestamp: !0
			};
		return {
			startState:        function () {
				return {tokenize: k, context: null}
			},
			token:             function (a, b) {
				if (a.sol() && b.context && null == b.context.align && (b.context.align = !1), a.eatSpace())return null;
				var c = b.tokenize(a, b);
				if ("comment" == c)return c;
				b.context && null == b.context.align && (b.context.align = !0);
				var d = a.current();
				return "(" == d ? n(a, b, ")") : "[" == d ? n(a, b, "]") : b.context && b.context.type == d && o(b), c
			},
			indent:            function (b, c) {
				var d = b.context;
				if (!d)return 0;
				var e = c.charAt(0) == d.type;
				return d.align ? d.col + (e ? 0 : 1) : d.indent + (e ? 0 : a.indentUnit)
			},
			blockCommentStart: "/*",
			blockCommentEnd:   "*/",
			lineComment:       h.commentSlashSlash ? "//" : h.commentHash ? "#" : null
		}
	}), function () {
		function b(a) {
			for (var b; null != (b = a.next());)if ("`" == b && !a.eat("`"))return "variable-2";
			return null
		}

		function c(a) {
			return a.eat("@") && (a.match(/^session\./), a.match(/^local\./), a.match(/^global\./)), a.eat("'") ? (a.match(/^.*'/), "variable-2") : a.eat('"') ? (a.match(/^.*"/), "variable-2") : a.eat("`") ? (a.match(/^.*`/), "variable-2") : a.match(/^[0-9a-zA-Z$\.\_]+/) ? "variable-2" : null
		}

		function d(a) {
			return a.eat("N") ? "atom" : a.match(/^[a-zA-Z.#!?]/) ? "variable-2" : null
		}

		function f(a) {
			for (var b = {}, c = a.split(" "), d = 0; d < c.length; ++d)b[c[d]] = !0;
			return b
		}

		var e = "alter and as asc between by count create delete desc distinct drop from having in insert into is join like not on or order select set table union update values where ";
		a.defineMIME("text/x-sql", {
			name:          "sql",
			keywords:      f(e + "begin"),
			builtin:       f("bool boolean bit blob enum long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text bigint int int1 int2 int3 int4 int8 integer float float4 float8 double char varbinary varchar varcharacter precision real date datetime year unsigned signed decimal numeric"),
			atoms:         f("false true null unknown"),
			operatorChars: /^[*+\-%<>!=]/,
			dateSQL:       f("date time timestamp"),
			support:       f("ODBCdotTable doubleQuote binaryNumber hexNumber")
		}), a.defineMIME("text/x-mssql", {
			name:          "sql",
			client:        f("charset clear connect edit ego exit go help nopager notee nowarning pager print prompt quit rehash source status system tee"),
			keywords:      f(e + "begin trigger proc view index for add constraint key primary foreign collate clustered nonclustered"),
			builtin:       f("bigint numeric bit smallint decimal smallmoney int tinyint money float real char varchar text nchar nvarchar ntext binary varbinary image cursor timestamp hierarchyid uniqueidentifier sql_variant xml table "),
			atoms:         f("false true null unknown"),
			operatorChars: /^[*+\-%<>!=]/,
			dateSQL:       f("date datetimeoffset datetime2 smalldatetime datetime time"),
			hooks:         {"@": c}
		}), a.defineMIME("text/x-mysql", {
			name:          "sql",
			client:        f("charset clear connect edit ego exit go help nopager notee nowarning pager print prompt quit rehash source status system tee"),
			keywords:      f(e + "accessible action add after algorithm all analyze asensitive at authors auto_increment autocommit avg avg_row_length before binary binlog both btree cache call cascade cascaded case catalog_name chain change changed character check checkpoint checksum class_origin client_statistics close coalesce code collate collation collations column columns comment commit committed completion concurrent condition connection consistent constraint contains continue contributors convert cross current current_date current_time current_timestamp current_user cursor data database databases day_hour day_microsecond day_minute day_second deallocate dec declare default delay_key_write delayed delimiter des_key_file describe deterministic dev_pop dev_samp deviance diagnostics directory disable discard distinctrow div dual dumpfile each elseif enable enclosed end ends engine engines enum errors escape escaped even event events every execute exists exit explain extended fast fetch field fields first flush for force foreign found_rows full fulltext function general get global grant grants group groupby_concat handler hash help high_priority hosts hour_microsecond hour_minute hour_second if ignore ignore_server_ids import index index_statistics infile inner innodb inout insensitive insert_method install interval invoker isolation iterate key keys kill language last leading leave left level limit linear lines list load local localtime localtimestamp lock logs low_priority master master_heartbeat_period master_ssl_verify_server_cert masters match max max_rows maxvalue message_text middleint migrate min min_rows minute_microsecond minute_second mod mode modifies modify mutex mysql_errno natural next no no_write_to_binlog offline offset one online open optimize option optionally out outer outfile pack_keys parser partition partitions password phase plugin plugins prepare preserve prev primary privileges procedure processlist profile profiles purge query quick range read read_write reads real rebuild recover references regexp relaylog release remove rename reorganize repair repeatable replace require resignal restrict resume return returns revoke right rlike rollback rollup row row_format rtree savepoint schedule schema schema_name schemas second_microsecond security sensitive separator serializable server session share show signal slave slow smallint snapshot soname spatial specific sql sql_big_result sql_buffer_result sql_cache sql_calc_found_rows sql_no_cache sql_small_result sqlexception sqlstate sqlwarning ssl start starting starts status std stddev stddev_pop stddev_samp storage straight_join subclass_origin sum suspend table_name table_statistics tables tablespace temporary terminated to trailing transaction trigger triggers truncate uncommitted undo uninstall unique unlock upgrade usage use use_frm user user_resources user_statistics using utc_date utc_time utc_timestamp value variables varying view views warnings when while with work write xa xor year_month zerofill begin do then else loop repeat"),
			builtin:       f("bool boolean bit blob decimal double float long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text bigint int int1 int2 int3 int4 int8 integer float float4 float8 double char varbinary varchar varcharacter precision date datetime year unsigned signed numeric"),
			atoms:         f("false true null unknown"),
			operatorChars: /^[*+\-%<>!=&|^]/,
			dateSQL:       f("date time timestamp"),
			support:       f("ODBCdotTable decimallessFloat zerolessFloat binaryNumber hexNumber doubleQuote nCharCast charsetCast commentHash commentSpaceRequired"),
			hooks:         {"@": c, "`": b, "\\": d}
		}), a.defineMIME("text/x-mariadb", {
			name:          "sql",
			client:        f("charset clear connect edit ego exit go help nopager notee nowarning pager print prompt quit rehash source status system tee"),
			keywords:      f(e + "accessible action add after algorithm all always analyze asensitive at authors auto_increment autocommit avg avg_row_length before binary binlog both btree cache call cascade cascaded case catalog_name chain change changed character check checkpoint checksum class_origin client_statistics close coalesce code collate collation collations column columns comment commit committed completion concurrent condition connection consistent constraint contains continue contributors convert cross current current_date current_time current_timestamp current_user cursor data database databases day_hour day_microsecond day_minute day_second deallocate dec declare default delay_key_write delayed delimiter des_key_file describe deterministic dev_pop dev_samp deviance diagnostics directory disable discard distinctrow div dual dumpfile each elseif enable enclosed end ends engine engines enum errors escape escaped even event events every execute exists exit explain extended fast fetch field fields first flush for force foreign found_rows full fulltext function general generated get global grant grants group groupby_concat handler hard hash help high_priority hosts hour_microsecond hour_minute hour_second if ignore ignore_server_ids import index index_statistics infile inner innodb inout insensitive insert_method install interval invoker isolation iterate key keys kill language last leading leave left level limit linear lines list load local localtime localtimestamp lock logs low_priority master master_heartbeat_period master_ssl_verify_server_cert masters match max max_rows maxvalue message_text middleint migrate min min_rows minute_microsecond minute_second mod mode modifies modify mutex mysql_errno natural next no no_write_to_binlog offline offset one online open optimize option optionally out outer outfile pack_keys parser partition partitions password persistent phase plugin plugins prepare preserve prev primary privileges procedure processlist profile profiles purge query quick range read read_write reads real rebuild recover references regexp relaylog release remove rename reorganize repair repeatable replace require resignal restrict resume return returns revoke right rlike rollback rollup row row_format rtree savepoint schedule schema schema_name schemas second_microsecond security sensitive separator serializable server session share show shutdown signal slave slow smallint snapshot soft soname spatial specific sql sql_big_result sql_buffer_result sql_cache sql_calc_found_rows sql_no_cache sql_small_result sqlexception sqlstate sqlwarning ssl start starting starts status std stddev stddev_pop stddev_samp storage straight_join subclass_origin sum suspend table_name table_statistics tables tablespace temporary terminated to trailing transaction trigger triggers truncate uncommitted undo uninstall unique unlock upgrade usage use use_frm user user_resources user_statistics using utc_date utc_time utc_timestamp value variables varying view views virtual warnings when while with work write xa xor year_month zerofill begin do then else loop repeat"),
			builtin:       f("bool boolean bit blob decimal double float long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text bigint int int1 int2 int3 int4 int8 integer float float4 float8 double char varbinary varchar varcharacter precision date datetime year unsigned signed numeric"),
			atoms:         f("false true null unknown"),
			operatorChars: /^[*+\-%<>!=&|^]/,
			dateSQL:       f("date time timestamp"),
			support:       f("ODBCdotTable decimallessFloat zerolessFloat binaryNumber hexNumber doubleQuote nCharCast charsetCast commentHash commentSpaceRequired"),
			hooks:         {"@": c, "`": b, "\\": d}
		}), a.defineMIME("text/x-cassandra", {
			name:          "sql",
			client:        {},
			keywords:      f("use select from using consistency where limit first reversed first and in insert into values using consistency ttl update set delete truncate begin batch apply create keyspace with columnfamily primary key index on drop alter type add any one quorum all local_quorum each_quorum"),
			builtin:       f("ascii bigint blob boolean counter decimal double float int text timestamp uuid varchar varint"),
			atoms:         f("false true"),
			operatorChars: /^[<>=]/,
			dateSQL:       {},
			support:       f("commentSlashSlash decimallessFloat"),
			hooks:         {}
		}), a.defineMIME("text/x-plsql", {
			name:          "sql",
			client:        f("appinfo arraysize autocommit autoprint autorecovery autotrace blockterminator break btitle cmdsep colsep compatibility compute concat copycommit copytypecheck define describe echo editfile embedded escape exec execute feedback flagger flush heading headsep instance linesize lno loboffset logsource long longchunksize markup native newpage numformat numwidth pagesize pause pno recsep recsepchar release repfooter repheader serveroutput shiftinout show showmode size spool sqlblanklines sqlcase sqlcode sqlcontinue sqlnumber sqlpluscompatibility sqlprefix sqlprompt sqlterminator suffix tab term termout time timing trimout trimspool ttitle underline verify version wrap"),
			keywords:      f("abort accept access add all alter and any array arraylen as asc assert assign at attributes audit authorization avg base_table begin between binary_integer body boolean by case cast char char_base check close cluster clusters colauth column comment commit compress connect connected constant constraint crash create current currval cursor data_base database date dba deallocate debugoff debugon decimal declare default definition delay delete desc digits dispose distinct do drop else elseif elsif enable end entry escape exception exception_init exchange exclusive exists exit external fast fetch file for force form from function generic goto grant group having identified if immediate in increment index indexes indicator initial initrans insert interface intersect into is key level library like limited local lock log logging long loop master maxextents maxtrans member minextents minus mislabel mode modify multiset new next no noaudit nocompress nologging noparallel not nowait number_base object of off offline on online only open option or order out package parallel partition pctfree pctincrease pctused pls_integer positive positiven pragma primary prior private privileges procedure public raise range raw read rebuild record ref references refresh release rename replace resource restrict return returning returns reverse revoke rollback row rowid rowlabel rownum rows run savepoint schema segment select separate session set share snapshot some space split sql start statement storage subtype successful synonym tabauth table tables tablespace task terminate then to trigger truncate type union unique unlimited unrecoverable unusable update use using validate value values variable view views when whenever where while with work"),
			builtin:       f("abs acos add_months ascii asin atan atan2 average bfile bfilename bigserial bit blob ceil character chartorowid chr clob concat convert cos cosh count dec decode deref dual dump dup_val_on_index empty error exp false float floor found glb greatest hextoraw initcap instr instrb int integer isopen last_day least lenght lenghtb ln lower lpad ltrim lub make_ref max min mlslabel mod months_between natural naturaln nchar nclob new_time next_day nextval nls_charset_decl_len nls_charset_id nls_charset_name nls_initcap nls_lower nls_sort nls_upper nlssort no_data_found notfound null number numeric nvarchar2 nvl others power rawtohex real reftohex round rowcount rowidtochar rowtype rpad rtrim serial sign signtype sin sinh smallint soundex sqlcode sqlerrm sqrt stddev string substr substrb sum sysdate tan tanh to_char text to_date to_label to_multi_byte to_number to_single_byte translate true trunc uid unlogged upper user userenv varchar varchar2 variance varying vsize xml"),
			operatorChars: /^[*+\-%<>!=~]/,
			dateSQL:       f("date time timestamp"),
			support:       f("doubleQuote nCharCast zerolessFloat binaryNumber hexNumber")
		}), a.defineMIME("text/x-hive", {
			name:          "sql",
			keywords:      f("select alter $elem$ $key$ $value$ add after all analyze and archive as asc before between binary both bucket buckets by cascade case cast change cluster clustered clusterstatus collection column columns comment compute concatenate continue create cross cursor data database databases dbproperties deferred delete delimited desc describe directory disable distinct distribute drop else enable end escaped exclusive exists explain export extended external false fetch fields fileformat first format formatted from full function functions grant group having hold_ddltime idxproperties if import in index indexes inpath inputdriver inputformat insert intersect into is items join keys lateral left like limit lines load local location lock locks mapjoin materialized minus msck no_drop nocompress not of offline on option or order out outer outputdriver outputformat overwrite partition partitioned partitions percent plus preserve procedure purge range rcfile read readonly reads rebuild recordreader recordwriter recover reduce regexp rename repair replace restrict revoke right rlike row schema schemas semi sequencefile serde serdeproperties set shared show show_database sort sorted ssl statistics stored streamtable table tables tablesample tblproperties temporary terminated textfile then tmp to touch transform trigger true unarchive undo union uniquejoin unlock update use using utc utc_tmestamp view when where while with"),
			builtin:       f("bool boolean long timestamp tinyint smallint bigint int float double date datetime unsigned string array struct map uniontype"),
			atoms:         f("false true null unknown"),
			operatorChars: /^[*+\-%<>!=]/,
			dateSQL:       f("date timestamp"),
			support:       f("ODBCdotTable doubleQuote binaryNumber hexNumber")
		})
	}()
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.defineMode("xml", function (b, c) {
		function k(a, b) {
			function c(c) {
				return b.tokenize = c, c(a, b)
			}

			var d = a.next();
			if ("<" == d)return a.eat("!") ? a.eat("[") ? a.match("CDATA[") ? c(n("atom", "]]>")) : null : a.match("--") ? c(n("comment", "-->")) : a.match("DOCTYPE", !0, !0) ? (a.eatWhile(/[\w\._\-]/), c(o(1))) : null : a.eat("?") ? (a.eatWhile(/[\w\._\-]/), b.tokenize = n("meta", "?>"), "meta") : (i = a.eat("/") ? "closeTag" : "openTag", b.tokenize = l, "tag bracket");
			if ("&" == d) {
				var e;
				return e = a.eat("#") ? a.eat("x") ? a.eatWhile(/[a-fA-F\d]/) && a.eat(";") : a.eatWhile(/[\d]/) && a.eat(";") : a.eatWhile(/[\w\.\-:]/) && a.eat(";"), e ? "atom" : "error"
			}
			return a.eatWhile(/[^&<]/), null
		}

		function l(a, b) {
			var c = a.next();
			if (">" == c || "/" == c && a.eat(">"))return b.tokenize = k, i = ">" == c ? "endTag" : "selfcloseTag", "tag bracket";
			if ("=" == c)return i = "equals", null;
			if ("<" == c) {
				b.tokenize = k, b.state = s, b.tagName = b.tagStart = null;
				var d = b.tokenize(a, b);
				return d ? d + " tag error" : "tag error"
			}
			return /[\'\"]/.test(c) ? (b.tokenize = m(c), b.stringStartCol = a.column(), b.tokenize(a, b)) : (a.match(/^[^\s\u00a0=<>\"\']*[^\s\u00a0=<>\"\'\/]/), "word")
		}

		function m(a) {
			var b = function (b, c) {
				for (; !b.eol();)if (b.next() == a) {
					c.tokenize = l;
					break
				}
				return "string"
			};
			return b.isInAttribute = !0, b
		}

		function n(a, b) {
			return function (c, d) {
				for (; !c.eol();) {
					if (c.match(b)) {
						d.tokenize = k;
						break
					}
					c.next()
				}
				return a
			}
		}

		function o(a) {
			return function (b, c) {
				for (var d; null != (d = b.next());) {
					if ("<" == d)return c.tokenize = o(a + 1), c.tokenize(b, c);
					if (">" == d) {
						if (1 == a) {
							c.tokenize = k;
							break
						}
						return c.tokenize = o(a - 1), c.tokenize(b, c)
					}
				}
				return "meta"
			}
		}

		function p(a, b, c) {
			this.prev = a.context, this.tagName = b, this.indent = a.indented, this.startOfLine = c, (g.doNotIndent.hasOwnProperty(b) || a.context && a.context.noIndent) && (this.noIndent = !0)
		}

		function q(a) {
			a.context && (a.context = a.context.prev)
		}

		function r(a, b) {
			for (var c; ;) {
				if (!a.context)return;
				if (c = a.context.tagName, !g.contextGrabbers.hasOwnProperty(c) || !g.contextGrabbers[c].hasOwnProperty(b))return;
				q(a)
			}
		}

		function s(a, b, c) {
			return "openTag" == a ? (c.tagStart = b.column(), t) : "closeTag" == a ? u : s
		}

		function t(a, b, c) {
			return "word" == a ? (c.tagName = b.current(), j = "tag", x) : (j = "error", t)
		}

		function u(a, b, c) {
			if ("word" == a) {
				var d = b.current();
				return c.context && c.context.tagName != d && g.implicitlyClosed.hasOwnProperty(c.context.tagName) && q(c), c.context && c.context.tagName == d ? (j = "tag", v) : (j = "tag error", w)
			}
			return j = "error", w
		}

		function v(a, b, c) {
			return "endTag" != a ? (j = "error", v) : (q(c), s)
		}

		function w(a, b, c) {
			return j = "error", v(a, b, c)
		}

		function x(a, b, c) {
			if ("word" == a)return j = "attribute", y;
			if ("endTag" == a || "selfcloseTag" == a) {
				var d = c.tagName, e = c.tagStart;
				return c.tagName = c.tagStart = null, "selfcloseTag" == a || g.autoSelfClosers.hasOwnProperty(d) ? r(c, d) : (r(c, d), c.context = new p(c, d, e == c.indented)), s
			}
			return j = "error", x
		}

		function y(a, b, c) {
			return "equals" == a ? z : (g.allowMissing || (j = "error"), x(a, b, c))
		}

		function z(a, b, c) {
			return "string" == a ? A : "word" == a && g.allowUnquoted ? (j = "string", x) : (j = "error", x(a, b, c))
		}

		function A(a, b, c) {
			return "string" == a ? A : x(a, b, c)
		}

		var d = b.indentUnit, e = c.multilineTagIndentFactor || 1, f = c.multilineTagIndentPastTag;
		null == f && (f = !0);
		var i, j, g = c.htmlMode ? {
			autoSelfClosers:  {
				area:    !0,
				base:    !0,
				br:      !0,
				col:     !0,
				command: !0,
				embed:   !0,
				frame:   !0,
				hr:      !0,
				img:     !0,
				input:   !0,
				keygen:  !0,
				link:    !0,
				meta:    !0,
				param:   !0,
				source:  !0,
				track:   !0,
				wbr:     !0
			},
			implicitlyClosed: {
				dd:       !0,
				li:       !0,
				optgroup: !0,
				option:   !0,
				p:        !0,
				rp:       !0,
				rt:       !0,
				tbody:    !0,
				td:       !0,
				tfoot:    !0,
				th:       !0,
				tr:       !0
			},
			contextGrabbers:  {
				dd:       {dd: !0, dt: !0},
				dt:       {dd: !0, dt: !0},
				li:       {li: !0},
				option:   {option: !0, optgroup: !0},
				optgroup: {optgroup: !0},
				p:        {
					address:    !0,
					article:    !0,
					aside:      !0,
					blockquote: !0,
					dir:        !0,
					div:        !0,
					dl:         !0,
					fieldset:   !0,
					footer:     !0,
					form:       !0,
					h1:         !0,
					h2:         !0,
					h3:         !0,
					h4:         !0,
					h5:         !0,
					h6:         !0,
					header:     !0,
					hgroup:     !0,
					hr:         !0,
					menu:       !0,
					nav:        !0,
					ol:         !0,
					p:          !0,
					pre:        !0,
					section:    !0,
					table:      !0,
					ul:         !0
				},
				rp:       {rp: !0, rt: !0},
				rt:       {rp: !0, rt: !0},
				tbody:    {tbody: !0, tfoot: !0},
				td:       {td: !0, th: !0},
				tfoot:    {tbody: !0},
				th:       {td: !0, th: !0},
				thead:    {tbody: !0, tfoot: !0},
				tr:       {tr: !0}
			},
			doNotIndent:      {pre: !0},
			allowUnquoted:    !0,
			allowMissing:     !0,
			caseFold:         !0
		} : {
			autoSelfClosers:  {},
			implicitlyClosed: {},
			contextGrabbers:  {},
			doNotIndent:      {},
			allowUnquoted:    !1,
			allowMissing:     !1,
			caseFold:         !1
		}, h = c.alignCDATA;
		return {
			startState:        function () {
				return {tokenize: k, state: s, indented: 0, tagName: null, tagStart: null, context: null}
			},
			token:             function (a, b) {
				if (!b.tagName && a.sol() && (b.indented = a.indentation()), a.eatSpace())return null;
				i = null;
				var c = b.tokenize(a, b);
				return (c || i) && "comment" != c && (j = null, b.state = b.state(i || c, a, b), j && (c = "error" == j ? c + " error" : j)), c
			},
			indent:            function (b, c, i) {
				var j = b.context;
				if (b.tokenize.isInAttribute)return b.tagStart == b.indented ? b.stringStartCol + 1 : b.indented + d;
				if (j && j.noIndent)return a.Pass;
				if (b.tokenize != l && b.tokenize != k)return i ? i.match(/^(\s*)/)[0].length : 0;
				if (b.tagName)return f ? b.tagStart + b.tagName.length + 2 : b.tagStart + d * e;
				if (h && /<!\[CDATA\[/.test(c))return 0;
				var m = c && /^<(\/)?([\w_:\.-]*)/.exec(c);
				if (m && m[1])for (; j;) {
					if (j.tagName == m[2]) {
						j = j.prev;
						break
					}
					if (!g.implicitlyClosed.hasOwnProperty(j.tagName))break;
					j = j.prev
				} else if (m)for (; j;) {
					var n = g.contextGrabbers[j.tagName];
					if (!n || !n.hasOwnProperty(m[2]))break;
					j = j.prev
				}
				for (; j && !j.startOfLine;)j = j.prev;
				return j ? j.indent + d : 0
			},
			electricInput:     /<\/[\s\w:]+>$/,
			blockCommentStart: "<!--",
			blockCommentEnd:   "-->",
			configuration:     c.htmlMode ? "html" : "xml",
			helperType:        c.htmlMode ? "html" : "xml"
		}
	}), a.defineMIME("text/xml", "xml"), a.defineMIME("application/xml", "xml"), a.mimeModes.hasOwnProperty("text/html") || a.defineMIME("text/html", {
		name: "xml",
		htmlMode: !0
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	function f(a, b) {
		var c = a.getRange(e(b.line, b.ch - 1), e(b.line, b.ch + 1));
		return 2 == c.length ? c : null
	}

	function g(b) {
		for (var c = {
			name: "autoCloseBrackets", Backspace: function (c) {
				if (c.getOption("disableInput"))return a.Pass;
				for (var d = c.listSelections(), g = 0; g < d.length; g++) {
					if (!d[g].empty())return a.Pass;
					var h = f(c, d[g].head);
					if (!h || 0 != b.indexOf(h) % 2)return a.Pass
				}
				for (var g = d.length - 1; g >= 0; g--) {
					var i = d[g].head;
					c.replaceRange("", e(i.line, i.ch - 1), e(i.line, i.ch + 1))
				}
			}
		}, g = "", h = 0; h < b.length; h += 2)!function (b, f) {
			b != f && (g += f), c["'" + b + "'"] = function (c) {
				if (c.getOption("disableInput"))return a.Pass;
				for (var i, j, h = c.listSelections(), k = 0; k < h.length; k++) {
					var n, l = h[k], m = l.head;
					if ("'" == b && "comment" == c.getTokenTypeAt(m))return a.Pass;
					var j = c.getRange(m, e(m.line, m.ch + 1));
					if (l.empty())if (b == f && j == f)n = c.getRange(m, e(m.line, m.ch + 3)) == b + b + b ? "skipThree" : "skip"; else if (b == f && m.ch > 1 && c.getRange(e(m.line, m.ch - 2), m) == b + b && (m.ch <= 2 || c.getRange(e(m.line, m.ch - 3), e(m.line, m.ch - 2)) != b))n = "addFour"; else {
						if (b == f && a.isWordChar(j))return a.Pass;
						if (!(c.getLine(m.line).length == m.ch || g.indexOf(j) >= 0 || d.test(j)))return a.Pass;
						n = "both"
					} else n = "surround";
					if (i) {
						if (i != n)return a.Pass
					} else i = n
				}
				c.operation(function () {
					if ("skip" == i)c.execCommand("goCharRight"); else if ("skipThree" == i)for (var a = 0; 3 > a; a++)c.execCommand("goCharRight"); else if ("surround" == i) {
						for (var d = c.getSelections(), a = 0; a < d.length; a++)d[a] = b + d[a] + f;
						c.replaceSelections(d, "around")
					} else"both" == i ? (c.replaceSelection(b + f, null), c.execCommand("goCharLeft")) : "addFour" == i && (c.replaceSelection(b + b + b + b, "before"), c.execCommand("goCharRight"))
				})
			}, b != f && (c["'" + f + "'"] = function (b) {
				for (var c = b.listSelections(), d = 0; d < c.length; d++) {
					var g = c[d];
					if (!g.empty() || b.getRange(g.head, e(g.head.line, g.head.ch + 1)) != f)return a.Pass
				}
				b.execCommand("goCharRight")
			})
		}(b.charAt(h), b.charAt(h + 1));
		return c
	}

	function h(b) {
		return function (c) {
			if (c.getOption("disableInput"))return a.Pass;
			for (var d = c.listSelections(), e = 0; e < d.length; e++) {
				if (!d[e].empty())return a.Pass;
				var g = f(c, d[e].head);
				if (!g || 0 != b.indexOf(g) % 2)return a.Pass
			}
			c.operation(function () {
				c.replaceSelection("\n\n", null), c.execCommand("goCharLeft"), d = c.listSelections();
				for (var a = 0; a < d.length; a++) {
					var b = d[a].head.line;
					c.indentLine(b, null, !0), c.indentLine(b + 1, null, !0)
				}
			})
		}
	}

	var b = "()[]{}''\"\"", c = "[]{}", d = /\s/, e = a.Pos;
	a.defineOption("autoCloseBrackets", !1, function (d, e, f) {
		if (f != a.Init && f && d.removeKeyMap("autoCloseBrackets"), e) {
			var i = b, j = c;
			"string" == typeof e ? i = e : "object" == typeof e && (null != e.pairs && (i = e.pairs), null != e.explode && (j = e.explode));
			var k = g(i);
			j && (k.Enter = h(j)), d.addKeyMap(k)
		}
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror"), require("../fold/xml-fold")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../fold/xml-fold"], a) : a(CodeMirror)
}(function (a) {
	function d(d) {
		if (d.getOption("disableInput"))return a.Pass;
		for (var e = d.listSelections(), h = [], i = 0; i < e.length; i++) {
			if (!e[i].empty())return a.Pass;
			var j = e[i].head, k = d.getTokenAt(j), l = a.innerMode(d.getMode(), k.state), m = l.state;
			if ("xml" != l.mode.name || !m.tagName)return a.Pass;
			var n = d.getOption("autoCloseTags"), o = "html" == l.mode.configuration, p = "object" == typeof n && n.dontCloseTags || o && b, q = "object" == typeof n && n.indentTags || o && c, r = m.tagName;
			k.end > j.ch && (r = r.slice(0, r.length - k.end + j.ch));
			var s = r.toLowerCase();
			if (!r || "string" == k.type && (k.end != j.ch || !/[\"\']/.test(k.string.charAt(k.string.length - 1)) || 1 == k.string.length) || "tag" == k.type && "closeTag" == m.type || k.string.indexOf("/") == k.string.length - 1 || p && f(p, s) > -1 || g(d, r, j, m, !0))return a.Pass;
			var t = q && f(q, s) > -1;
			h[i] = {
				indent: t,
				text:   ">" + (t ? "\n\n" : "") + "</" + r + ">",
				newPos: t ? a.Pos(j.line + 1, 0) : a.Pos(j.line, j.ch + 1)
			}
		}
		for (var i = e.length - 1; i >= 0; i--) {
			var u = h[i];
			d.replaceRange(u.text, e[i].head, e[i].anchor, "+insert");
			var v = d.listSelections().slice(0);
			v[i] = {
				head:   u.newPos,
				anchor: u.newPos
			}, d.setSelections(v), u.indent && (d.indentLine(u.newPos.line, null, !0), d.indentLine(u.newPos.line + 1, null, !0))
		}
	}

	function e(b) {
		if (b.getOption("disableInput"))return a.Pass;
		for (var c = b.listSelections(), d = [], e = 0; e < c.length; e++) {
			if (!c[e].empty())return a.Pass;
			var f = c[e].head, h = b.getTokenAt(f), i = a.innerMode(b.getMode(), h.state), j = i.state;
			if ("string" == h.type || "<" != h.string.charAt(0) || h.start != f.ch - 1 || "xml" != i.mode.name || !j.context || !j.context.tagName || g(b, j.context.tagName, f, j))return a.Pass;
			d[e] = "/" + j.context.tagName + ">"
		}
		b.replaceSelections(d)
	}

	function f(a, b) {
		if (a.indexOf)return a.indexOf(b);
		for (var c = 0, d = a.length; d > c; ++c)if (a[c] == b)return c;
		return -1
	}

	function g(b, c, d, e, f) {
		if (!a.scanForClosingTag)return !1;
		var g = Math.min(b.lastLine() + 1, d.line + 500), h = a.scanForClosingTag(b, d, null, g);
		if (!h || h.tag != c)return !1;
		for (var i = e.context, j = f ? 1 : 0; i && i.tagName == c; i = i.prev)++j;
		d = h.to;
		for (var k = 1; j > k; k++) {
			var l = a.scanForClosingTag(b, d, null, g);
			if (!l || l.tag != c)return !1;
			d = l.to
		}
		return !0
	}

	a.defineOption("autoCloseTags", !1, function (b, c, f) {
		if (f != a.Init && f && b.removeKeyMap("autoCloseTags"), c) {
			var g = {name: "autoCloseTags"};
			("object" != typeof c || c.whenClosing) && (g["'/'"] = function (a) {
				return e(a)
			}), ("object" != typeof c || c.whenOpening) && (g["'>'"] = function (a) {
				return d(a)
			}), b.addKeyMap(g)
		}
	});
	var b = ["area", "base", "br", "col", "command", "embed", "hr", "img", "input", "keygen", "link", "meta", "param", "source", "track", "wbr"], c = ["applet", "blockquote", "body", "button", "div", "dl", "fieldset", "form", "frameset", "h1", "h2", "h3", "h4", "h5", "h6", "head", "html", "iframe", "layer", "legend", "object", "ol", "p", "select", "table", "ul"]
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a) {
		var b = a.getWrapperElement();
		a.state.fullScreenRestore = {
			scrollTop:  window.pageYOffset,
			scrollLeft: window.pageXOffset,
			width:      b.style.width,
			height:     b.style.height
		}, b.style.width = "", b.style.height = "auto", b.className += " CodeMirror-fullscreen", document.documentElement.style.overflow = "hidden", a.refresh()
	}

	function c(a) {
		var b = a.getWrapperElement();
		b.className = b.className.replace(/\s*CodeMirror-fullscreen\b/, ""), document.documentElement.style.overflow = "";
		var c = a.state.fullScreenRestore;
		b.style.width = c.width, b.style.height = c.height, window.scrollTo(c.scrollLeft, c.scrollTop), a.refresh()
	}

	a.defineOption("fullScreen", !1, function (d, e, f) {
		f == a.Init && (f = !1), !f != !e && (e ? b(d) : c(d))
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	a.registerHelper("fold", "indent", function (b, c) {
		var d = b.getOption("tabSize"), e = b.getLine(c.line);
		if (/\S/.test(e)) {
			for (var f = function (b) {
				return a.countColumn(b, null, d)
			}, g = f(e), h = null, i = c.line + 1, j = b.lastLine(); j >= i; ++i) {
				var k = b.getLine(i), l = f(k);
				if (l > g)h = i; else if (/\S/.test(k))break
			}
			return h ? {from: a.Pos(c.line, e.length), to: a.Pos(h, b.getLine(h).length)} : void 0
		}
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a) {
		a.operation(function () {
			j(a)
		})
	}

	function c(a) {
		a.state.markedSelection.length && a.operation(function () {
			h(a)
		})
	}

	function g(a, b, c, g) {
		if (0 != f(b, c))for (var h = a.state.markedSelection, i = a.state.markedSelectionStyle, j = b.line; ;) {
			var k = j == b.line ? b : e(j, 0), l = j + d, m = l >= c.line, n = m ? c : e(l, 0), o = a.markText(k, n, {className: i});
			if (null == g ? h.push(o) : h.splice(g++, 0, o), m)break;
			j = l
		}
	}

	function h(a) {
		for (var b = a.state.markedSelection, c = 0; c < b.length; ++c)b[c].clear();
		b.length = 0
	}

	function i(a) {
		h(a);
		for (var b = a.listSelections(), c = 0; c < b.length; c++)g(a, b[c].from(), b[c].to())
	}

	function j(a) {
		if (!a.somethingSelected())return h(a);
		if (a.listSelections().length > 1)return i(a);
		var b = a.getCursor("start"), c = a.getCursor("end"), e = a.state.markedSelection;
		if (!e.length)return g(a, b, c);
		var j = e[0].find(), k = e[e.length - 1].find();
		if (!j || !k || c.line - b.line < d || f(b, k.to) >= 0 || f(c, j.from) <= 0)return i(a);
		for (; f(b, j.from) > 0;)e.shift().clear(), j = e[0].find();
		for (f(b, j.from) < 0 && (j.to.line - b.line < d ? (e.shift().clear(), g(a, b, j.to, 0)) : g(a, b, j.from, 0)); f(c, k.to) < 0;)e.pop().clear(), k = e[e.length - 1].find();
		f(c, k.to) > 0 && (c.line - k.from.line < d ? (e.pop().clear(), g(a, k.from, c)) : g(a, k.to, c))
	}

	a.defineOption("styleSelectedText", !1, function (d, e, f) {
		var g = f && f != a.Init;
		e && !g ? (d.state.markedSelection = [], d.state.markedSelectionStyle = "string" == typeof e ? e : "CodeMirror-selectedtext", i(d), d.on("cursorActivity", b), d.on("change", c)) : !e && g && (d.off("cursorActivity", b), d.off("change", c), h(d), d.state.markedSelection = d.state.markedSelectionStyle = null)
	});
	var d = 8, e = a.Pos, f = a.cmpPos
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function e(a) {
		"object" == typeof a && (this.minChars = a.minChars, this.style = a.style, this.showToken = a.showToken, this.delay = a.delay), null == this.style && (this.style = c), null == this.minChars && (this.minChars = b), null == this.delay && (this.delay = d), this.overlay = this.timeout = null
	}

	function f(a) {
		var b = a.state.matchHighlighter;
		clearTimeout(b.timeout), b.timeout = setTimeout(function () {
			g(a)
		}, b.delay)
	}

	function g(a) {
		a.operation(function () {
			var b = a.state.matchHighlighter;
			if (b.overlay && (a.removeOverlay(b.overlay), b.overlay = null), !a.somethingSelected() && b.showToken) {
				for (var c = b.showToken === !0 ? /[\w$]/ : b.showToken, d = a.getCursor(), e = a.getLine(d.line), f = d.ch, g = f; f && c.test(e.charAt(f - 1));)--f;
				for (; g < e.length && c.test(e.charAt(g));)++g;
				return g > f && a.addOverlay(b.overlay = i(e.slice(f, g), c, b.style)), void 0
			}
			var h = a.getCursor("from"), j = a.getCursor("to");
			if (h.line == j.line) {
				var k = a.getRange(h, j).replace(/^\s+|\s+$/g, "");
				k.length >= b.minChars && a.addOverlay(b.overlay = i(k, !1, b.style))
			}
		})
	}

	function h(a, b) {
		return !(a.start && b.test(a.string.charAt(a.start - 1)) || a.pos != a.string.length && b.test(a.string.charAt(a.pos)))
	}

	function i(a, b, c) {
		return {
			token: function (d) {
				return !d.match(a) || b && !h(d, b) ? (d.next(), d.skipTo(a.charAt(0)) || d.skipToEnd(), void 0) : c
			}
		}
	}

	var b = 2, c = "matchhighlight", d = 100;
	a.defineOption("highlightSelectionMatches", !1, function (b, c, d) {
		if (d && d != a.Init) {
			var h = b.state.matchHighlighter.overlay;
			h && b.removeOverlay(h), clearTimeout(b.state.matchHighlighter.timeout), b.state.matchHighlighter = null, b.off("cursorActivity", f)
		}
		c && (b.state.matchHighlighter = new e(c), g(b), b.on("cursorActivity", f))
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	function e(a, b, e, g) {
		var h = a.getLineHandle(b.line), i = b.ch - 1, j = i >= 0 && d[h.text.charAt(i)] || d[h.text.charAt(++i)];
		if (!j)return null;
		var k = ">" == j.charAt(1) ? 1 : -1;
		if (e && k > 0 != (i == b.ch))return null;
		var l = a.getTokenTypeAt(c(b.line, i + 1)), m = f(a, c(b.line, i + (k > 0 ? 1 : 0)), k, l || null, g);
		return null == m ? null : {from: c(b.line, i), to: m && m.pos, match: m && m.ch == j.charAt(0), forward: k > 0}
	}

	function f(a, b, e, f, g) {
		for (var h = g && g.maxScanLineLength || 1e4, i = g && g.maxScanLines || 1e3, j = [], k = g && g.bracketRegex ? g.bracketRegex : /[(){}[\]]/, l = e > 0 ? Math.min(b.line + i, a.lastLine() + 1) : Math.max(a.firstLine() - 1, b.line - i), m = b.line; m != l; m += e) {
			var n = a.getLine(m);
			if (n) {
				var o = e > 0 ? 0 : n.length - 1, p = e > 0 ? n.length : -1;
				if (!(n.length > h))for (m == b.line && (o = b.ch - (0 > e ? 1 : 0)); o != p; o += e) {
					var q = n.charAt(o);
					if (k.test(q) && (void 0 === f || a.getTokenTypeAt(c(m, o + 1)) == f)) {
						var r = d[q];
						if (">" == r.charAt(1) == e > 0)j.push(q); else {
							if (!j.length)return {pos: c(m, o), ch: q};
							j.pop()
						}
					}
				}
			}
		}
		return m - e == (e > 0 ? a.lastLine() : a.firstLine()) ? !1 : null
	}

	function g(a, d, f) {
		for (var g = a.state.matchBrackets.maxHighlightLineLength || 1e3, h = [], i = a.listSelections(), j = 0; j < i.length; j++) {
			var k = i[j].empty() && e(a, i[j].head, !1, f);
			if (k && a.getLine(k.from.line).length <= g) {
				var l = k.match ? "CodeMirror-matchingbracket" : "CodeMirror-nonmatchingbracket";
				h.push(a.markText(k.from, c(k.from.line, k.from.ch + 1), {className: l})), k.to && a.getLine(k.to.line).length <= g && h.push(a.markText(k.to, c(k.to.line, k.to.ch + 1), {className: l}))
			}
		}
		if (h.length) {
			b && a.state.focused && a.display.input.focus();
			var m = function () {
				a.operation(function () {
					for (var a = 0; a < h.length; a++)h[a].clear()
				})
			};
			if (!d)return m;
			setTimeout(m, 800)
		}
	}

	function i(a) {
		a.operation(function () {
			h && (h(), h = null), h = g(a, !1, a.state.matchBrackets)
		})
	}

	var b = /MSIE \d/.test(navigator.userAgent) && (null == document.documentMode || document.documentMode < 8), c = a.Pos, d = {
		"(": ")>",
		")": "(<",
		"[": "]>",
		"]": "[<",
		"{": "}>",
		"}": "{<"
	}, h = null;
	a.defineOption("matchBrackets", !1, function (b, c, d) {
		d && d != a.Init && b.off("cursorActivity", i), c && (b.state.matchBrackets = "object" == typeof c ? c : {}, b.on("cursorActivity", i))
	}), a.defineExtension("matchBrackets", function () {
		g(this, !0)
	}), a.defineExtension("findMatchingBracket", function (a, b, c) {
		return e(this, a, b, c)
	}), a.defineExtension("scanForBracket", function (a, b, c, d) {
		return f(this, a, b, c, d)
	})
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror"), require("../fold/xml-fold")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../fold/xml-fold"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a) {
		a.state.tagHit && a.state.tagHit.clear(), a.state.tagOther && a.state.tagOther.clear(), a.state.tagHit = a.state.tagOther = null
	}

	function c(c) {
		c.state.failedTagMatch = !1, c.operation(function () {
			if (b(c), !c.somethingSelected()) {
				var d = c.getCursor(), e = c.getViewport();
				e.from = Math.min(e.from, d.line), e.to = Math.max(d.line + 1, e.to);
				var f = a.findMatchingTag(c, d, e);
				if (f) {
					if (c.state.matchBothTags) {
						var g = "open" == f.at ? f.open : f.close;
						g && (c.state.tagHit = c.markText(g.from, g.to, {className: "CodeMirror-matchingtag"}))
					}
					var h = "close" == f.at ? f.open : f.close;
					h ? c.state.tagOther = c.markText(h.from, h.to, {className: "CodeMirror-matchingtag"}) : c.state.failedTagMatch = !0
				}
			}
		})
	}

	function d(a) {
		a.state.failedTagMatch && c(a)
	}

	a.defineOption("matchTags", !1, function (e, f, g) {
		g && g != a.Init && (e.off("cursorActivity", c), e.off("viewportChange", d), b(e)), f && (e.state.matchBothTags = "object" == typeof f && f.bothTags, e.on("cursorActivity", c), e.on("viewportChange", d), c(e))
	}), a.commands.toMatchingTag = function (b) {
		var c = a.findMatchingTag(b, b.getCursor());
		if (c) {
			var d = "close" == c.at ? c.open : c.close;
			d && b.extendSelection(d.to, d.from)
		}
	}
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror"), require("./searchcursor"), require("../dialog/dialog")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "./searchcursor", "../dialog/dialog"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function b(a, b) {
		return "string" == typeof a ? a = new RegExp(a.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&"), b ? "gi" : "g") : a.global || (a = new RegExp(a.source, a.ignoreCase ? "gi" : "g")), {
			token: function (b) {
				a.lastIndex = b.pos;
				var c = a.exec(b.string);
				return c && c.index == b.pos ? (b.pos += c[0].length, "searching") : (c ? b.pos = c.index : b.skipToEnd(), void 0)
			}
		}
	}

	function c() {
		this.posFrom = this.posTo = this.query = null, this.overlay = null
	}

	function d(a) {
		return a.state.search || (a.state.search = new c)
	}

	function e(a) {
		return "string" == typeof a && a == a.toLowerCase()
	}

	function f(a, b, c) {
		return a.getSearchCursor(b, c, e(b))
	}

	function g(a, b, c, d, e) {
		a.openDialog ? a.openDialog(b, e, {value: d}) : e(prompt(c, d))
	}

	function h(a, b, c, d) {
		a.openConfirm ? a.openConfirm(b, d) : confirm(c) && d[0]()
	}

	function i(a) {
		var b = a.match(/^\/(.*)\/([a-z]*)$/);
		return b ? (a = new RegExp(b[1], -1 == b[2].indexOf("i") ? "" : "i"), a.test("") && (a = /x^/)) : "" == a && (a = /x^/), a
	}

	function k(a, c) {
		var f = d(a);
		return f.query ? l(a, c) : (g(a, j, "Search for:", a.getSelection(), function (d) {
			a.operation(function () {
				d && !f.query && (f.query = i(d), a.removeOverlay(f.overlay, e(f.query)), f.overlay = b(f.query, e(f.query)), a.addOverlay(f.overlay), f.posFrom = f.posTo = a.getCursor(), l(a, c))
			})
		}), void 0)
	}

	function l(b, c) {
		b.operation(function () {
			var e = d(b), g = f(b, e.query, c ? e.posFrom : e.posTo);
			(g.find(c) || (g = f(b, e.query, c ? a.Pos(b.lastLine()) : a.Pos(b.firstLine(), 0)), g.find(c))) && (b.setSelection(g.from(), g.to()), b.scrollIntoView({
				from: g.from(),
				to: g.to()
			}), e.posFrom = g.from(), e.posTo = g.to())
		})
	}

	function m(a) {
		a.operation(function () {
			var b = d(a);
			b.query && (b.query = null, a.removeOverlay(b.overlay))
		})
	}

	function q(a, b) {
		g(a, n, "Replace:", a.getSelection(), function (c) {
			c && (c = i(c), g(a, o, "Replace with:", "", function (d) {
				if (b)a.operation(function () {
					for (var b = f(a, c); b.findNext();)if ("string" != typeof c) {
						var e = a.getRange(b.from(), b.to()).match(c);
						b.replace(d.replace(/\$(\d)/g, function (a, b) {
							return e[b]
						}))
					} else b.replace(d)
				}); else {
					m(a);
					var e = f(a, c, a.getCursor()), g = function () {
						var d, b = e.from();
						!(d = e.findNext()) && (e = f(a, c), !(d = e.findNext()) || b && e.from().line == b.line && e.from().ch == b.ch) || (a.setSelection(e.from(), e.to()), a.scrollIntoView({
							from: e.from(),
							to: e.to()
						}), h(a, p, "Replace?", [function () {
							i(d)
						}, g]))
					}, i = function (a) {
						e.replace("string" == typeof c ? d : d.replace(/\$(\d)/g, function (b, c) {
							return a[c]
						})), g()
					};
					g()
				}
			}))
		})
	}

	var j = 'Search: <input type="text" style="width: 10em"/> <span style="color: #888">(Use /re/ syntax for regexp search)</span>', n = 'Replace: <input type="text" style="width: 10em"/> <span style="color: #888">(Use /re/ syntax for regexp search)</span>', o = 'With: <input type="text" style="width: 10em"/>', p = "Replace? <button>Yes</button> <button>No</button> <button>Stop</button>";
	a.commands.find = function (a) {
		m(a), k(a)
	}, a.commands.findNext = k, a.commands.findPrev = function (a) {
		k(a, !0)
	}, a.commands.clearSearch = m, a.commands.replace = q, a.commands.replaceAll = function (a) {
		q(a, !0)
	}
}), function (a) {
	"object" == typeof exports && "object" == typeof module ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror)
}(function (a) {
	"use strict";
	function c(a, c, e, f) {
		if (this.atOccurrence = !1, this.doc = a, null == f && "string" == typeof c && (f = !1), e = e ? a.clipPos(e) : b(0, 0), this.pos = {
				from: e,
				to: e
			}, "string" != typeof c)c.global || (c = new RegExp(c.source, c.ignoreCase ? "ig" : "g")), this.matches = function (d, e) {
			if (d) {
				c.lastIndex = 0;
				for (var h, i, f = a.getLine(e.line).slice(0, e.ch), g = 0; ;) {
					c.lastIndex = g;
					var j = c.exec(f);
					if (!j)break;
					if (h = j, i = h.index, g = h.index + (h[0].length || 1), g == f.length)break
				}
				var k = h && h[0].length || 0;
				k || (0 == i && 0 == f.length ? h = void 0 : i != a.getLine(e.line).length && k++)
			} else {
				c.lastIndex = e.ch;
				var f = a.getLine(e.line), h = c.exec(f), k = h && h[0].length || 0, i = h && h.index;
				i + k == f.length || k || (k = 1)
			}
			return h && k ? {from: b(e.line, i), to: b(e.line, i + k), match: h} : void 0
		}; else {
			var g = c;
			f && (c = c.toLowerCase());
			var h = f ? function (a) {
				return a.toLowerCase()
			} : function (a) {
				return a
			}, i = c.split("\n");
			if (1 == i.length)this.matches = c.length ? function (e, f) {
				if (e) {
					var i = a.getLine(f.line).slice(0, f.ch), j = h(i), k = j.lastIndexOf(c);
					if (k > -1)return k = d(i, j, k), {from: b(f.line, k), to: b(f.line, k + g.length)}
				} else {
					var i = a.getLine(f.line).slice(f.ch), j = h(i), k = j.indexOf(c);
					if (k > -1)return k = d(i, j, k) + f.ch, {from: b(f.line, k), to: b(f.line, k + g.length)}
				}
			} : function () {
			}; else {
				var j = g.split("\n");
				this.matches = function (c, d) {
					var e = i.length - 1;
					if (c) {
						if (d.line - (i.length - 1) < a.firstLine())return;
						if (h(a.getLine(d.line).slice(0, j[e].length)) != i[i.length - 1])return;
						for (var f = b(d.line, j[e].length), g = d.line - 1, k = e - 1; k >= 1; --k, --g)if (i[k] != h(a.getLine(g)))return;
						var l = a.getLine(g), m = l.length - j[0].length;
						if (h(l.slice(m)) != i[0])return;
						return {from: b(g, m), to: f}
					}
					if (!(d.line + (i.length - 1) > a.lastLine())) {
						var l = a.getLine(d.line), m = l.length - j[0].length;
						if (h(l.slice(m)) == i[0]) {
							for (var n = b(d.line, m), g = d.line + 1, k = 1; e > k; ++k, ++g)if (i[k] != h(a.getLine(g)))return;
							if (a.getLine(g).slice(0, j[e].length) == i[e])return {from: n, to: b(g, j[e].length)}
						}
					}
				}
			}
		}
	}

	function d(a, b, c) {
		if (a.length == b.length)return c;
		for (var d = Math.min(c, a.length); ;) {
			var e = a.slice(0, d).toLowerCase().length;
			if (c > e)++d; else {
				if (!(e > c))return d;
				--d
			}
		}
	}

	var b = a.Pos;
	c.prototype = {
		findNext:        function () {
			return this.find(!1)
		}, findPrevious: function () {
			return this.find(!0)
		}, find:         function (a) {
			function e(a) {
				var d = b(a, 0);
				return c.pos = {from: d, to: d}, c.atOccurrence = !1, !1
			}

			for (var c = this, d = this.doc.clipPos(a ? this.pos.from : this.pos.to); ;) {
				if (this.pos = this.matches(a, d))return this.atOccurrence = !0, this.pos.match || !0;
				if (a) {
					if (!d.line)return e(0);
					d = b(d.line - 1, this.doc.getLine(d.line - 1).length)
				} else {
					var f = this.doc.lineCount();
					if (d.line == f - 1)return e(f);
					d = b(d.line + 1, 0)
				}
			}
		}, from:         function () {
			return this.atOccurrence ? this.pos.from : void 0
		}, to:           function () {
			return this.atOccurrence ? this.pos.to : void 0
		}, replace:      function (c) {
			if (this.atOccurrence) {
				var d = a.splitLines(c);
				this.doc.replaceRange(d, this.pos.from, this.pos.to), this.pos.to = b(this.pos.from.line + d.length - 1, d[d.length - 1].length + (1 == d.length ? this.pos.from.ch : 0))
			}
		}
	}, a.defineExtension("getSearchCursor", function (a, b, d) {
		return new c(this.doc, a, b, d)
	}), a.defineDocExtension("getSearchCursor", function (a, b, d) {
		return new c(this, a, b, d)
	}), a.defineExtension("selectMatches", function (b, c) {
		for (var e, d = [], f = this.getSearchCursor(b, this.getCursor("from"), c); (e = f.findNext()) && !(a.cmpPos(f.to(), this.getCursor("to")) > 0);)d.push({
			anchor: f.from(),
			head: f.to()
		});
		d.length && this.setSelections(d, 0)
	})
});
