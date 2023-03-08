import { Head as $r, Link as Ir, usePage as Fr, router as re } from "@inertiajs/vue3";
import { defineComponent as Mr, computed as Ge, useAttrs as Tr, openBlock as Nr, createBlock as Cr, resolveDynamicComponent as Br, unref as ze, withModifiers as _r, withCtx as Ur, renderSlot as Dr } from "vue";
var Lr = typeof globalThis < "u" ? globalThis : typeof window < "u" ? window : typeof global < "u" ? global : typeof self < "u" ? self : {};
function Wr(r) {
  if (r.__esModule)
    return r;
  var e = r.default;
  if (typeof e == "function") {
    var t = function n() {
      if (this instanceof n) {
        var o = [null];
        o.push.apply(o, arguments);
        var a = Function.bind.apply(e, o);
        return new a();
      }
      return e.apply(this, arguments);
    };
    t.prototype = e.prototype;
  } else
    t = {};
  return Object.defineProperty(t, "__esModule", { value: !0 }), Object.keys(r).forEach(function(n) {
    var o = Object.getOwnPropertyDescriptor(r, n);
    Object.defineProperty(t, n, o.get ? o : {
      enumerable: !0,
      get: function() {
        return r[n];
      }
    });
  }), t;
}
var kr = function(e) {
  return jr(e) && !Gr(e);
};
function jr(r) {
  return !!r && typeof r == "object";
}
function Gr(r) {
  var e = Object.prototype.toString.call(r);
  return e === "[object RegExp]" || e === "[object Date]" || qr(r);
}
var zr = typeof Symbol == "function" && Symbol.for, Hr = zr ? Symbol.for("react.element") : 60103;
function qr(r) {
  return r.$$typeof === Hr;
}
function Vr(r) {
  return Array.isArray(r) ? [] : {};
}
function ie(r, e) {
  return e.clone !== !1 && e.isMergeableObject(r) ? J(Vr(r), r, e) : r;
}
function Qr(r, e, t) {
  return r.concat(e).map(function(n) {
    return ie(n, t);
  });
}
function Jr(r, e) {
  if (!e.customMerge)
    return J;
  var t = e.customMerge(r);
  return typeof t == "function" ? t : J;
}
function Kr(r) {
  return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(r).filter(function(e) {
    return Object.propertyIsEnumerable.call(r, e);
  }) : [];
}
function He(r) {
  return Object.keys(r).concat(Kr(r));
}
function sr(r, e) {
  try {
    return e in r;
  } catch {
    return !1;
  }
}
function Yr(r, e) {
  return sr(r, e) && !(Object.hasOwnProperty.call(r, e) && Object.propertyIsEnumerable.call(r, e));
}
function Xr(r, e, t) {
  var n = {};
  return t.isMergeableObject(r) && He(r).forEach(function(o) {
    n[o] = ie(r[o], t);
  }), He(e).forEach(function(o) {
    Yr(r, o) || (sr(r, o) && t.isMergeableObject(e[o]) ? n[o] = Jr(o, t)(r[o], e[o], t) : n[o] = ie(e[o], t));
  }), n;
}
function J(r, e, t) {
  t = t || {}, t.arrayMerge = t.arrayMerge || Qr, t.isMergeableObject = t.isMergeableObject || kr, t.cloneUnlessOtherwiseSpecified = ie;
  var n = Array.isArray(e), o = Array.isArray(r), a = n === o;
  return a ? n ? t.arrayMerge(r, e, t) : Xr(r, e, t) : ie(e, t);
}
J.all = function(e, t) {
  if (!Array.isArray(e))
    throw new Error("first argument should be an array");
  return e.reduce(function(n, o) {
    return J(n, o, t);
  }, {});
};
var Zr = J, et = Zr, rt = function() {
  if (typeof Symbol != "function" || typeof Object.getOwnPropertySymbols != "function")
    return !1;
  if (typeof Symbol.iterator == "symbol")
    return !0;
  var e = {}, t = Symbol("test"), n = Object(t);
  if (typeof t == "string" || Object.prototype.toString.call(t) !== "[object Symbol]" || Object.prototype.toString.call(n) !== "[object Symbol]")
    return !1;
  var o = 42;
  e[t] = o;
  for (t in e)
    return !1;
  if (typeof Object.keys == "function" && Object.keys(e).length !== 0 || typeof Object.getOwnPropertyNames == "function" && Object.getOwnPropertyNames(e).length !== 0)
    return !1;
  var a = Object.getOwnPropertySymbols(e);
  if (a.length !== 1 || a[0] !== t || !Object.prototype.propertyIsEnumerable.call(e, t))
    return !1;
  if (typeof Object.getOwnPropertyDescriptor == "function") {
    var c = Object.getOwnPropertyDescriptor(e, t);
    if (c.value !== o || c.enumerable !== !0)
      return !1;
  }
  return !0;
}, qe = typeof Symbol < "u" && Symbol, tt = rt, nt = function() {
  return typeof qe != "function" || typeof Symbol != "function" || typeof qe("foo") != "symbol" || typeof Symbol("bar") != "symbol" ? !1 : tt();
}, at = "Function.prototype.bind called on incompatible ", ge = Array.prototype.slice, ot = Object.prototype.toString, it = "[object Function]", lt = function(e) {
  var t = this;
  if (typeof t != "function" || ot.call(t) !== it)
    throw new TypeError(at + t);
  for (var n = ge.call(arguments, 1), o, a = function() {
    if (this instanceof o) {
      var p = t.apply(
        this,
        n.concat(ge.call(arguments))
      );
      return Object(p) === p ? p : this;
    } else
      return t.apply(
        e,
        n.concat(ge.call(arguments))
      );
  }, c = Math.max(0, t.length - n.length), u = [], i = 0; i < c; i++)
    u.push("$" + i);
  if (o = Function("binder", "return function (" + u.join(",") + "){ return binder.apply(this,arguments); }")(a), t.prototype) {
    var l = function() {
    };
    l.prototype = t.prototype, o.prototype = new l(), l.prototype = null;
  }
  return o;
}, ft = lt, Ce = Function.prototype.bind || ft, ut = Ce, ct = ut.call(Function.call, Object.prototype.hasOwnProperty), h, K = SyntaxError, pr = Function, Q = TypeError, me = function(r) {
  try {
    return pr('"use strict"; return (' + r + ").constructor;")();
  } catch {
  }
}, j = Object.getOwnPropertyDescriptor;
if (j)
  try {
    j({}, "");
  } catch {
    j = null;
  }
