/*
 * pop up for google map marker
 */
function InfoBubble(e) {
    this.extend(InfoBubble, google.maps.OverlayView);
    this.baseZIndex_ = 100;
    this.isOpen_ = false;
    var t = e || {};
    if (t["backgroundColor"] == undefined) {
        t["backgroundColor"] = this.BACKGROUND_COLOR_
    }
    if (t["borderColor"] == undefined) {
        t["borderColor"] = this.BORDER_COLOR_
    }
    if (t["borderRadius"] == undefined) {
        t["borderRadius"] = this.BORDER_RADIUS_
    }
    if (t["borderWidth"] == undefined) {
        t["borderWidth"] = this.BORDER_WIDTH_
    }
    if (t["padding"] == undefined) {
        t["padding"] = this.PADDING_
    }
    if (t["arrowPosition"] == undefined) {
        t["arrowPosition"] = this.ARROW_POSITION_
    }
    if (t["minWidth"] == undefined) {
        t["minWidth"] = this.MIN_WIDTH_
    }    
    this.buildDom_();
    this.setValues(t)    
}
window["InfoBubble"] = InfoBubble;
InfoBubble.prototype.ARROW_SIZE_ = 15;
InfoBubble.prototype.ARROW_STYLE_ = 0;
InfoBubble.prototype.SHADOW_STYLE_ = 1;
InfoBubble.prototype.MIN_WIDTH_ = 50;
InfoBubble.prototype.ARROW_POSITION_ = 50;
InfoBubble.prototype.PADDING_ = 10;
InfoBubble.prototype.BORDER_WIDTH_ = 1;
InfoBubble.prototype.BORDER_COLOR_ = "#ccc";
InfoBubble.prototype.BORDER_RADIUS_ = 10;
InfoBubble.prototype.BACKGROUND_COLOR_ = "#fff";
InfoBubble.prototype.extend = function (e, t) {
    return function (e) {
        for (var t in e.prototype) {
            this.prototype[t] = e.prototype[t]
        }
        return this
    }.apply(e, [t])
};
InfoBubble.prototype.buildDom_ = function () {
    var e = this.bubble_ = document.createElement("DIV");
    e.style["position"] = "absolute";
    e.style["zIndex"] = this.baseZIndex_;
    var t = this.close_ = document.createElement("IMG");
    t.style["position"] = "absolute";
    t.style["width"] = this.px(12);
    t.style["height"] = this.px(12);
    t.style["border"] = 0;
    t.style["zIndex"] = this.baseZIndex_ + 1;
    t.style["cursor"] = "pointer";
    t.src = "http://maps.gstatic.com/intl/en_us/mapfiles/close.gif";
    var n = this;
    google.maps.event.addDomListener(t, "click", function () {
        n.close();
        google.maps.event.trigger(n, "closeclick")
    });
    var r = this.contentContainer_ = document.createElement("DIV");
    r.style["overflowX"] = "visible";
    r.style["overflowY"] = "visible";
    r.style["cursor"] = "default";
    r.style["clear"] = "both";
    r.style["position"] = "relative";
    r.className = "map_infobubble map_popup";
    var i = this.content_ = document.createElement("DIV");
    r.appendChild(i);
    var s = this.arrow_ = document.createElement("DIV");
    s.style["position"] = "relative";
    s.className = "map_infoarrow";
    var o = this.arrowOuter_ = document.createElement("DIV");
    var u = this.arrowInner_ = document.createElement("DIV");
    var a = this.getArrowSize_();
    o.style["position"] = u.style["position"] = "absolute";
    o.style["left"] = u.style["left"] = "50%";
    o.style["height"] = u.style["height"] = "0";
    o.style["width"] = u.style["width"] = "0";
    o.style["marginLeft"] = this.px(-a);
    o.style["borderWidth"] = this.px(a);
    o.style["borderBottomWidth"] = 0;
    var f = document.createElement("DIV");
    f.style["position"] = "absolute";
    e.style["display"] = f.style["display"] = "none";
    e.appendChild(t);
    e.appendChild(r);
    s.appendChild(o);
    s.appendChild(u);
    e.appendChild(s);
    var l = document.createElement("style");
    l.setAttribute("type", "text/css");
    var c = "";
    l.textContent = c;
    document.getElementsByTagName("head")[0].appendChild(l)
};
InfoBubble.prototype.setBackgroundClassName = function (e) {
    this.set("backgroundClassName", e)
};
InfoBubble.prototype["setBackgroundClassName"] = InfoBubble.prototype.setBackgroundClassName;
InfoBubble.prototype.getArrowStyle_ = function () {
    return parseInt(this.get("arrowStyle"), 10) || 0
};
InfoBubble.prototype.setArrowStyle = function (e) {
    this.set("arrowStyle", e)
};
InfoBubble.prototype["setArrowStyle"] = InfoBubble.prototype.setArrowStyle;
InfoBubble.prototype.getArrowSize_ = function () {
    return parseInt(this.get("arrowSize"), 10) || 0
};
InfoBubble.prototype.getArrowPosition_ = function () {
    return parseInt(this.get("arrowPosition"), 10) || 0
};
InfoBubble.prototype.setZIndex = function (e) {
    this.set("zIndex", e)
};
InfoBubble.prototype["setZIndex"] = InfoBubble.prototype.setZIndex;
InfoBubble.prototype.getZIndex = function () {
    return parseInt(this.get("zIndex"), 10) || this.baseZIndex_
};
InfoBubble.prototype.setShadowStyle = function (e) {
    this.set("shadowStyle", e)
};
InfoBubble.prototype["setShadowStyle"] = InfoBubble.prototype.setShadowStyle;
InfoBubble.prototype.getShadowStyle_ = function () {
    return parseInt(this.get("shadowStyle"), 10) || 0
};
InfoBubble.prototype.showCloseButton = function () {
    this.set("hideCloseButton", false)
};
InfoBubble.prototype["showCloseButton"] = InfoBubble.prototype.showCloseButton;
InfoBubble.prototype.hideCloseButton = function () {
    this.set("hideCloseButton", true)
};
InfoBubble.prototype["hideCloseButton"] = InfoBubble.prototype.hideCloseButton;
InfoBubble.prototype.getBorderRadius_ = function () {
    return parseInt(this.get("borderRadius"), 10) || 0
};
InfoBubble.prototype.getBorderWidth_ = function () {
    return parseInt(this.get("borderWidth"), 10) || 0
};
InfoBubble.prototype.setBorderWidth = function (e) {
    this.set("borderWidth", e)
};
InfoBubble.prototype["setBorderWidth"] = InfoBubble.prototype.setBorderWidth;
InfoBubble.prototype.getPadding_ = function () {
    return parseInt(this.get("padding"), 10) || 0
};
InfoBubble.prototype.px = function (e) {
    if (e) {
        return e + "px"
    }
    return e
};
InfoBubble.prototype.addEvents_ = function () {
    var e = ["mousedown", "mousemove", "mouseover", "mouseout", "mouseup", "mousewheel", "DOMMouseScroll", "touchstart", "touchend", "touchmove", "dblclick", "contextmenu", "click"];
    var t = this.bubble_;
    this.listeners_ = [];
    for (var n = 0, r; r = e[n]; n++) {
        this.listeners_.push(google.maps.event.addDomListener(t, r, function (e) {
            e.cancelBubble = true;
            if (e.stopPropagation) {
                e.stopPropagation()
            }
        }))
    }
};
InfoBubble.prototype.onAdd = function () {
    if (!this.bubble_) {
        this.buildDom_()
    }
    this.addEvents_();
    var e = this.getPanes();
    if (e) {
        e.floatPane.appendChild(this.bubble_);        
    }
};
InfoBubble.prototype["onAdd"] = InfoBubble.prototype.onAdd;
InfoBubble.prototype.draw = function () {
    var e = this.getProjection();
    if (!e) {
        return
    }
    var t = this.get("position");
    if (!t) {
        this.close();
        return
    }
    var n = 0;
    var r = this.getAnchorHeight_();
    var i = this.getArrowSize_();
    var s = this.getArrowPosition_();
    s = s / 100;
    var o = e.fromLatLngToDivPixel(t);
    var u = this.contentContainer_.offsetWidth;
    var a = this.bubble_.offsetHeight;
    if (!u) {
        return
    }
    var f = o.y - (a + i);
    if (r) {
        f -= r
    }
    var l = o.x - u * s;
    this.bubble_.style["top"] = this.px(f);
    this.bubble_.style["left"] = this.px(l);    
};
InfoBubble.prototype["draw"] = InfoBubble.prototype.draw;
InfoBubble.prototype.onRemove = function () {
    if (this.bubble_ && this.bubble_.parentNode) {
        this.bubble_.parentNode.removeChild(this.bubble_)
    }
    
    for (var e = 0, t; t = this.listeners_[e]; e++) {
        google.maps.event.removeListener(t)
    }
};
InfoBubble.prototype["onRemove"] = InfoBubble.prototype.onRemove;
InfoBubble.prototype.isOpen = function () {
    return this.isOpen_
};
InfoBubble.prototype["isOpen"] = InfoBubble.prototype.isOpen;
InfoBubble.prototype.close = function () {
    if (this.bubble_) {
        this.bubble_.style["display"] = "none"
    }
    
    this.isOpen_ = false
};
InfoBubble.prototype["close"] = InfoBubble.prototype.close;
InfoBubble.prototype.open = function (e, t) {
    var n = this;
    window.setTimeout(function () {
        n.open_(e, t)
    }, 0)
};
InfoBubble.prototype.open_ = function (e, t) {
    this.updateContent_();
    if (e) {
        this.setMap(e)
    }
    if (t) {
        this.set("anchor", t);
        this.bindTo("anchorPoint", t);
        this.bindTo("position", t)
    }
    this.bubble_.style["display"] = "";
    this.redraw_();
    this.isOpen_ = true;
    var n = !this.get("disableAutoPan");
    if (n) {
        var r = this;
        window.setTimeout(function () {
            r.panToView()
        }, 200)
    }
};
InfoBubble.prototype["open"] = InfoBubble.prototype.open;
InfoBubble.prototype.setPosition = function (e) {
    if (e) {
        this.set("position", e)
    }
};
InfoBubble.prototype["setPosition"] = InfoBubble.prototype.setPosition;
InfoBubble.prototype.getPosition = function () {
    return this.get("position")
};
InfoBubble.prototype["getPosition"] = InfoBubble.prototype.getPosition;
InfoBubble.prototype.panToView = function () {
    var e = this.getProjection();
    if (!e) {
        return
    }
    if (!this.bubble_) {
        return
    }
    var t = this.getAnchorHeight_();
    var n = this.bubble_.offsetHeight + t;
    var r = this.get("map");
    var i = r.getDiv();
    var s = i.offsetHeight;
    var o = this.getPosition();
    var u = e.fromLatLngToContainerPixel(r.getCenter());
    var a = e.fromLatLngToContainerPixel(o);
    var f = u.y - n;
    var l = s - u.y;
    var c = f < 0;
    var h = 0;
    if (c) {
        f *= -1;
        h = (f + l) / 2
    }
    a.y -= h;
    o = e.fromContainerPixelToLatLng(a);
    if (r.getCenter() != o) {
        r.panTo(o)
    }
};
InfoBubble.prototype["panToView"] = InfoBubble.prototype.panToView;
InfoBubble.prototype.htmlToDocumentFragment_ = function (e) {
    e = e.replace(/^\s*([\S\s]*)\b\s*$/, "$1");
    var t = document.createElement("DIV");
    t.innerHTML = e;
    if (t.childNodes.length == 1) {
        return t.removeChild(t.firstChild)
    } else {
        var n = document.createDocumentFragment();
        while (t.firstChild) {
            n.appendChild(t.firstChild)
        }
        return n
    }
};
InfoBubble.prototype.removeChildren_ = function (e) {
    if (!e) {
        return
    }
    var t;
    while (t = e.firstChild) {
        e.removeChild(t)
    }
};
InfoBubble.prototype.setContent = function (e) {
    this.set("content", e)
};
InfoBubble.prototype["setContent"] = InfoBubble.prototype.setContent;
InfoBubble.prototype.getContent = function () {
    return this.get("content")
};
InfoBubble.prototype["getContent"] = InfoBubble.prototype.getContent;
InfoBubble.prototype.updateContent_ = function () {
    if (!this.content_) {
        return
    }
    this.removeChildren_(this.content_);
    var e = this.getContent();
    if (e) {
        if (typeof e == "string") {
            e = this.htmlToDocumentFragment_(e)
        }
        this.content_.appendChild(e);
        var t = this;
        var n = this.content_.getElementsByTagName("IMG");
        for (var r = 0, i; i = n[r]; r++) {          
            google.maps.event.addDomListener(i, "load", function () {
                t.imageLoaded_()
            })
        }
        google.maps.event.trigger(this, "domready")
    }
    this.redraw_()
};
InfoBubble.prototype.imageLoaded_ = function () {
    var e = !this.get("disableAutoPan");
    this.redraw_()
};
InfoBubble.prototype.setMaxWidth = function (e) {
    this.set("maxWidth", e)
};
InfoBubble.prototype["setMaxWidth"] = InfoBubble.prototype.setMaxWidth;
InfoBubble.prototype.setMaxHeight = function (e) {
    this.set("maxHeight", e)
};
InfoBubble.prototype["setMaxHeight"] = InfoBubble.prototype.setMaxHeight;
InfoBubble.prototype.setMinWidth = function (e) {
    this.set("minWidth", e)
};
InfoBubble.prototype["setMinWidth"] = InfoBubble.prototype.setMinWidth;
InfoBubble.prototype.setMinHeight = function (e) {
    this.set("minHeight", e)
};
InfoBubble.prototype["setMinHeight"] = InfoBubble.prototype.setMinHeight;
InfoBubble.prototype.getElementSize_ = function (e, t, n) {
    var r = document.createElement("DIV");
    r.style["display"] = "inline";
    r.style["position"] = "absolute";
    r.style["visibility"] = "hidden";
    if (typeof e == "string") {
        r.innerHTML = e
    } else {
        r.appendChild(e.cloneNode(true))
    }
    document.body.appendChild(r);
    var i = new google.maps.Size(r.offsetWidth, r.offsetHeight);
    if (t && i.width > t) {
        r.style["width"] = this.px(t);
        i = new google.maps.Size(r.offsetWidth, r.offsetHeight)
    }
    if (n && i.height > n) {
        r.style["height"] = this.px(n);
        i = new google.maps.Size(r.offsetWidth, r.offsetHeight)
    }
    document.body.removeChild(r);
    delete r;
    return i
};
InfoBubble.prototype.redraw_ = function () {
    this.figureOutSize_();
    this.positionCloseButton_();
    this.draw()
};
InfoBubble.prototype.figureOutSize_ = function () {
    var e = this.get("map");
    if (!e) {
        return
    }
    var t = this.getPadding_();
    var n = this.getBorderWidth_();
    var r = this.getBorderRadius_();
    var i = this.getArrowSize_();
    var s = e.getDiv();
    var o = i * 2;
    var u = s.offsetWidth - o;
    var a = s.offsetHeight - o - this.getAnchorHeight_();
    var f = 0;
    var l = this.get("minWidth") || 0;
    var c = this.get("minHeight") || 0;
    var h = this.get("maxWidth") || 0;
    var p = this.get("maxHeight") || 0;
    h = Math.min(u, h);
    p = Math.min(a, p);
    var d = 0;
    var v = this.get("content");
    if (typeof v == "string") {
        v = this.htmlToDocumentFragment_(v)
    }
    if (v) {
        var m = this.getElementSize_(v, h, p);
        if (l < m.width) {
            l = m.width
        }
        if (c < m.height) {
            c = m.height
        }
    }
    if (h) {
        l = Math.min(l, h)
    }
    if (p) {
        c = Math.min(c, p)
    }
    l = Math.max(l, d);
    if (l == d) {
        l = l + 2 * t
    }
    i = i * 2;
    l = Math.max(l, i);
    if (l > u) {
        l = u
    }
    if (c > a) {
        c = a - f
    }
    this.contentContainer_.style["width"] = this.px(l)
};
InfoBubble.prototype.getAnchorHeight_ = function () {
    var e = this.get("anchor");
    if (e) {
        var t = this.get("anchorPoint");
        if (t) {
            return -1 * t.y
        }
    }
    return 0
};
InfoBubble.prototype.positionCloseButton_ = function () {
    var e = this.getBorderRadius_();
    var t = this.getBorderWidth_();
    var n = 2;
    var r = 56;
    r += t;
    n += t;
    var i = this.contentContainer_;
    if (i && i.clientHeight < i.scrollHeight) {
        n += 15
    }
    this.close_.style["right"] = this.px(n);
    this.close_.style["top"] = this.px(r)
}