<?php

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

$config = [
 "server" => "http://fizzbuzz-http.s.zeid.me/server.php?"
];

if (is_file("client.conf"))
 $config = array_merge($config, parse_ini_file("client.conf"));

function request($url = "", $method = "", $_get = [], $_post = []) {
 global $config;
 $j = file_get_contents("{$config["server"]}{$url}");
 return json_decode($j, true);
}

function fizzbuzz() {
 for ($i  = (int) request("/start");
      $i <= (int) request("/end");
      $i += (int) request("/step")) {
  $line = "";
  if (request("/divisible-by/$i/".request("/fizz/number")))
   $line .= request("/fizz/text");
  if (request("/divisible-by/$i/".request("/buzz/number")))
   $line .= request("/buzz/text");
  if (empty($line))
   $line = (string) $i;
  echo "$line\n";
  flush();
 }
}

function main() {
 ini_set("implicit_flush", "On");
 
 if (PHP_SAPI !== "cli"): ?>
<!DOCTYPE html><!-- vim: set fdm=marker: -->

<html>
 <head>
 <meta charset="utf-8" />
 <!-- Copyright notice and X11 License {{{
   
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
   
 --><!-- }}} -->
  <title><?php echo request("/fizz/text").request("/buzz/text"); ?></title>
  <link rel="stylesheet" type="text/css" href="https://s.zeid.me/styles/basic.css" />
 </head>
 <body>
  <h1 id="title"><?php echo request("/fizz/text").request("/buzz/text"); ?></h1>
  <pre id="body"><?php
 endif;
 
 fizzbuzz();
 
 if (PHP_SAPI !== "cli"): ?></pre>
 </body>
</html>
<?php
 endif;
}

main();

?>
