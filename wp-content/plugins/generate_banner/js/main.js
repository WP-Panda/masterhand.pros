function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            } else {
                var old = tempArray[i].split('=')[1];
            }
        }
    }

    if( typeof old !== 'undefined') {
        var rows_txt = temp + "" + param + "=" + old + paramVal;
    } else {
        var rows_txt = temp;
    }

    return baseURL + "?" + newAdditionalURL + rows_txt;
}

!function (e) {
    var t = {};

    function n(r) {
        if (t[r]) return t[r].exports;
        var o = t[r] = {i: r, l: !1, exports: {}};
        return e[r].call(o.exports, o, o.exports, n), o.l = !0, o.exports
    }

    n.m = e, n.c = t, n.d = function (e, t, r) {
        n.o(e, t) || Object.defineProperty(e, t, {enumerable: !0, get: r})
    }, n.r = function (e) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
    }, n.t = function (e, t) {
        if (1 & t && (e = n(e)), 8 & t) return e;
        if (4 & t && "object" == typeof e && e && e.__esModule) return e;
        var r = Object.create(null);
        if (n.r(r), Object.defineProperty(r, "default", {
            enumerable: !0,
            value: e
        }), 2 & t && "string" != typeof e) for (var o in e) n.d(r, o, function (t) {
            return e[t]
        }.bind(null, o));
        return r
    }, n.n = function (e) {
        var t = e && e.__esModule ? function () {
            return e.default
        } : function () {
            return e
        };
        return n.d(t, "a", t), t
    }, n.o = function (e, t) {
        return Object.prototype.hasOwnProperty.call(e, t)
    }, n.p = "", n(n.s = 4)
}([function (e, t, n) {
    "use strict";
    Object.defineProperty(t, "__esModule", {value: !0});
    var r, o = "application/font-woff", i = {
        woff: o,
        woff2: o,
        ttf: "application/font-truetype",
        eot: "application/vnd.ms-fontobject",
        png: "image/png",
        jpg: "image/jpeg",
        jpeg: "image/jpeg",
        gif: "image/gif",
        tiff: "image/tiff",
        svg: "image/svg+xml"
    };

    function u(e) {
        var t = /\.([^./]*?)$/g.exec(e);
        return t ? t[1] : ""
    }

    function a(e) {
        var t = u(e).toLowerCase();
        return i[t] || ""
    }

    function c(e) {
        return new Promise((function (t, n) {
            var r = new Image;
            r.onload = function () {
                t(r)
            }, r.onerror = n, r.crossOrigin = "anonymous", r.src = e
        }))
    }

    function s(e) {
        return e.split(/,/)[1]
    }

    function l(e, t) {
        var n = window.getComputedStyle(e).getPropertyValue(t);
        return parseFloat(n.replace("px", ""))
    }

    function f() {
        return window.devicePixelRatio || 1
    }

    t.uuid = (r = 0, function () {
        return r += 1, "u" + ("0000" + (Math.random() * Math.pow(36, 4) << 0).toString(36)).slice(-4) + r
    }), t.parseExtension = u, t.getMimeType = a, t.delay = function (e) {
        return function (t) {
            return new Promise((function (n) {
                setTimeout((function () {
                    n(t)
                }), e)
            }))
        }
    }, t.createImage = c, t.isDataUrl = function (e) {
        return -1 !== e.search(/^(data:)/)
    }, t.toDataURL = function (e, t) {
        return "data:" + t + ";base64," + e
    }, t.getDataURLContent = s, t.canvasToBlob = function (e) {
        return e.toBlob ? new Promise((function (t) {
            e.toBlob(t)
        })) : function (e) {
            return new Promise((function (t) {
                for (var n = window.atob(e.toDataURL().split(",")[1]), r = n.length, o = new Uint8Array(r), i = 0; i < r; i += 1) o[i] = n.charCodeAt(i);
                t(new Blob([o], {type: "image/png"}))
            }))
        }(e)
    }, t.toArray = function (e) {
        for (var t = [], n = 0, r = e.length; n < r; n += 1) t.push(e[n]);
        return t
    }, t.getNodeWidth = function (e) {
        var t = l(e, "border-left-width"), n = l(e, "border-right-width");
        return e.scrollWidth + t + n
    }, t.getNodeHeight = function (e) {
        var t = l(e, "border-top-width"), n = l(e, "border-bottom-width");
        return e.scrollHeight + t + n
    }, t.getPixelRatio = f, t.svgToDataURL = function (e) {
        return Promise.resolve().then((function () {
            return (new XMLSerializer).serializeToString(e)
        })).then(encodeURIComponent).then((function (e) {
            return "data:image/svg+xml;charset=utf-8," + e
        }))
    }, t.getBlobFromImageURL = function (e) {
        return c(e).then((function (t) {
            var n = t.width, r = t.height, o = document.createElement("canvas"), i = o.getContext("2d"), u = f();
            return o.width = n * u, o.height = r * u, o.style.width = "" + n, o.style.height = "" + r, i.scale(u, u), i.drawImage(t, 0, 0), s(o.toDataURL(a(e)))
        }))
    }
}, function (e, t, n) {
    "use strict";
    var r = this && this.__importDefault || function (e) {
        return e && e.__esModule ? e : {default: e}
    };
    Object.defineProperty(t, "__esModule", {value: !0});
    var o = r(n(2)), i = n(0), u = /url\((['"]?)([^'"]+?)\1\)/g;

    function a(e) {
        var t = [];
        return e.replace(u, (function (e, n, r) {
            return t.push(r), e
        })), t.filter((function (e) {
            return !i.isDataUrl(e)
        }))
    }

    function c(e, t, n, r) {
        var u = n ? function (e, t) {
            if (e.match(/^[a-z]+:\/\//i)) return e;
            if (e.match(/^\/\//)) return window.location.protocol + e;
            if (e.match(/^[a-z]+:/i)) return e;
            var n = document.implementation.createHTMLDocument(), r = n.createElement("base"), o = n.createElement("a");
            return n.head.appendChild(r), n.body.appendChild(o), t && (r.href = t), o.href = e, o.href
        }(t, n) : t;
        return Promise.resolve(u).then((function (e) {
            return o.default(e, r)
        })).then((function (e) {
            return i.toDataURL(e, i.getMimeType(t))
        })).then((function (n) {
            return e.replace(new RegExp("(url\\(['\"]?)(" + function (e) {
                return e.replace(/([.*+?^${}()|\[\]\/\\])/g, "\\$1")
            }(t) + ")(['\"]?\\))", "g"), "$1" + n + "$3")
        })).then((function (e) {
            return e
        }), (function () {
            return u
        }))
    }

    function s(e) {
        return -1 !== e.search(u)
    }

    t.shouldEmbed = s, t.default = function (e, t, n) {
        return s(e) ? Promise.resolve(e).then(a).then((function (r) {
            return r.reduce((function (e, r) {
                return e.then((function (e) {
                    return c(e, r, t, n)
                }))
            }), Promise.resolve(e))
        })) : Promise.resolve(e)
    }
}, function (e, t, n) {
    "use strict";
    Object.defineProperty(t, "__esModule", {value: !0});
    var r = n(0), o = 3e4;
    t.default = function (e, t) {
        return t.cacheBust && (e += (/\?/.test(e) ? "&" : "?") + (new Date).getTime()), (window.fetch ? window.fetch(e).then((function (e) {
            return e.blob()
        })).then((function (e) {
            return new Promise((function (t, n) {
                var r = new FileReader;
                r.onloadend = function () {
                    return t(r.result)
                }, r.onerror = n, r.readAsDataURL(e)
            }))
        })).then(r.getDataURLContent).catch((function () {
            return new Promise((function (e, t) {
                t()
            }))
        })) : new Promise((function (t, n) {
            var i = new XMLHttpRequest;
            i.onreadystatechange = function () {
                if (4 === i.readyState) if (200 === i.status) {
                    var o = new FileReader;
                    o.onloadend = function () {
                        t(r.getDataURLContent(o.result))
                    }, o.readAsDataURL(i.response)
                } else n(new Error("Failed to fetch resource: " + e + ", status: " + i.status))
            }, i.ontimeout = function () {
                n(new Error("Timeout of " + o + "ms occured while fetching resource: " + e))
            }, i.responseType = "blob", i.timeout = o, i.open("GET", e, !0), i.send()
        }))).catch((function (n) {
            var r = "";
            if (t.imagePlaceholder) {
                var o = t.imagePlaceholder.split(/,/);
                o && o[1] && (r = o[1])
            }
            var i = "Failed to fetch resource: " + e;
            return n && (i = "string" == typeof n ? n : n.message), i && console.error(i), r
        }))
    }
}, function (e, t, n) {
    "use strict";
    var r = this && this.__importDefault || function (e) {
        return e && e.__esModule ? e : {default: e}
    };
    Object.defineProperty(t, "__esModule", {value: !0});
    var o = r(n(5)), i = r(n(7)), u = r(n(8)), a = r(n(9)), c = r(n(10)), s = n(0);

    function l(e, t) {
        return void 0 === t && (t = {}), {width: t.width || s.getNodeWidth(e), height: t.height || s.getNodeHeight(e)}
    }

    function f(e, t) {
        void 0 === t && (t = {});
        var n = l(e, t), r = n.width, s = n.height;
        return o.default(e, t.filter, !0).then((function (e) {
            return i.default(e, t)
        })).then((function (e) {
            return u.default(e, t)
        })).then((function (e) {
            return c.default(e, t)
        })).then((function (e) {
            return a.default(e, r, s)
        }))
    }

    function d(e, t) {
        return void 0 === t && (t = {}), f(e, t).then(s.createImage).then(s.delay(100)).then((function (n) {
            var r = document.createElement("canvas"), o = r.getContext("2d"), i = s.getPixelRatio(), u = l(e, t),
                a = u.width, c = u.height;
            return r.width = a * i, r.height = c * i, r.style.width = "" + a, r.style.height = "" + c, o.scale(i, i), t.backgroundColor && (o.fillStyle = t.backgroundColor, o.fillRect(0, 0, r.width, r.height)), o.drawImage(n, 0, 0), r
        }))
    }

    function h(e, t) {
        void 0 === t && (t = {});
        var n = l(e, t), r = n.width, o = n.height;
        return d(e, t).then((function (e) {
            return e.getContext("2d").getImageData(0, 0, r, o).data
        }))
    }

    function m(e, t) {
        return void 0 === t && (t = {}), d(e, t).then((function (e) {
            return e.toDataURL()
        }))
    }

    function p(e, t) {
        return void 0 === t && (t = {}), d(e, t).then((function (e) {
            return e.toDataURL("image/jpeg", t.quality || 1)
        }))
    }

    function g(e, t) {
        return void 0 === t && (t = {}), d(e, t).then(s.canvasToBlob)
    }

    t.toSvgDataURL = f, t.toCanvas = d, t.toPixelData = h, t.toPng = m, t.toJpeg = p, t.toBlob = g, t.default = {
        toSvgDataURL: f,
        toCanvas: d,
        toPixelData: h,
        toPng: m,
        toJpeg: p,
        toBlob: g
    }
}, function (e, t, n) {
    "use strict";
    n.r(t);
    var r = n(3), o = n.n(r);
    n(11);
    document.addEventListener("DOMContentLoaded", (function () {
        for (var e = document.getElementsByClassName("getImgBtn"), t = function () {
            document.body.classList.add("processing");
            var e = event.target.dataset.name;
            let t = document.getElementById("content_" + e);
            o.a.toPng(t).then((function (t) {
                jQuery.ajax({
                    type: "POST",
                    url: "/wp-admin/admin-ajax.php",
                    data: {img_str: t, template: e, action: "save_banner"}
                }).done((function (e) {
                    if (document.body.classList.remove("processing"), "error" == e) alert("Error! No save image"); else {
                        var t = document.getElementsByClassName("img_show");
                        t[0].src =  e.data.img;
                        jQuery('.banner_form').find('.wpp-share-btn').each(function () {
                            var _url = jQuery(this).attr('href');
                            jQuery(this).attr('href', updateURLParameter( _url, "u", "?b=" + e.data.number ));
                            jQuery(this).attr('href', updateURLParameter( _url, "url", "?b=" + e.data.number ));
                        });
                        jQuery("#modal_banner").modal("show");
                    }
                }))
            }))
        }, n = 0; n < e.length; n++) e[n].addEventListener("click", t, !1)
    }))
}, function (e, t, n) {
    "use strict";
    var r = this && this.__importDefault || function (e) {
        return e && e.__esModule ? e : {default: e}
    };
    Object.defineProperty(t, "__esModule", {value: !0});
    var o = n(0), i = r(n(6));

    function u(e) {
        return e instanceof HTMLCanvasElement ? o.createImage(e.toDataURL()) : e.tagName && "svg" === e.tagName.toLowerCase() ? Promise.resolve(e).then((function (e) {
            return o.svgToDataURL(e)
        })).then(o.createImage) : Promise.resolve(e.cloneNode(!1))
    }

    function a(e, t) {
        return t instanceof Element ? Promise.resolve().then((function () {
            return function (e, t) {
                var n = window.getComputedStyle(e), r = t.style;
                n.cssText ? r.cssText = n.cssText : o.toArray(n).forEach((function (e) {
                    r.setProperty(e, n.getPropertyValue(e), n.getPropertyPriority(e))
                }))
            }(e, t)
        })).then((function () {
            return i.default(e, t)
        })).then((function () {
            return function (e, t) {
                e instanceof HTMLTextAreaElement && (t.innerHTML = e.value), e instanceof HTMLInputElement && t.setAttribute("value", e.value)
            }(e, t)
        })).then((function () {
            return t
        })) : t
    }

    function c(e, t, n) {
        return n || !t || t(e) ? Promise.resolve(e).then(u).then((function (n) {
            return function (e, t, n) {
                var r = o.toArray(e.childNodes);
                return 0 === r.length ? Promise.resolve(t) : r.reduce((function (e, r) {
                    return e.then((function () {
                        return c(r, n)
                    })).then((function (e) {
                        e && t.appendChild(e)
                    }))
                }), Promise.resolve()).then((function () {
                    return t
                }))
            }(e, n, t)
        })).then((function (t) {
            return a(e, t)
        })) : Promise.resolve(null)
    }

    t.default = c
}, function (e, t, n) {
    "use strict";
    Object.defineProperty(t, "__esModule", {value: !0});
    var r = n(0);

    function o(e, t, n) {
        var o = "." + e + ":" + t, i = n.cssText ? function (e) {
            var t = e.getPropertyValue("content");
            return e.cssText + " content: " + t + ";"
        }(n) : function (e) {
            return r.toArray(e).map((function (t) {
                return t + ": " + e.getPropertyValue(t) + (e.getPropertyPriority(t) ? " !important" : "") + ";"
            })).join(" ")
        }(n);
        return document.createTextNode(o + "{" + i + "}")
    }

    t.default = function (e, t) {
        [":before", ":after"].forEach((function (n) {
            return function (e, t, n) {
                var i = window.getComputedStyle(e, n), u = i.getPropertyValue("content");
                if ("" !== u && "none" !== u) {
                    var a = r.uuid(), c = document.createElement("style");
                    c.appendChild(o(a, n, i)), t.className = t.className + " " + a, t.appendChild(c)
                }
            }(e, t, n)
        }))
    }
}, function (e, t, n) {
    "use strict";
    var r = this && this.__importStar || function (e) {
        if (e && e.__esModule) return e;
        var t = {};
        if (null != e) for (var n in e) Object.hasOwnProperty.call(e, n) && (t[n] = e[n]);
        return t.default = e, t
    };
    Object.defineProperty(t, "__esModule", {value: !0});
    var o = n(0), i = r(n(1));

    function u(e) {
        if (void 0 === e) return [];
        var t = e, n = [], r = new RegExp("(\\/\\*[\\s\\S]*?\\*\\/)", "gi");
        t = t.replace(r, "");
        for (var o, i = new RegExp("((@.*?keyframes [\\s\\S]*?){([\\s\\S]*?}\\s*?)})", "gi"); null !== (o = i.exec(t));) n.push(o[0]);
        t = t.replace(i, "");
        for (var u = new RegExp("((\\s*?(?:\\/\\*[\\s\\S]*?\\*\\/)?\\s*?@media[\\s\\S]*?){([\\s\\S]*?)}\\s*?})|(([\\s\\S]*?){([\\s\\S]*?)})", "gi"); null !== (o = u.exec(t));) n.push(o[0]);
        return n
    }

    function a(e, t) {
        return fetch(e).then((function (t) {
            return {url: e, cssText: t.text()}
        }), (function (e) {
            console.log("ERROR FETCHING CSS: ", e.toString())
        }))
    }

    function c(e) {
        return e.cssText.then((function (t) {
            var n = t, r = (n.match(/url\([^)]+\)/g) || []).map((function (t) {
                var r = t.replace(/url\(([^]+)\)/g, "$1");
                if (!r.startsWith("https://")) {
                    var o = e.url;
                    r = new URL(r, o).href
                }
                return new Promise((function (e, o) {
                    fetch(r).then((function (e) {
                        return e.blob()
                    })).then((function (r) {
                        var o = new FileReader;
                        o.addEventListener("load", (function (r) {
                            n = n.replace(t, "url(" + o.result + ")"), e([t, o.result])
                        })), o.readAsDataURL(r)
                    })).catch(o)
                }))
            }));
            return Promise.all(r).then((function () {
                return n
            }))
        }))
    }

    function s(e) {
        var t = [], n = [];
        return e.forEach((function (t) {
            if ("cssRules" in t) try {
                o.toArray(t.cssRules).forEach((function (e) {
                    e.type === CSSRule.IMPORT_RULE && n.push(a(e.href).then(c).then((function (e) {
                        u(e).forEach((function (e) {
                            t.insertRule(e, t.cssRules.length)
                        }))
                    })).catch((function (e) {
                        console.log("Error loading remote css", e.toString())
                    })))
                }))
            } catch (o) {
                var r = e.find((function (e) {
                    return null === e.href
                })) || document.styleSheets[0];
                null != t.href && n.push(a(t.href).then(c).then((function (e) {
                    u(e).forEach((function (e) {
                        r.insertRule(e, t.cssRules.length)
                    }))
                })).catch((function (e) {
                    console.log("Error loading remote stylesheet", e.toString())
                }))), console.log("Error inlining remote css file", o.toString())
            }
        })), Promise.all(n).then((function () {
            return e.forEach((function (e) {
                if ("cssRules" in e) try {
                    o.toArray(e.cssRules).forEach((function (e) {
                        t.push(e)
                    }))
                } catch (t) {
                    console.log("Error while reading CSS rules from " + e.href, t.toString())
                }
            })), t
        }))
    }

    function l(e) {
        return e.filter((function (e) {
            return e.type === CSSRule.FONT_FACE_RULE
        })).filter((function (e) {
            return i.shouldEmbed(e.style.getPropertyValue("src"))
        }))
    }

    function f(e) {
        return new Promise((function (t, n) {
            e.ownerDocument || n(new Error("Provided element is not within a Document")), t(o.toArray(e.ownerDocument.styleSheets))
        })).then(s).then(l)
    }

    t.parseWebFontRules = f, t.default = function (e, t) {
        return f(e).then((function (e) {
            return Promise.all(e.map((function (e) {
                var n = e.parentStyleSheet ? e.parentStyleSheet.href : null;
                return i.default(e.cssText, n, t)
            })))
        })).then((function (e) {
            return e.join("\n")
        })).then((function (t) {
            var n = document.createElement("style"), r = document.createTextNode(t);
            return n.appendChild(r), e.firstChild ? e.insertBefore(n, e.firstChild) : e.appendChild(n), e
        }))
    }
}, function (e, t, n) {
    "use strict";
    var r = this && this.__importDefault || function (e) {
        return e && e.__esModule ? e : {default: e}
    };
    Object.defineProperty(t, "__esModule", {value: !0});
    var o = n(0), i = r(n(2)), u = r(n(1));

    function a(e, t) {
        return e instanceof Element ? Promise.resolve(e).then((function (e) {
            return function (e, t) {
                var n = e.style.getPropertyValue("background");
                return n ? Promise.resolve(n).then((function (e) {
                    return u.default(e, null, t)
                })).then((function (t) {
                    return e.style.setProperty("background", t, e.style.getPropertyPriority("background")), e
                })) : Promise.resolve(e)
            }(e, t)
        })).then((function (e) {
            return function (e, t) {
                return e instanceof HTMLImageElement && !o.isDataUrl(e.src) ? Promise.resolve(e.src).then((function (e) {
                    return i.default(e, t)
                })).then((function (t) {
                    return o.toDataURL(t, o.getMimeType(e.src))
                })).then((function (t) {
                    return new Promise((function (n, r) {
                        e.onload = n, e.onerror = r, e.src = t
                    }))
                })).then((function () {
                    return e
                }), (function () {
                    return e
                })) : Promise.resolve(e)
            }(e, t)
        })).then((function (e) {
            return function (e, t) {
                var n = o.toArray(e.childNodes).map((function (e) {
                    return a(e, t)
                }));
                return Promise.all(n).then((function () {
                    return e
                }))
            }(e, t)
        })) : Promise.resolve(e)
    }

    t.default = a
}, function (e, t, n) {
    "use strict";
    Object.defineProperty(t, "__esModule", {value: !0});
    var r = n(0);
    t.default = function (e, t, n) {
        var o = "http://www.w3.org/2000/svg", i = document.createElementNS(o, "svg"),
            u = document.createElementNS(o, "foreignObject");
        return i.setAttributeNS("", "width", "" + t), i.setAttributeNS("", "height", "" + n), u.setAttributeNS("", "width", "100%"), u.setAttributeNS("", "height", "100%"), u.setAttributeNS("", "x", "0"), u.setAttributeNS("", "y", "0"), u.setAttributeNS("", "externalResourcesRequired", "true"), i.appendChild(u), u.appendChild(e), r.svgToDataURL(i)
    }
}, function (e, t, n) {
    "use strict";
    Object.defineProperty(t, "__esModule", {value: !0}), t.default = function (e, t) {
        var n = e.style;
        return t.backgroundColor && (n.backgroundColor = t.backgroundColor), t.width && (n.width = t.width + "px"), t.height && (n.height = t.height + "px"), t.style && Object.assign(n, t.style), e
    }
}, function (e, t, n) {
    (function (n) {
        var r, o, i;
        o = [], void 0 === (i = "function" == typeof(r = function () {
            "use strict";

            function t(e, t, n) {
                var r = new XMLHttpRequest;
                r.open("GET", e), r.responseType = "blob", r.onload = function () {
                    u(r.response, t, n)
                }, r.onerror = function () {
                    console.error("could not download file")
                }, r.send()
            }

            function r(e) {
                var t = new XMLHttpRequest;
                t.open("HEAD", e, !1);
                try {
                    t.send()
                } catch (e) {
                }
                return 200 <= t.status && 299 >= t.status
            }

            function o(e) {
                try {
                    e.dispatchEvent(new MouseEvent("click"))
                } catch (n) {
                    var t = document.createEvent("MouseEvents");
                    t.initMouseEvent("click", !0, !0, window, 0, 0, 0, 80, 20, !1, !1, !1, !1, 0, null), e.dispatchEvent(t)
                }
            }

            var i = "object" == typeof window && window.window === window ? window : "object" == typeof self && self.self === self ? self : "object" == typeof n && n.global === n ? n : void 0,
                u = i.saveAs || ("object" != typeof window || window !== i ? function () {
                } : "download" in HTMLAnchorElement.prototype ? function (e, n, u) {
                    var a = i.URL || i.webkitURL, c = document.createElement("a");
                    n = n || e.name || "download", c.download = n, c.rel = "noopener", "string" == typeof e ? (c.href = e, c.origin === location.origin ? o(c) : r(c.href) ? t(e, n, u) : o(c, c.target = "_blank")) : (c.href = a.createObjectURL(e), setTimeout((function () {
                        a.revokeObjectURL(c.href)
                    }), 4e4), setTimeout((function () {
                        o(c)
                    }), 0))
                } : "msSaveOrOpenBlob" in navigator ? function (e, n, i) {
                    if (n = n || e.name || "download", "string" != typeof e) navigator.msSaveOrOpenBlob(function (e, t) {
                        return void 0 === t ? t = {autoBom: !1} : "object" != typeof t && (console.warn("Deprecated: Expected third argument to be a object"), t = {autoBom: !t}), t.autoBom && /^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(e.type) ? new Blob(["\ufeff", e], {type: e.type}) : e
                    }(e, i), n); else if (r(e)) t(e, n, i); else {
                        var u = document.createElement("a");
                        u.href = e, u.target = "_blank", setTimeout((function () {
                            o(u)
                        }))
                    }
                } : function (e, n, r, o) {
                    if ((o = o || open("", "_blank")) && (o.document.title = o.document.body.innerText = "downloading..."), "string" == typeof e) return t(e, n, r);
                    var u = "application/octet-stream" === e.type, a = /constructor/i.test(i.HTMLElement) || i.safari,
                        c = /CriOS\/[\d]+/.test(navigator.userAgent);
                    if ((c || u && a) && "object" == typeof FileReader) {
                        var s = new FileReader;
                        s.onloadend = function () {
                            var e = s.result;
                            e = c ? e : e.replace(/^data:[^;]*;/, "data:attachment/file;"), o ? o.location.href = e : location = e, o = null
                        }, s.readAsDataURL(e)
                    } else {
                        var l = i.URL || i.webkitURL, f = l.createObjectURL(e);
                        o ? o.location = f : location.href = f, o = null, setTimeout((function () {
                            l.revokeObjectURL(f)
                        }), 4e4)
                    }
                });
            i.saveAs = u.saveAs = u, e.exports = u
        }) ? r.apply(t, o) : r) || (e.exports = i)
    }).call(this, n(12))
}, function (e, t) {
    var n;
    n = function () {
        return this
    }();
    try {
        n = n || new Function("return this")()
    } catch (e) {
        "object" == typeof window && (n = window)
    }
    e.exports = n
}]);