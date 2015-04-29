<?php

/* vim: set fdm=marker: */
/* Copyright notice and X11 License {{{
   
   fizzbuzz-api
   
   Copyright (C) 2014-2015 Scott Zeid.
   http://code.s.zeid.me/fizzbuzz-api
   
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

require("app.php");

$app = new App("fizzbuzz-http/server.php");

$app->get(["/start"], function($params) {
 return result(1);
});

$app->get(["/end"], function($params) {
 return result(100);
});

$app->get(["/step"], function($params) {
 return result(1);
});

$app->get(["/divisible-by/:a/:b"], function($params) {
 return result($params["a"] % $params["b"] == 0);
});

$app->get(["/fizz/:what"], function($params) {
 $what = $params["what"];
 if ($what == "number")
  return result(3);
 if ($what == "text")
  return result(json_encode("Fizz"));
});

$app->get(["/buzz/:what"], function($params) {
 $what = $params["what"];
 if ($what == "number")
  return result(5);
 if ($what == "text")
  return result(json_encode("Buzz"));
});

////

function capture($prefix = "", $url = "", $method = "", $_get = [], $_post = []) {
 global $app;
 ob_start();
 $app->handle($prefix, $url, $method, $_get, $_post);
 $j = ob_get_contents();
 ob_end_clean();
 return json_decode($j, true);
}

if (PHP_SAPI !== "cli")
 $app->handle();
else {
 ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
 ini_set("display_errors", "stderr");
 $_argc = $_SERVER["argc"]; $_argv = $_SERVER["argv"];
 
 if ($_argc < 2) {
  for ($i  = (int) capture("", "/start");
       $i <= (int) capture("", "/end");
       $i += (int) capture("", "/step")) {
   $line = "";
   if (capture("", "/divisible-by/$i/".capture("", "/fizz/number")))
    $line .= capture("", "/fizz/text");
   if (capture("", "/divisible-by/$i/".capture("", "/buzz/number")))
    $line .= capture("", "/buzz/text");
   if (empty($line))
    $line = (string) $i;
   echo "$line\n";
  }
  exit(0);
 }
 $c = $app->handle_cli($_argc, $_argv, "");
 echo "\n";
 if ($c >= 400)
  exit(1);
 exit(0);
}

?>
