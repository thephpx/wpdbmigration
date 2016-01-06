<?php 
/*
 * 
 * The MIT License (MIT)
 * Copyright (c) 2016 Faisal Ahmed
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files 
 * (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, 
 * publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do 
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * 
 * This script can be used as migration script generator for mysql dump file to replace domain and path values.
 *
 * @author Faisal Ahmed <thephpx@gmail.com>
 * @license https://opensource.org/licenses/MIT
 */

/* Prodive old and new dump file names */
$dump = "db.sql";
$dump_new = "db_new.sql";

/* Provide old and new domain replacement values */
$domain_o = rtrim("http://abc.com","/");
$domain_n = rtrim("http://xyz.com","/");

/* Provide old and new path replacement values */
$path_o = rtrim(""/home/abc/public_html","/");
$path_n = rtrim(""/home/xyz/public_html","/");

/* Calculate string delta */
$domain_d = strlen($domain_n) - strlen($domain_o);
$path_d = strlen($path_n) - strlen($path_o);

/* load sql dump string */
$data = file_get_contents($dump);

/* match domain serialized values */
$expression = '/s:(.[^;]*):"('.preg_quote($domain_o,'/').')(.[^"]*)"/';
preg_match_all($expression, $data,$match);

/* replace domain serialized values */
for($i=0;$i<count($match[0]);$i++)
{
	$data = str_replace('s:'.$match[1][$i].':"'.$match[2][$i],'s:'.($match[1][$i] + $domain_d).':"'.$domain_n,$data);
}

/* match path serialized values */
$expression = '/s:(.[^;]*):"('.preg_quote($path_o,'/').')(.[^"]*)"/';
preg_match_all($expression, $data,$match);

/* replace path serialized values */
for($i=0;$i<count($match[0]);$i++)
{
	$data = str_replace('s:'.$match[1][$i].':"'.$match[2][$i],'s:'.($match[1][$i] + $path_d).':"'.$path_n,$data);
}

/* basic replacement of domain from within attributes */
$data = str_replace('="'.$domain_o,'="'.$domain_n,$data);
/* basic replacement of path from within attributes */
$data = str_replace('="'.$path_o,'="'.$path_n,$data);

/* basic replacement of domain */
$data = str_replace($domain_o,$domain_n,$data);

/* basic replacement of path */
$data = str_replace($path_o,$path_n,$data);

/* write updated dump to new file */
file_put_contents($dump_new, $data);