var he = function() {
  throw new Q();
}, st = j ? function() {
  try {
    return arguments.callee, he;
  } catch {
    try {
      return j(arguments, "callee").get;
    } catch {
      return he;
    }
  }
}() : he, q = nt(), M = Object.getPrototypeOf || function(r) {
  return r.__proto__;
}, V = {}, pt = typeof Uint8Array > "u" ? h : M(Uint8Array), G = {
  "%AggregateError%": typeof AggregateError > "u" ? h : AggregateError,
  "%Array%": Array,
  "%ArrayBuffer%": typeof ArrayBuffer > "u" ? h : ArrayBuffer,
  "%ArrayIteratorPrototype%": q ? M([][Symbol.iterator]()) : h,
  "%AsyncFromSyncIteratorPrototype%": h,
  "%AsyncFunction%": V,
  "%AsyncGenerator%": V,
  "%AsyncGeneratorFunction%": V,
  "%AsyncIteratorPrototype%": V,
  "%Atomics%": typeof Atomics > "u" ? h : Atomics,
  "%BigInt%": typeof BigInt > "u" ? h : BigInt,
  "%BigInt64Array%": typeof BigInt64Array > "u" ? h : BigInt64Array,
  "%BigUint64Array%": typeof BigUint64Array > "u" ? h : BigUint64Array,
  "%Boolean%": Boolean,
  "%DataView%": typeof DataView > "u" ? h : DataView,
  "%Date%": Date,
  "%decodeURI%": decodeURI,
  "%decodeURIComponent%": decodeURIComponent,
  "%encodeURI%": encodeURI,
  "%encodeURIComponent%": encodeURIComponent,
  "%Error%": Error,
  "%eval%": eval,
  // eslint-disable-line no-eval
  "%EvalError%": EvalError,
  "%Float32Array%": typeof Float32Array > "u" ? h : Float32Array,
  "%Float64Array%": typeof Float64Array > "u" ? h : Float64Array,
  "%FinalizationRegistry%": typeof FinalizationRegistry > "u" ? h : FinalizationRegistry,
  "%Function%": pr,
  "%GeneratorFunction%": V,
  "%Int8Array%": typeof Int8Array > "u" ? h : Int8Array,
  "%Int16Array%": typeof Int16Array > "u" ? h : Int16Array,
  "%Int32Array%": typeof Int32Array > "u" ? h : Int32Array,
  "%isFinite%": isFinite,
  "%isNaN%": isNaN,
  "%IteratorPrototype%": q ? M(M([][Symbol.iterator]())) : h,
  "%JSON%": typeof JSON == "object" ? JSON : h,
  "%Map%": typeof Map > "u" ? h : Map,
  "%MapIteratorPrototype%": typeof Map > "u" || !q ? h : M((/* @__PURE__ */ new Map())[Symbol.iterator]()),
  "%Math%": Math,
  "%Number%": Number,
  "%Object%": Object,
  "%parseFloat%": parseFloat,
  "%parseInt%": parseInt,
  "%Promise%": typeof Promise > "u" ? h : Promise,
  "%Proxy%": typeof Proxy > "u" ? h : Proxy,
  "%RangeError%": RangeError,
  "%ReferenceError%": ReferenceError,
  "%Reflect%": typeof Reflect > "u" ? h : Reflect,
  "%RegExp%": RegExp,
  "%Set%": typeof Set > "u" ? h : Set,
  "%SetIteratorPrototype%": typeof Set > "u" || !q ? h : M((/* @__PURE__ */ new Set())[Symbol.iterator]()),
  "%SharedArrayBuffer%": typeof SharedArrayBuffer > "u" ? h : SharedArrayBuffer,
  "%String%": String,
  "%StringIteratorPrototype%": q ? M(""[Symbol.iterator]()) : h,
  "%Symbol%": q ? Symbol : h,
  "%SyntaxError%": K,
  "%ThrowTypeError%": st,
  "%TypedArray%": pt,
  "%TypeError%": Q,
  "%Uint8Array%": typeof Uint8Array > "u" ? h : Uint8Array,
  "%Uint8ClampedArray%": typeof Uint8ClampedArray > "u" ? h : Uint8ClampedArray,
  "%Uint16Array%": typeof Uint16Array > "u" ? h : Uint16Array,
  "%Uint32Array%": typeof Uint32Array > "u" ? h : Uint32Array,
  "%URIError%": URIError,
  "%WeakMap%": typeof WeakMap > "u" ? h : WeakMap,
  "%WeakRef%": typeof WeakRef > "u" ? h : WeakRef,
  "%WeakSet%": typeof WeakSet > "u" ? h : WeakSet
};
try {
  null.error;
} catch (r) {
  var yt = M(M(r));
  G["%Error.prototype%"] = yt;
}
var dt = function r(e) {
  var t;
  if (e === "%AsyncFunction%")
    t = me("async function () {}");
  else if (e === "%GeneratorFunction%")
    t = me("function* () {}");
  else if (e === "%AsyncGeneratorFunction%")
    t = me("async function* () {}");
  else if (e === "%AsyncGenerator%") {
    var n = r("%AsyncGeneratorFunction%");
    n && (t = n.prototype);
  } else if (e === "%AsyncIteratorPrototype%") {
    var o = r("%AsyncGenerator%");
    o && (t = M(o.prototype));
  }
  return G[e] = t, t;
}, Ve = {
  "%ArrayBufferPrototype%": ["ArrayBuffer", "prototype"],
  "%ArrayPrototype%": ["Array", "prototype"],
  "%ArrayProto_entries%": ["Array", "prototype", "entries"],
  "%ArrayProto_forEach%": ["Array", "prototype", "forEach"],
  "%ArrayProto_keys%": ["Array", "prototype", "keys"],
  "%ArrayProto_values%": ["Array", "prototype", "values"],
  "%AsyncFunctionPrototype%": ["AsyncFunction", "prototype"],
  "%AsyncGenerator%": ["AsyncGeneratorFunction", "prototype"],
  "%AsyncGeneratorPrototype%": ["AsyncGeneratorFunction", "prototype", "prototype"],
  "%BooleanPrototype%": ["Boolean", "prototype"],
  "%DataViewPrototype%": ["DataView", "prototype"],
  "%DatePrototype%": ["Date", "prototype"],
  "%ErrorPrototype%": ["Error", "prototype"],
  "%EvalErrorPrototype%": ["EvalError", "prototype"],
  "%Float32ArrayPrototype%": ["Float32Array", "prototype"],
  "%Float64ArrayPrototype%": ["Float64Array", "prototype"],
  "%FunctionPrototype%": ["Function", "prototype"],
  "%Generator%": ["GeneratorFunction", "prototype"],
  "%GeneratorPrototype%": ["GeneratorFunction", "prototype", "prototype"],
  "%Int8ArrayPrototype%": ["Int8Array", "prototype"],
  "%Int16ArrayPrototype%": ["Int16Array", "prototype"],
  "%Int32ArrayPrototype%": ["Int32Array", "prototype"],
  "%JSONParse%": ["JSON", "parse"],
  "%JSONStringify%": ["JSON", "stringify"],
  "%MapPrototype%": ["Map", "prototype"],
  "%NumberPrototype%": ["Number", "prototype"],
  "%ObjectPrototype%": ["Object", "prototype"],
  "%ObjProto_toString%": ["Object", "prototype", "toString"],
  "%ObjProto_valueOf%": ["Object", "prototype", "valueOf"],
  "%PromisePrototype%": ["Promise", "prototype"],
  "%PromiseProto_then%": ["Promise", "prototype", "then"],
  "%Promise_all%": ["Promise", "all"],
  "%Promise_reject%": ["Promise", "reject"],
  "%Promise_resolve%": ["Promise", "resolve"],
  "%RangeErrorPrototype%": ["RangeError", "prototype"],
  "%ReferenceErrorPrototype%": ["ReferenceError", "prototype"],
  "%RegExpPrototype%": ["RegExp", "prototype"],
  "%SetPrototype%": ["Set", "prototype"],
  "%SharedArrayBufferPrototype%": ["SharedArrayBuffer", "prototype"],
  "%StringPrototype%": ["String", "prototype"],
  "%SymbolPrototype%": ["Symbol", "prototype"],
  "%SyntaxErrorPrototype%": ["SyntaxError", "prototype"],
  "%TypedArrayPrototype%": ["TypedArray", "prototype"],
  "%TypeErrorPrototype%": ["TypeError", "prototype"],
  "%Uint8ArrayPrototype%": ["Uint8Array", "prototype"],
  "%Uint8ClampedArrayPrototype%": ["Uint8ClampedArray", "prototype"],
  "%Uint16ArrayPrototype%": ["Uint16Array", "prototype"],
  "%Uint32ArrayPrototype%": ["Uint32Array", "prototype"],
  "%URIErrorPrototype%": ["URIError", "prototype"],
  "%WeakMapPrototype%": ["WeakMap", "prototype"],
  "%WeakSetPrototype%": ["WeakSet", "prototype"]
}, le = Ce, se = ct, vt = le.call(Function.call, Array.prototype.concat), gt = le.call(Function.apply, Array.prototype.splice), Qe = le.call(Function.call, String.prototype.replace), pe = le.call(Function.call, String.prototype.slice), mt = le.call(Function.call, RegExp.prototype.exec), ht = /[^%.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|%$))/g, St = /\\(\\)?/g, bt = function(e) {
  var t = pe(e, 0, 1), n = pe(e, -1);
  if (t === "%" && n !== "%")
    throw new K("invalid intrinsic syntax, expected closing `%`");
  if (n === "%" && t !== "%")
    throw new K("invalid intrinsic syntax, expected opening `%`");
  var o = [];
  return Qe(e, ht, function(a, c, u, i) {
    o[o.length] = u ? Qe(i, St, "$1") : c || a;
  }), o;
}, Ot = function(e, t) {
  var n = e, o;
  if (se(Ve, n) && (o = Ve[n], n = "%" + o[0] + "%"), se(G, n)) {
    var a = G[n];
    if (a === V && (a = dt(n)), typeof a > "u" && !t)
      throw new Q("intrinsic " + e + " exists, but is not available. Please file an issue!");
    return {
      alias: o,
      name: n,
      value: a
    };
  }
  throw new K("intrinsic " + e + " does not exist!");
}, Be = function(e, t) {
  if (typeof e != "string" || e.length === 0)
    throw new Q("intrinsic name must be a non-empty string");
  if (arguments.length > 1 && typeof t != "boolean")
    throw new Q('"allowMissing" argument must be a boolean');
  if (mt(/^%?[^%]*%?$/, e) === null)
    throw new K("`%` may not be present anywhere but at the beginning and end of the intrinsic name");
  var n = bt(e), o = n.length > 0 ? n[0] : "", a = Ot("%" + o + "%", t), c = a.name, u = a.value, i = !1, l = a.alias;
  l && (o = l[0], gt(n, vt([0, 1], l)));
  for (var p = 1, y = !0; p < n.length; p += 1) {
    var d = n[p], m = pe(d, 0, 1), f = pe(d, -1);
    if ((m === '"' || m === "'" || m === "`" || f === '"' || f === "'" || f === "`") && m !== f)
      throw new K("property names with quotes must have matching quotes");
    if ((d === "constructor" || !y) && (i = !0), o += "." + d, c = "%" + o + "%", se(G, c))
      u = G[c];
    else if (u != null) {
      if (!(d in u)) {
        if (!t)
          throw new Q("base intrinsic for " + e + " exists, but the property is not available.");
        return;
      }
      if (j && p + 1 >= n.length) {
        var s = j(u, d);
        y = !!s, y && "get" in s && !("originalValue" in s.get) ? u = s.get : u = u[d];
      } else
        y = se(u, d), u = u[d];
      y && !i && (G[c] = u);
    }
  }
  return u;
}, xe = {}, wt = {
  get exports() {
    return xe;
  },
  set exports(r) {
    xe = r;
  }
};
(function(r) {
  var e = Ce, t = Be, n = t("%Function.prototype.apply%"), o = t("%Function.prototype.call%"), a = t("%Reflect.apply%", !0) || e.call(o, n), c = t("%Object.getOwnPropertyDescriptor%", !0), u = t("%Object.defineProperty%", !0), i = t("%Math.max%");
  if (u)
    try {
      u({}, "a", { value: 1 });
    } catch {
      u = null;
    }
  r.exports = function(y) {
    var d = a(e, o, arguments);
    if (c && u) {
      var m = c(d, "length");
      m.configurable && u(
        d,
        "length",
        { value: 1 + i(0, y.length - (arguments.length - 1)) }
      );
    }
    return d;
  };
  var l = function() {
    return a(e, n, arguments);
  };
  u ? u(r.exports, "apply", { value: l }) : r.exports.apply = l;
})(wt);
var yr = Be, dr = xe, At = dr(yr("String.prototype.indexOf")), Pt = function(e, t) {
  var n = yr(e, !!t);
  return typeof n == "function" && At(e, ".prototype.") > -1 ? dr(n) : n;
};
const Et = {}, xt = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Et
}, Symbol.toStringTag, { value: "Module" })), Rt = /* @__PURE__ */ Wr(xt);
var _e = typeof Map == "function" && Map.prototype, Se = Object.getOwnPropertyDescriptor && _e ? Object.getOwnPropertyDescriptor(Map.prototype, "size") : null, ye = _e && Se && typeof Se.get == "function" ? Se.get : null, Je = _e && Map.prototype.forEach, Ue = typeof Set == "function" && Set.prototype, be = Object.getOwnPropertyDescriptor && Ue ? Object.getOwnPropertyDescriptor(Set.prototype, "size") : null, de = Ue && be && typeof be.get == "function" ? be.get : null, Ke = Ue && Set.prototype.forEach, $t = typeof WeakMap == "function" && WeakMap.prototype, ne = $t ? WeakMap.prototype.has : null, It = typeof WeakSet == "function" && WeakSet.prototype, ae = It ? WeakSet.prototype.has : null, Ft = typeof WeakRef == "function" && WeakRef.prototype, Ye = Ft ? WeakRef.prototype.deref : null, Mt = Boolean.prototype.valueOf, Tt = Object.prototype.toString, Nt = Function.prototype.toString, Ct = String.prototype.match, De = String.prototype.slice, D = String.prototype.replace, Bt = String.prototype.toUpperCase, Xe = String.prototype.toLowerCase, vr = RegExp.prototype.test, Ze = Array.prototype.concat, T = Array.prototype.join, _t = Array.prototype.slice, er = Math.floor, Re = typeof BigInt == "function" ? BigInt.prototype.valueOf : null, Oe = Object.getOwnPropertySymbols, $e = typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? Symbol.prototype.toString : null, Y = typeof Symbol == "function" && typeof Symbol.iterator == "object", x = typeof Symbol == "function" && Symbol.toStringTag && (typeof Symbol.toStringTag === Y || "symbol") ? Symbol.toStringTag : null, gr = Object.prototype.propertyIsEnumerable, rr = (typeof Reflect == "function" ? Reflect.getPrototypeOf : Object.getPrototypeOf) || ([].__proto__ === Array.prototype ? function(r) {
  return r.__proto__;
} : null);
function tr(r, e) {
  if (r === 1 / 0 || r === -1 / 0 || r !== r || r && r > -1e3 && r < 1e3 || vr.call(/e/, e))
    return e;
  var t = /[0-9](?=(?:[0-9]{3})+(?![0-9]))/g;
  if (typeof r == "number") {
    var n = r < 0 ? -er(-r) : er(r);
    if (n !== r) {
      var o = String(n), a = De.call(e, o.length + 1);
      return D.call(o, t, "$&_") + "." + D.call(D.call(a, /([0-9]{3})/g, "$&_"), /_$/, "");
    }
  }
  return D.call(e, t, "$&_");
}
var Ie = Rt, nr = Ie.custom, ar = hr(nr) ? nr : null, Ut = function r(e, t, n, o) {
  var a = t || {};
  if (U(a, "quoteStyle") && a.quoteStyle !== "single" && a.quoteStyle !== "double")
    throw new TypeError('option "quoteStyle" must be "single" or "double"');
  if (U(a, "maxStringLength") && (typeof a.maxStringLength == "number" ? a.maxStringLength < 0 && a.maxStringLength !== 1 / 0 : a.maxStringLength !== null))
    throw new TypeError('option "maxStringLength", if provided, must be a positive integer, Infinity, or `null`');
  var c = U(a, "customInspect") ? a.customInspect : !0;
  if (typeof c != "boolean" && c !== "symbol")
    throw new TypeError("option \"customInspect\", if provided, must be `true`, `false`, or `'symbol'`");
  if (U(a, "indent") && a.indent !== null && a.indent !== "	" && !(parseInt(a.indent, 10) === a.indent && a.indent > 0))
    throw new TypeError('option "indent" must be "\\t", an integer > 0, or `null`');
  if (U(a, "numericSeparator") && typeof a.numericSeparator != "boolean")
    throw new TypeError('option "numericSeparator", if provided, must be `true` or `false`');
  var u = a.numericSeparator;
  if (typeof e > "u")
    return "undefined";
  if (e === null)
    return "null";
  if (typeof e == "boolean")
    return e ? "true" : "false";
  if (typeof e == "string")
    return br(e, a);
  if (typeof e == "number") {
    if (e === 0)
      return 1 / 0 / e > 0 ? "0" : "-0";
    var i = String(e);
    return u ? tr(e, i) : i;
  }
  if (typeof e == "bigint") {
    var l = String(e) + "n";
    return u ? tr(e, l) : l;
  }
  var p = typeof a.depth > "u" ? 5 : a.depth;
  if (typeof n > "u" && (n = 0), n >= p && p > 0 && typeof e == "object")
    return Fe(e) ? "[Array]" : "[Object]";
  var y = rn(a, n);
  if (typeof o > "u")
    o = [];
  else if (Sr(o, e) >= 0)
    return "[Circular]";
  function d(I, _, N) {
    if (_ && (o = _t.call(o), o.push(_)), N) {
      var ee = {
        depth: a.depth
      };
      return U(a, "quoteStyle") && (ee.quoteStyle = a.quoteStyle), r(I, ee, n + 1, o);
    }
    return r(I, a, n + 1, o);
  }
  if (typeof e == "function" && !or(e)) {
    var m = qt(e), f = fe(e, d);
    return "[Function" + (m ? ": " + m : " (anonymous)") + "]" + (f.length > 0 ? " { " + T.call(f, ", ") + " }" : "");
  }
  if (hr(e)) {
    var s = Y ? D.call(String(e), /^(Symbol\(.*\))_[^)]*$/, "$1") : $e.call(e);
    return typeof e == "object" && !Y ? te(s) : s;
  }
  if (Xt(e)) {
    for (var g = "<" + Xe.call(String(e.nodeName)), v = e.attributes || [], b = 0; b < v.length; b++)
      g += " " + v[b].name + "=" + mr(Dt(v[b].value), "double", a);
    return g += ">", e.childNodes && e.childNodes.length && (g += "..."), g += "</" + Xe.call(String(e.nodeName)) + ">", g;
  }
  if (Fe(e)) {
    if (e.length === 0)
      return "[]";
    var w = fe(e, d);
    return y && !en(w) ? "[" + Me(w, y) + "]" : "[ " + T.call(w, ", ") + " ]";
  }
  if (Wt(e)) {
    var S = fe(e, d);
    return !("cause" in Error.prototype) && "cause" in e && !gr.call(e, "cause") ? "{ [" + String(e) + "] " + T.call(Ze.call("[cause]: " + d(e.cause), S), ", ") + " }" : S.length === 0 ? "[" + String(e) + "]" : "{ [" + String(e) + "] " + T.call(S, ", ") + " }";
  }
  if (typeof e == "object" && c) {
    if (ar && typeof e[ar] == "function" && Ie)
      return Ie(e, { depth: p - n });
    if (c !== "symbol" && typeof e.inspect == "function")
      return e.inspect();
  }
  if (Vt(e)) {
    var A = [];
    return Je && Je.call(e, function(I, _) {
      A.push(d(_, e, !0) + " => " + d(I, e));
    }), ir("Map", ye.call(e), A, y);
  }
  if (Kt(e)) {
    var E = [];
    return Ke && Ke.call(e, function(I) {
      E.push(d(I, e));
    }), ir("Set", de.call(e), E, y);
  }
  if (Qt(e))
    return we("WeakMap");
  if (Yt(e))
    return we("WeakSet");
  if (Jt(e))
    return we("WeakRef");
  if (jt(e))
    return te(d(Number(e)));
  if (zt(e))
    return te(d(Re.call(e)));
  if (Gt(e))
    return te(Mt.call(e));
  if (kt(e))
    return te(d(String(e)));
  if (!Lt(e) && !or(e)) {
    var R = fe(e, d), $ = rr ? rr(e) === Object.prototype : e instanceof Object || e.constructor === Object, B = e instanceof Object ? "" : "null prototype", z = !$ && x && Object(e) === e && x in e ? De.call(L(e), 8, -1) : B ? "Object" : "", W = $ || typeof e.constructor != "function" ? "" : e.constructor.name ? e.constructor.name + " " : "", H = W + (z || B ? "[" + T.call(Ze.call([], z || [], B || []), ": ") + "] " : "");
    return R.length === 0 ? H + "{}" : y ? H + "{" + Me(R, y) + "}" : H + "{ " + T.call(R, ", ") + " }";
  }
  return String(e);
};
function mr(r, e, t) {
  var n = (t.quoteStyle || e) === "double" ? '"' : "'";
  return n + r + n;
}
function Dt(r) {
  return D.call(String(r), /"/g, "&quot;");
}
function Fe(r) {
  return L(r) === "[object Array]" && (!x || !(typeof r == "object" && x in r));
}
function Lt(r) {
  return L(r) === "[object Date]" && (!x || !(typeof r == "object" && x in r));
}
function or(r) {
  return L(r) === "[object RegExp]" && (!x || !(typeof r == "object" && x in r));
}
function Wt(r) {
  return L(r) === "[object Error]" && (!x || !(typeof r == "object" && x in r));
}
function kt(r) {
  return L(r) === "[object String]" && (!x || !(typeof r == "object" && x in r));
}
function jt(r) {
  return L(r) === "[object Number]" && (!x || !(typeof r == "object" && x in r));
}
function Gt(r) {
  return L(r) === "[object Boolean]" && (!x || !(typeof r == "object" && x in r));
}
function hr(r) {
  if (Y)
    return r && typeof r == "object" && r instanceof Symbol;
  if (typeof r == "symbol")
    return !0;
  if (!r || typeof r != "object" || !$e)
    return !1;
  try {
    return $e.call(r), !0;
  } catch {
  }
  return !1;
}
function zt(r) {
  if (!r || typeof r != "object" || !Re)
    return !1;
  try {
    return Re.call(r), !0;
  } catch {
  }
  return !1;
}
var Ht = Object.prototype.hasOwnProperty || function(r) {
  return r in this;
};
function U(r, e) {
  return Ht.call(r, e);
}
function L(r) {
  return Tt.call(r);
}
function qt(r) {
  if (r.name)
    return r.name;
  var e = Ct.call(Nt.call(r), /^function\s*([\w$]+)/);
  return e ? e[1] : null;
}
function Sr(r, e) {
  if (r.indexOf)
    return r.indexOf(e);
  for (var t = 0, n = r.length; t < n; t++)
    if (r[t] === e)
      return t;
  return -1;
}
function Vt(r) {
  if (!ye || !r || typeof r != "object")
    return !1;
  try {
    ye.call(r);
    try {
      de.call(r);
    } catch {
      return !0;
    }
    return r instanceof Map;
  } catch {
  }
  return !1;
}
function Qt(r) {
  if (!ne || !r || typeof r != "object")
    return !1;
  try {
    ne.call(r, ne);
    try {
      ae.call(r, ae);
    } catch {
      return !0;
    }
    return r instanceof WeakMap;
  } catch {
  }
  return !1;
}
function Jt(r) {
  if (!Ye || !r || typeof r != "object")
    return !1;
  try {
    return Ye.call(r), !0;
  } catch {
  }
  return !1;
}
function Kt(r) {
  if (!de || !r || typeof r != "object")
    return !1;
  try {
    de.call(r);
    try {
      ye.call(r);
    } catch {
      return !0;
    }
    return r instanceof Set;
  } catch {
  }
  return !1;
}
function Yt(r) {
  if (!ae || !r || typeof r != "object")
    return !1;
  try {
    ae.call(r, ae);
    try {
      ne.call(r, ne);
    } catch {
      return !0;
    }
    return r instanceof WeakSet;
  } catch {
  }
  return !1;
}
function Xt(r) {
  return !r || typeof r != "object" ? !1 : typeof HTMLElement < "u" && r instanceof HTMLElement ? !0 : typeof r.nodeName == "string" && typeof r.getAttribute == "function";
}
function br(r, e) {
  if (r.length > e.maxStringLength) {
    var t = r.length - e.maxStringLength, n = "... " + t + " more character" + (t > 1 ? "s" : "");
    return br(De.call(r, 0, e.maxStringLength), e) + n;
  }
  var o = D.call(D.call(r, /(['\\])/g, "\\$1"), /[\x00-\x1f]/g, Zt);
  return mr(o, "single", e);
}
function Zt(r) {
  var e = r.charCodeAt(0), t = {
    8: "b",
    9: "t",
    10: "n",
    12: "f",
    13: "r"
  }[e];
  return t ? "\\" + t : "\\x" + (e < 16 ? "0" : "") + Bt.call(e.toString(16));
}
function te(r) {
  return "Object(" + r + ")";
}
function we(r) {
  return r + " { ? }";
}
function ir(r, e, t, n) {
  var o = n ? Me(t, n) : T.call(t, ", ");
  return r + " (" + e + ") {" + o + "}";
}
function en(r) {
  for (var e = 0; e < r.length; e++)
    if (Sr(r[e], `
`) >= 0)
      return !1;
  return !0;
}
function rn(r, e) {
  var t;
  if (r.indent === "	")
    t = "	";
  else if (typeof r.indent == "number" && r.indent > 0)
    t = T.call(Array(r.indent + 1), " ");
  else
    return null;
  return {
    base: t,
    prev: T.call(Array(e + 1), t)
  };
}
function Me(r, e) {
  if (r.length === 0)
    return "";
  var t = `
` + e.prev + e.base;
  return t + T.call(r, "," + t) + `
` + e.prev;
}
function fe(r, e) {
  var t = Fe(r), n = [];
  if (t) {
    n.length = r.length;
    for (var o = 0; o < r.length; o++)
      n[o] = U(r, o) ? e(r[o], r) : "";
  }
  var a = typeof Oe == "function" ? Oe(r) : [], c;
  if (Y) {
    c = {};
    for (var u = 0; u < a.length; u++)
      c["$" + a[u]] = a[u];
  }
  for (var i in r)
    U(r, i) && (t && String(Number(i)) === i && i < r.length || Y && c["$" + i] instanceof Symbol || (vr.call(/[^\w$]/, i) ? n.push(e(i, r) + ": " + e(r[i], r)) : n.push(i + ": " + e(r[i], r))));
  if (typeof Oe == "function")
    for (var l = 0; l < a.length; l++)
      gr.call(r, a[l]) && n.push("[" + e(a[l]) + "]: " + e(r[a[l]], r));
  return n;
}
var Le = Be, Z = Pt, tn = Ut, nn = Le("%TypeError%"), ue = Le("%WeakMap%", !0), ce = Le("%Map%", !0), an = Z("WeakMap.prototype.get", !0), on = Z("WeakMap.prototype.set", !0), ln = Z("WeakMap.prototype.has", !0), fn = Z("Map.prototype.get", !0), un = Z("Map.prototype.set", !0), cn = Z("Map.prototype.has", !0), We = function(r, e) {
  for (var t = r, n; (n = t.next) !== null; t = n)
    if (n.key === e)
      return t.next = n.next, n.next = r.next, r.next = n, n;
}, sn = function(r, e) {
  var t = We(r, e);
  return t && t.value;
}, pn = function(r, e, t) {
  var n = We(r, e);
  n ? n.value = t : r.next = {
    // eslint-disable-line no-param-reassign
    key: e,
    next: r.next,
    value: t
  };
}, yn = function(r, e) {
  return !!We(r, e);
}, dn = function() {
  var e, t, n, o = {
    assert: function(a) {
      if (!o.has(a))
        throw new nn("Side channel does not contain " + tn(a));
    },
    get: function(a) {
      if (ue && a && (typeof a == "object" || typeof a == "function")) {
        if (e)
          return an(e, a);
      } else if (ce) {
        if (t)
          return fn(t, a);
      } else if (n)
        return sn(n, a);
    },
    has: function(a) {
      if (ue && a && (typeof a == "object" || typeof a == "function")) {
        if (e)
          return ln(e, a);
      } else if (ce) {
        if (t)
          return cn(t, a);
      } else if (n)
        return yn(n, a);
      return !1;
    },
    set: function(a, c) {
      ue && a && (typeof a == "object" || typeof a == "function") ? (e || (e = new ue()), on(e, a, c)) : ce ? (t || (t = new ce()), un(t, a, c)) : (n || (n = { key: {}, next: null }), pn(n, a, c));
    }
  };
  return o;
}, vn = String.prototype.replace, gn = /%20/g, Ae = {
  RFC1738: "RFC1738",
  RFC3986: "RFC3986"
}, ke = {
  default: Ae.RFC3986,
  formatters: {
    RFC1738: function(r) {
      return vn.call(r, gn, "+");
    },
    RFC3986: function(r) {
      return String(r);
    }
  },
  RFC1738: Ae.RFC1738,
  RFC3986: Ae.RFC3986
}, mn = ke, Pe = Object.prototype.hasOwnProperty, k = Array.isArray, F = function() {
  for (var r = [], e = 0; e < 256; ++e)
    r.push("%" + ((e < 16 ? "0" : "") + e.toString(16)).toUpperCase());
  return r;
}(), hn = function(e) {
  for (; e.length > 1; ) {
    var t = e.pop(), n = t.obj[t.prop];
    if (k(n)) {
      for (var o = [], a = 0; a < n.length; ++a)
        typeof n[a] < "u" && o.push(n[a]);
      t.obj[t.prop] = o;
    }
  }
}, Or = function(e, t) {
  for (var n = t && t.plainObjects ? /* @__PURE__ */ Object.create(null) : {}, o = 0; o < e.length; ++o)
    typeof e[o] < "u" && (n[o] = e[o]);
  return n;
}, Sn = function r(e, t, n) {
  if (!t)
    return e;
  if (typeof t != "object") {
    if (k(e))
      e.push(t);
    else if (e && typeof e == "object")
      (n && (n.plainObjects || n.allowPrototypes) || !Pe.call(Object.prototype, t)) && (e[t] = !0);
    else
      return [e, t];
    return e;
  }
  if (!e || typeof e != "object")
    return [e].concat(t);
  var o = e;
  return k(e) && !k(t) && (o = Or(e, n)), k(e) && k(t) ? (t.forEach(function(a, c) {
    if (Pe.call(e, c)) {
      var u = e[c];
      u && typeof u == "object" && a && typeof a == "object" ? e[c] = r(u, a, n) : e.push(a);
    } else
      e[c] = a;
  }), e) : Object.keys(t).reduce(function(a, c) {
    var u = t[c];
    return Pe.call(a, c) ? a[c] = r(a[c], u, n) : a[c] = u, a;
  }, o);
}, bn = function(e, t) {
  return Object.keys(t).reduce(function(n, o) {
    return n[o] = t[o], n;
  }, e);
}, On = function(r, e, t) {
  var n = r.replace(/\+/g, " ");
  if (t === "iso-8859-1")
    return n.replace(/%[0-9a-f]{2}/gi, unescape);
  try {
    return decodeURIComponent(n);
  } catch {
    return n;
  }
}, wn = function(e, t, n, o, a) {
  if (e.length === 0)
    return e;
  var c = e;
  if (typeof e == "symbol" ? c = Symbol.prototype.toString.call(e) : typeof e != "string" && (c = String(e)), n === "iso-8859-1")
    return escape(c).replace(/%u[0-9a-f]{4}/gi, function(p) {
      return "%26%23" + parseInt(p.slice(2), 16) + "%3B";
    });
  for (var u = "", i = 0; i < c.length; ++i) {
    var l = c.charCodeAt(i);
    if (l === 45 || l === 46 || l === 95 || l === 126 || l >= 48 && l <= 57 || l >= 65 && l <= 90 || l >= 97 && l <= 122 || a === mn.RFC1738 && (l === 40 || l === 41)) {
      u += c.charAt(i);
      continue;
    }
    if (l < 128) {
      u = u + F[l];
      continue;
    }
    if (l < 2048) {
      u = u + (F[192 | l >> 6] + F[128 | l & 63]);
      continue;
    }
    if (l < 55296 || l >= 57344) {
      u = u + (F[224 | l >> 12] + F[128 | l >> 6 & 63] + F[128 | l & 63]);
      continue;
    }
    i += 1, l = 65536 + ((l & 1023) << 10 | c.charCodeAt(i) & 1023), u += F[240 | l >> 18] + F[128 | l >> 12 & 63] + F[128 | l >> 6 & 63] + F[128 | l & 63];
  }
  return u;
}, An = function(e) {
  for (var t = [{ obj: { o: e }, prop: "o" }], n = [], o = 0; o < t.length; ++o)
    for (var a = t[o], c = a.obj[a.prop], u = Object.keys(c), i = 0; i < u.length; ++i) {
      var l = u[i], p = c[l];
      typeof p == "object" && p !== null && n.indexOf(p) === -1 && (t.push({ obj: c, prop: l }), n.push(p));
    }
  return hn(t), e;
}, Pn = function(e) {
  return Object.prototype.toString.call(e) === "[object RegExp]";
}, En = function(e) {
  return !e || typeof e != "object" ? !1 : !!(e.constructor && e.constructor.isBuffer && e.constructor.isBuffer(e));
}, xn = function(e, t) {
  return [].concat(e, t);
}, Rn = function(e, t) {
  if (k(e)) {
    for (var n = [], o = 0; o < e.length; o += 1)
      n.push(t(e[o]));
    return n;
  }
  return t(e);
}, wr = {
  arrayToObject: Or,
  assign: bn,
  combine: xn,
  compact: An,
  decode: On,
  encode: wn,
  isBuffer: En,
  isRegExp: Pn,
  maybeMap: Rn,
  merge: Sn
}, Ar = dn, Te = wr, oe = ke, $n = Object.prototype.hasOwnProperty, lr = {
  brackets: function(e) {
    return e + "[]";
  },
  comma: "comma",
  indices: function(e, t) {
    return e + "[" + t + "]";
  },
  repeat: function(e) {
    return e;
  }
}, C = Array.isArray, In = String.prototype.split, Fn = Array.prototype.push, Pr = function(r, e) {
  Fn.apply(r, C(e) ? e : [e]);
}, Mn = Date.prototype.toISOString, fr = oe.default, P = {
  addQueryPrefix: !1,
  allowDots: !1,
  charset: "utf-8",
  charsetSentinel: !1,
  delimiter: "&",
  encode: !0,
  encoder: Te.encode,
  encodeValuesOnly: !1,
  format: fr,
  formatter: oe.formatters[fr],
  // deprecated
  indices: !1,
  serializeDate: function(e) {
    return Mn.call(e);
  },
  skipNulls: !1,
  strictNullHandling: !1
}, Tn = function(e) {
  return typeof e == "string" || typeof e == "number" || typeof e == "boolean" || typeof e == "symbol" || typeof e == "bigint";
}, Ee = {}, Nn = function r(e, t, n, o, a, c, u, i, l, p, y, d, m, f, s, g) {
  for (var v = e, b = g, w = 0, S = !1; (b = b.get(Ee)) !== void 0 && !S; ) {
    var A = b.get(e);
    if (w += 1, typeof A < "u") {
      if (A === w)
        throw new RangeError("Cyclic object value");
      S = !0;
    }
    typeof b.get(Ee) > "u" && (w = 0);
  }
  if (typeof i == "function" ? v = i(t, v) : v instanceof Date ? v = y(v) : n === "comma" && C(v) && (v = Te.maybeMap(v, function(ve) {
    return ve instanceof Date ? y(ve) : ve;
  })), v === null) {
    if (a)
      return u && !f ? u(t, P.encoder, s, "key", d) : t;
    v = "";
  }
  if (Tn(v) || Te.isBuffer(v)) {
    if (u) {
      var E = f ? t : u(t, P.encoder, s, "key", d);
      if (n === "comma" && f) {
        for (var R = In.call(String(v), ","), $ = "", B = 0; B < R.length; ++B)
          $ += (B === 0 ? "" : ",") + m(u(R[B], P.encoder, s, "value", d));
        return [m(E) + (o && C(v) && R.length === 1 ? "[]" : "") + "=" + $];
      }
      return [m(E) + "=" + m(u(v, P.encoder, s, "value", d))];
    }
    return [m(t) + "=" + m(String(v))];
  }
  var z = [];
  if (typeof v > "u")
    return z;
  var W;
  if (n === "comma" && C(v))
    W = [{ value: v.length > 0 ? v.join(",") || null : void 0 }];
  else if (C(i))
    W = i;
  else {
    var H = Object.keys(v);
    W = l ? H.sort(l) : H;
  }
  for (var I = o && C(v) && v.length === 1 ? t + "[]" : t, _ = 0; _ < W.length; ++_) {
    var N = W[_], ee = typeof N == "object" && typeof N.value < "u" ? N.value : v[N];
    if (!(c && ee === null)) {
      var Rr = C(v) ? typeof n == "function" ? n(I, N) : I : I + (p ? "." + N : "[" + N + "]");
      g.set(e, w);
      var je = Ar();
      je.set(Ee, g), Pr(z, r(
        ee,
        Rr,
        n,
        o,
        a,
        c,
        u,
        i,
        l,
        p,
        y,
        d,
        m,
        f,
        s,
        je
      ));
    }
  }
  return z;
}, Cn = function(e) {
  if (!e)
    return P;
  if (e.encoder !== null && typeof e.encoder < "u" && typeof e.encoder != "function")
    throw new TypeError("Encoder has to be a function.");
  var t = e.charset || P.charset;
  if (typeof e.charset < "u" && e.charset !== "utf-8" && e.charset !== "iso-8859-1")
    throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
  var n = oe.default;
  if (typeof e.format < "u") {
    if (!$n.call(oe.formatters, e.format))
      throw new TypeError("Unknown format option provided.");
    n = e.format;
  }
  var o = oe.formatters[n], a = P.filter;
  return (typeof e.filter == "function" || C(e.filter)) && (a = e.filter), {
    addQueryPrefix: typeof e.addQueryPrefix == "boolean" ? e.addQueryPrefix : P.addQueryPrefix,
    allowDots: typeof e.allowDots > "u" ? P.allowDots : !!e.allowDots,
    charset: t,
    charsetSentinel: typeof e.charsetSentinel == "boolean" ? e.charsetSentinel : P.charsetSentinel,
    delimiter: typeof e.delimiter > "u" ? P.delimiter : e.delimiter,
    encode: typeof e.encode == "boolean" ? e.encode : P.encode,
    encoder: typeof e.encoder == "function" ? e.encoder : P.encoder,
    encodeValuesOnly: typeof e.encodeValuesOnly == "boolean" ? e.encodeValuesOnly : P.encodeValuesOnly,
    filter: a,
    format: n,
    formatter: o,
    serializeDate: typeof e.serializeDate == "function" ? e.serializeDate : P.serializeDate,
    skipNulls: typeof e.skipNulls == "boolean" ? e.skipNulls : P.skipNulls,
    sort: typeof e.sort == "function" ? e.sort : null,
    strictNullHandling: typeof e.strictNullHandling == "boolean" ? e.strictNullHandling : P.strictNullHandling
  };
}, Bn = function(r, e) {
  var t = r, n = Cn(e), o, a;
  typeof n.filter == "function" ? (a = n.filter, t = a("", t)) : C(n.filter) && (a = n.filter, o = a);
  var c = [];
  if (typeof t != "object" || t === null)
    return "";
  var u;
  e && e.arrayFormat in lr ? u = e.arrayFormat : e && "indices" in e ? u = e.indices ? "indices" : "repeat" : u = "indices";
  var i = lr[u];
  if (e && "commaRoundTrip" in e && typeof e.commaRoundTrip != "boolean")
    throw new TypeError("`commaRoundTrip` must be a boolean, or absent");
  var l = i === "comma" && e && e.commaRoundTrip;
  o || (o = Object.keys(t)), n.sort && o.sort(n.sort);
  for (var p = Ar(), y = 0; y < o.length; ++y) {
    var d = o[y];
    n.skipNulls && t[d] === null || Pr(c, Nn(
      t[d],
      d,
      i,
      l,
      n.strictNullHandling,
      n.skipNulls,
      n.encode ? n.encoder : null,
      n.filter,
      n.sort,
      n.allowDots,
      n.serializeDate,
      n.format,
      n.formatter,
      n.encodeValuesOnly,
      n.charset,
      p
    ));
  }
  var m = c.join(n.delimiter), f = n.addQueryPrefix === !0 ? "?" : "";
  return n.charsetSentinel && (n.charset === "iso-8859-1" ? f += "utf8=%26%2310003%3B&" : f += "utf8=%E2%9C%93&"), m.length > 0 ? f + m : "";
}, X = wr, Ne = Object.prototype.hasOwnProperty, _n = Array.isArray, O = {
  allowDots: !1,
  allowPrototypes: !1,
  allowSparse: !1,
  arrayLimit: 20,
  charset: "utf-8",
  charsetSentinel: !1,
  comma: !1,
  decoder: X.decode,
  delimiter: "&",
  depth: 5,
  ignoreQueryPrefix: !1,
  interpretNumericEntities: !1,
  parameterLimit: 1e3,
  parseArrays: !0,
  plainObjects: !1,
  strictNullHandling: !1
}, Un = function(r) {
  return r.replace(/&#(\d+);/g, function(e, t) {
    return String.fromCharCode(parseInt(t, 10));
  });
}, Er = function(r, e) {
  return r && typeof r == "string" && e.comma && r.indexOf(",") > -1 ? r.split(",") : r;
}, Dn = "utf8=%26%2310003%3B", Ln = "utf8=%E2%9C%93", Wn = function(e, t) {
  var n = {}, o = t.ignoreQueryPrefix ? e.replace(/^\?/, "") : e, a = t.parameterLimit === 1 / 0 ? void 0 : t.parameterLimit, c = o.split(t.delimiter, a), u = -1, i, l = t.charset;
  if (t.charsetSentinel)
    for (i = 0; i < c.length; ++i)
      c[i].indexOf("utf8=") === 0 && (c[i] === Ln ? l = "utf-8" : c[i] === Dn && (l = "iso-8859-1"), u = i, i = c.length);
  for (i = 0; i < c.length; ++i)
    if (i !== u) {
      var p = c[i], y = p.indexOf("]="), d = y === -1 ? p.indexOf("=") : y + 1, m, f;
      d === -1 ? (m = t.decoder(p, O.decoder, l, "key"), f = t.strictNullHandling ? null : "") : (m = t.decoder(p.slice(0, d), O.decoder, l, "key"), f = X.maybeMap(
        Er(p.slice(d + 1), t),
        function(s) {
          return t.decoder(s, O.decoder, l, "value");
        }
      )), f && t.interpretNumericEntities && l === "iso-8859-1" && (f = Un(f)), p.indexOf("[]=") > -1 && (f = _n(f) ? [f] : f), Ne.call(n, m) ? n[m] = X.combine(n[m], f) : n[m] = f;
    }
  return n;
}, kn = function(r, e, t, n) {
  for (var o = n ? e : Er(e, t), a = r.length - 1; a >= 0; --a) {
    var c, u = r[a];
    if (u === "[]" && t.parseArrays)
      c = [].concat(o);
    else {
      c = t.plainObjects ? /* @__PURE__ */ Object.create(null) : {};
      var i = u.charAt(0) === "[" && u.charAt(u.length - 1) === "]" ? u.slice(1, -1) : u, l = parseInt(i, 10);
      !t.parseArrays && i === "" ? c = { 0: o } : !isNaN(l) && u !== i && String(l) === i && l >= 0 && t.parseArrays && l <= t.arrayLimit ? (c = [], c[l] = o) : i !== "__proto__" && (c[i] = o);
    }
    o = c;
  }
  return o;
}, jn = function(e, t, n, o) {
  if (e) {
    var a = n.allowDots ? e.replace(/\.([^.[]+)/g, "[$1]") : e, c = /(\[[^[\]]*])/, u = /(\[[^[\]]*])/g, i = n.depth > 0 && c.exec(a), l = i ? a.slice(0, i.index) : a, p = [];
    if (l) {
      if (!n.plainObjects && Ne.call(Object.prototype, l) && !n.allowPrototypes)
        return;
      p.push(l);
    }
    for (var y = 0; n.depth > 0 && (i = u.exec(a)) !== null && y < n.depth; ) {
      if (y += 1, !n.plainObjects && Ne.call(Object.prototype, i[1].slice(1, -1)) && !n.allowPrototypes)
        return;
      p.push(i[1]);
    }
    return i && p.push("[" + a.slice(i.index) + "]"), kn(p, t, n, o);
  }
}, Gn = function(e) {
  if (!e)
    return O;
  if (e.decoder !== null && e.decoder !== void 0 && typeof e.decoder != "function")
    throw new TypeError("Decoder has to be a function.");
  if (typeof e.charset < "u" && e.charset !== "utf-8" && e.charset !== "iso-8859-1")
    throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
  var t = typeof e.charset > "u" ? O.charset : e.charset;
  return {
    allowDots: typeof e.allowDots > "u" ? O.allowDots : !!e.allowDots,
    allowPrototypes: typeof e.allowPrototypes == "boolean" ? e.allowPrototypes : O.allowPrototypes,
    allowSparse: typeof e.allowSparse == "boolean" ? e.allowSparse : O.allowSparse,
    arrayLimit: typeof e.arrayLimit == "number" ? e.arrayLimit : O.arrayLimit,
    charset: t,
    charsetSentinel: typeof e.charsetSentinel == "boolean" ? e.charsetSentinel : O.charsetSentinel,
    comma: typeof e.comma == "boolean" ? e.comma : O.comma,
    decoder: typeof e.decoder == "function" ? e.decoder : O.decoder,
    delimiter: typeof e.delimiter == "string" || X.isRegExp(e.delimiter) ? e.delimiter : O.delimiter,
    // eslint-disable-next-line no-implicit-coercion, no-extra-parens
    depth: typeof e.depth == "number" || e.depth === !1 ? +e.depth : O.depth,
    ignoreQueryPrefix: e.ignoreQueryPrefix === !0,
    interpretNumericEntities: typeof e.interpretNumericEntities == "boolean" ? e.interpretNumericEntities : O.interpretNumericEntities,
    parameterLimit: typeof e.parameterLimit == "number" ? e.parameterLimit : O.parameterLimit,
    parseArrays: e.parseArrays !== !1,
    plainObjects: typeof e.plainObjects == "boolean" ? e.plainObjects : O.plainObjects,
    strictNullHandling: typeof e.strictNullHandling == "boolean" ? e.strictNullHandling : O.strictNullHandling
  };
}, zn = function(r, e) {
  var t = Gn(e);
  if (r === "" || r === null || typeof r > "u")
    return t.plainObjects ? /* @__PURE__ */ Object.create(null) : {};
  for (var n = typeof r == "string" ? Wn(r, t) : r, o = t.plainObjects ? /* @__PURE__ */ Object.create(null) : {}, a = Object.keys(n), c = 0; c < a.length; ++c) {
    var u = a[c], i = jn(u, n[u], t, typeof r == "string");
    o = X.merge(o, i, t);
  }
  return t.allowSparse === !0 ? o : X.compact(o);
}, Hn = Bn, qn = zn, Vn = ke, ur = {
  formats: Vn,
  parse: qn,
  stringify: Hn
}, cr = {}, Qn = {
  get exports() {
    return cr;
  },
  set exports(r) {
    cr = r;
  }
};
/* NProgress, (c) 2013, 2014 Rico Sta. Cruz - http://ricostacruz.com/nprogress
 * @license MIT */
(function(r, e) {
  (function(t, n) {
    r.exports = n();
  })(Lr, function() {
    var t = {};
    t.version = "0.2.0";
    var n = t.settings = {
      minimum: 0.08,
      easing: "ease",
      positionUsing: "",
      speed: 200,
      trickle: !0,
      trickleRate: 0.02,
      trickleSpeed: 800,
      showSpinner: !0,
      barSelector: '[role="bar"]',
      spinnerSelector: '[role="spinner"]',
      parent: "body",
      template: '<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>'
    };
    t.configure = function(f) {
      var s, g;
      for (s in f)
        g = f[s], g !== void 0 && f.hasOwnProperty(s) && (n[s] = g);
      return this;
    }, t.status = null, t.set = function(f) {
      var s = t.isStarted();
      f = o(f, n.minimum, 1), t.status = f === 1 ? null : f;
      var g = t.render(!s), v = g.querySelector(n.barSelector), b = n.speed, w = n.easing;
      return g.offsetWidth, u(function(S) {
        n.positionUsing === "" && (n.positionUsing = t.getPositioningCSS()), i(v, c(f, b, w)), f === 1 ? (i(g, {
          transition: "none",
          opacity: 1
        }), g.offsetWidth, setTimeout(function() {
          i(g, {
            transition: "all " + b + "ms linear",
            opacity: 0
          }), setTimeout(function() {
            t.remove(), S();
          }, b);
        }, b)) : setTimeout(S, b);
      }), this;
    }, t.isStarted = function() {
      return typeof t.status == "number";
    }, t.start = function() {
      t.status || t.set(0);
      var f = function() {
        setTimeout(function() {
          t.status && (t.trickle(), f());
        }, n.trickleSpeed);
      };
      return n.trickle && f(), this;
    }, t.done = function(f) {
      return !f && !t.status ? this : t.inc(0.3 + 0.5 * Math.random()).set(1);
    }, t.inc = function(f) {
      var s = t.status;
      return s ? (typeof f != "number" && (f = (1 - s) * o(Math.random() * s, 0.1, 0.95)), s = o(s + f, 0, 0.994), t.set(s)) : t.start();
    }, t.trickle = function() {
      return t.inc(Math.random() * n.trickleRate);
    }, function() {
      var f = 0, s = 0;
      t.promise = function(g) {
        return !g || g.state() === "resolved" ? this : (s === 0 && t.start(), f++, s++, g.always(function() {
          s--, s === 0 ? (f = 0, t.done()) : t.set((f - s) / f);
        }), this);
      };
    }(), t.render = function(f) {
      if (t.isRendered())
        return document.getElementById("nprogress");
      p(document.documentElement, "nprogress-busy");
      var s = document.createElement("div");
      s.id = "nprogress", s.innerHTML = n.template;
      var g = s.querySelector(n.barSelector), v = f ? "-100" : a(t.status || 0), b = document.querySelector(n.parent), w;
      return i(g, {
        transition: "all 0 linear",
        transform: "translate3d(" + v + "%,0,0)"
      }), n.showSpinner || (w = s.querySelector(n.spinnerSelector), w && m(w)), b != document.body && p(b, "nprogress-custom-parent"), b.appendChild(s), s;
    }, t.remove = function() {
      y(document.documentElement, "nprogress-busy"), y(document.querySelector(n.parent), "nprogress-custom-parent");
      var f = document.getElementById("nprogress");
      f && m(f);
    }, t.isRendered = function() {
      return !!document.getElementById("nprogress");
    }, t.getPositioningCSS = function() {
      var f = document.body.style, s = "WebkitTransform" in f ? "Webkit" : "MozTransform" in f ? "Moz" : "msTransform" in f ? "ms" : "OTransform" in f ? "O" : "";
      return s + "Perspective" in f ? "translate3d" : s + "Transform" in f ? "translate" : "margin";
    };
    function o(f, s, g) {
      return f < s ? s : f > g ? g : f;
    }
    function a(f) {
      return (-1 + f) * 100;
    }
    function c(f, s, g) {
      var v;
      return n.positionUsing === "translate3d" ? v = { transform: "translate3d(" + a(f) + "%,0,0)" } : n.positionUsing === "translate" ? v = { transform: "translate(" + a(f) + "%,0)" } : v = { "margin-left": a(f) + "%" }, v.transition = "all " + s + "ms " + g, v;
    }
    var u = function() {
      var f = [];
      function s() {
        var g = f.shift();
        g && g(s);
      }
      return function(g) {
        f.push(g), f.length == 1 && s();
      };
    }(), i = function() {
      var f = ["Webkit", "O", "Moz", "ms"], s = {};
      function g(S) {
        return S.replace(/^-ms-/, "ms-").replace(/-([\da-z])/gi, function(A, E) {
          return E.toUpperCase();
        });
      }
      function v(S) {
        var A = document.body.style;
        if (S in A)
          return S;
        for (var E = f.length, R = S.charAt(0).toUpperCase() + S.slice(1), $; E--; )
          if ($ = f[E] + R, $ in A)
            return $;
        return S;
      }
      function b(S) {
        return S = g(S), s[S] || (s[S] = v(S));
      }
      function w(S, A, E) {
        A = b(A), S.style[A] = E;
      }
      return function(S, A) {
        var E = arguments, R, $;
        if (E.length == 2)
          for (R in A)
            $ = A[R], $ !== void 0 && A.hasOwnProperty(R) && w(S, R, $);
        else
          w(S, E[1], E[2]);
      };
    }();
    function l(f, s) {
      var g = typeof f == "string" ? f : d(f);
      return g.indexOf(" " + s + " ") >= 0;
    }
    function p(f, s) {
      var g = d(f), v = g + s;
      l(g, s) || (f.className = v.substring(1));
    }
    function y(f, s) {
      var g = d(f), v;
      l(f, s) && (v = g.replace(" " + s + " ", " "), f.className = v.substring(1, v.length - 1));
    }
    function d(f) {
      return (" " + (f.className || "") + " ").replace(/\s+/gi, " ");
    }
    function m(f) {
      f && f.parentNode && f.parentNode.removeChild(f);
    }
    return t;
  });
})(Qn);
function Jn(r, e, t, n = "brackets") {
  let o = /^https?:\/\//.test(e.toString()), a = o || e.toString().startsWith("/"), c = !a && !e.toString().startsWith("#") && !e.toString().startsWith("?"), u = e.toString().includes("?") || r === "get" && Object.keys(t).length, i = e.toString().includes("#"), l = new URL(e.toString(), "http://localhost");
  return r === "get" && Object.keys(t).length && (l.search = ur.stringify(et(ur.parse(l.search, { ignoreQueryPrefix: !0 }), t), { encodeValuesOnly: !0, arrayFormat: n }), t = {}), [[o ? `${l.protocol}//${l.host}` : "", a ? l.pathname : "", c ? l.pathname.substring(1) : "", u ? l.search : "", i ? l.hash : ""].join(""), t];
}
function Kn(r) {
  let e = r.currentTarget.tagName.toLowerCase() === "a";
  return !(r.target && (r == null ? void 0 : r.target).isContentEditable || r.defaultPrevented || e && r.which > 1 || e && r.altKey || e && r.ctrlKey || e && r.metaKey || e && r.shiftKey);
}
const Yn = /* @__PURE__ */ Mr({
  __name: "TypedLink",
  props: {
    to: Object,
    data: Object,
    as: {
      type: String,
      default: "a"
    },
    queryStringArrayFormat: {
      type: String,
      default: "brackets"
    },
    method: {
      type: String,
      default: "get"
    },
    replace: {
      type: Boolean,
      default: !1
    },
    preserveScroll: {
      type: Boolean,
      default: !1
    },
    preserveState: {
      type: Boolean,
      default: null
    },
    only: {
      type: Array,
      default: () => []
    },
    headers: {
      type: Object,
      default: () => ({})
    }
  },
  setup(r) {
    const e = r, { route: t, ifetch: n } = xr(), o = e.as.toLowerCase(), a = e.method.toLowerCase(), c = Ge(() => {
      const y = e.to;
      return t(y);
    }), u = Ge(() => e.data || {});
    Tr();
    const [i, l] = Jn(a, c.value || "", u.value, e.queryStringArrayFormat);
    o === "a" && a !== "get" && console.warn(
      `Creating POST/PUT/PATCH/DELETE <a> links is discouraged as it causes "Open Link in New Tab/Window" accessibility issues.

Please specify a more appropriate element using the "as" attribute. For example:

<Link href="${i}" method="${a}" as="button">...</Link>`
    );
    const p = (y) => {
      Kn(y) && (y.preventDefault(), n.get(e.to));
    };
    return (y, d) => (Nr(), Cr(Br(ze(o)), {
      href: ze(i),
      onClick: _r(p, ["stop"])
    }, {
      default: Ur(() => [
        Dr(y.$slots, "default")
      ]),
      _: 3
    }, 8, ["href", "onClick"]));
  }
}), ea = {
  install: (r) => {
    const e = xr();
    return r.config.globalProperties.$route = e.route, r.config.globalProperties.$isRoute = e.isRoute, r.config.globalProperties.$currentRoute = e.currentRoute, r.provide("inertia", {
      route: r.config.globalProperties.$route,
      isRoute: r.config.globalProperties.$isRoute,
      currentRoute: r.config.globalProperties.$currentRoute
    }), r.component("Head", $r), r.component("Link", Ir), r.component("TypedLink", Yn), r;
  }
}, ra = (r, e) => e[`./Pages/${r}.vue`], ta = (r, e) => {
  var n;
  let t = ((n = window.document.getElementsByTagName("title")[0]) == null ? void 0 : n.innerText) || "Laravel";
  return e && (t = e), `${r} - ${t}`;
}, xr = () => {
  const r = window.Routes, e = (i) => r[i.name].path, t = {
    get: (i, l) => re.get(e(i), l),
    post: (i, l) => re.post(e(i), l),
    patch: (i, l) => re.patch(e(i), l),
    put: (i, l) => re.put(e(i), l),
    delete: (i) => re.delete(e(i))
  }, n = Fr(), o = () => {
    const i = location.pathname, p = Object.entries(r).find((y) => y[1].path === i);
    if (p)
      return p[1];
  };
  return {
    ifetch: t,
    route: (i) => {
      const l = r[i.name];
      if (l.params) {
        const p = {};
        Object.entries(l.params).forEach(([m]) => {
          i.params && (p[m] = i.params[m]);
        });
        let y = l.path;
        const d = l.path.match(/{(.*?)}/g);
        return d && d.forEach((m) => {
          const f = m.replace("{", "").replace("}", "");
          y = y.replace(m, p[f]);
        }), y;
      }
      return l.params ? "/" : l.path;
    },
    isRoute: (i) => !!Object.entries(r).find((y) => y[1].name === i),
    currentRoute: () => o(),
    page: n
  };
};
export {
  ea as InertiaTyped,
  Yn as TypedLink,
  ra as appResolve,
  ta as appTitle,
  xr as useInertia
};
