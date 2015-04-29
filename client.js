/* vim: set fdm=marker: */
/* Copyright notice and X11 License {{{
   
   fizzbuzz-http sample client
   
   Copyright (C) 2014-2015 Scott Zeid.
   http://code.s.zeid.me/fizzbuzz-http
   
   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:
   
   The above copyright notice and this permission notice shall be included in
   all copies or substantial portions of the Software.
   
   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
   THE SOFTWARE.
   
   Except as contained in this notice, the name(s) of the above copyright holders
   shall not be used in advertising or otherwise to promote the sale, use or
   other dealings in this Software without prior written authorization.
   
}}}*/

var BROWSER = (typeof document != "undefined");

function fizzbuzz(server, output_el) {
 function output(what) {
  if (output_el) {
   var span = document.createElement("span");
   span.innerHTML = stripHTML(what);
   output_el.appendChild(span);
   output_el.appendChild(document.createElement("br"));
  } else
   console.log(what);
 }
 function get(url, success) {
  request(server + url, null, null, success);
 }
 
 get("/start", function(start) {
  get("/end", function(end) {
   get("/step", function(step) {
    function iteration(i) {
     if (i <= end) {
      get("/fizz/number", function(fizzNumber) {
       get("/buzz/number", function(buzzNumber) {
        get("/fizz/text", function(fizzText) {
         get("/buzz/text", function(buzzText) {
          get("/divisible-by/" + i + "/" + fizzNumber, function(fizzDivisible) {
           get("/divisible-by/" + i + "/" + buzzNumber, function(buzzDivisible) {
            var line = "";
            if (fizzDivisible)
             line += fizzText;
            if (buzzDivisible)
             line += buzzText;
            if (line == "")
             line = i;
            output(line);
            iteration(i + step);
           });
          });
         });
        });
       });
      });
     } else
      return;
    }
    iteration(start);
   });
  });
 });
}

function get(url, success) {
 request(url, null, null, success);
}

function request(url, method, params, success, error) {
 method = (method || "GET").toUpperCase();
 success = success || console.log;
 error = error || console.log;
 function data() {
  var data = "";
  for (var k in params) {
   if (params.hasOwnProperty(k))
    data += "&" + encodeURIComponent(k) + "=" + encodeURIComponent(params[k]);
  }
  data = data.substr(1);
  return data;
 }
 if (BROWSER) {
  var request = new XMLHttpRequest();
  request.open(method, url, true);
  console.log(url);
  request.onreadystatechange = function() {
   if (this.readyState === 4) {
    var response = this.responseText;
    try { response = JSON.parse(response) } catch (e) {}
    if (this.status >= 200 && this.status < 400)
     success(response);
    else
     error(response);
   }
  }
  if (params != null && typeof(params) === "object") {
   request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
   request.send(data());
  } else
   request.send(null);
  request = null;
 } else {
  var options = require("url").parse(url);
  options.method = method;
  options.headers = {"User-Agent": "fizzbuzz-http/client.js"};
  if (options.hostname.indexOf(":") >= 0)
   options.headers["Host"] = "[" + options.hostname + "]";
  if (params != null && typeof(params) === "object")
   options.headers["Content-Type"] = "application/x-www-form-urlencoded";
  var request = require("http").request(options, function(res) {
   res.setEncoding("utf8");
   var body = "";
   res.on("readable", function() {
    body += res.read();
   });
   res.on("end", function() {
    try { body = JSON.parse(body) } catch (e) {}
    if (res.statusCode >= 200 && res.statusCode < 400)
     success(body);
    else
     error(body);
   });
   res.on("error", function(e) {
    error(e);
   });
  });
  if (params != null && typeof(params) === "object") {
   request.write(data());
  }
  request.end();
 }
};

function stripHTML(html) {
 html = String(html);
 return html.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;");
}

if (!BROWSER) {
 var server = "http://fizzbuzz-http.s.zeid.me/server.php?";
 if (typeof process != "undefined" && typeof process.argv == "object") {
  var path = require("path");
  if (path.resolve(process.argv[0]) == path.resolve(process.execPath))
   var server = process.argv[1] || server;
  else
   var server = process.argv[2] || server;
 }
 fizzbuzz(server);
}
