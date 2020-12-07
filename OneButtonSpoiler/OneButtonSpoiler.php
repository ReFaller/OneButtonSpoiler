<?php
$defLang="en";
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
switch ($lang){
       case "ru":
           $defLang="ru";
           break;
       case "be":
           $defLang="ru";
           break;
       case "uk":
           $defLang="ru";
           break;    
       case "ky":
           $defLang="ru";
           break; 
       case "ab":
           $defLang="ru";
           break; 
       case "mo":
           $defLang="ru";
           break; 
       case "et":
           $defLang="ru";
           break; 
       case "lv":
           $defLang="ru";
           break; 
       default:
           //
           break;
}

# MediaWiki OneButtonSpoiler spoiler extension 
# <spoiler> some text </spoiler>
# <spoiler text="something"> some text </spoiler>
# the function registered by the extension hides the text between the
# tags behind a JavaScript spoiler block.
#
# Based on the work of Brian Enigma: http://vanishingpointwiki.com/wiki/MediaWiki:Spoiler and JSpoiler https://www.mediawiki.org/wiki/Extension:Spoilers Tim "Telshin" Aldridge, Kris "Developaws" Blair
#
# (C) Copyright 2009, ddsx <ddsx@live.it>
# This work is licensed under a Creative Commons Attribution-Noncommercial-Share 
# Alike 2.5 License.  Some rights reserved.
# https://creativecommons.org/licenses/by-nc-sa/2.5/

# Configuration:
if($defLang=="ru"){
$defaultText = "Показать спойлер";
}else{
$defaultText = "Show spoiler";
}
$spoilerCSS = "border: 1px dashed #000000; background-color: #EEEEF2; padding: 3px;";
$spoilerCSS = "border: 1px solid rgba(0,0,0,0.1); background-color: rgba(230,240,250,0.05); padding: 3px; display: none; color: #C9C9C9;";
$spoilerHeaderCSS = "font-size: 135%; color: #000;";
$spoilerLinkCSS = "	background: #AAA; background: -moz-linear-gradient(top, #EEE, #AAA); background: -webkit-linear-gradient(top, #EEE, #AAA); background: -o-linear-gradient(top, #EEE, #AAA); background: linear-gradient(to bottom, #EEE, #AAA); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#EEE', endColorstr='#AAA', GradientType=0); border: 1px solid #999; border-radius: 5px; padding: 3px 10px; transition: 0.1s background-position linear; font-size: 100%; color: #000;";
# End Configuration

$wgExtensionFunctions[] = "wfSpoilerExtension";
$wgHooks['OutputPageBeforeHTML'][] = 'spoilerParserHook' ;
$OneButtonSpoilerVersion = '1.0';
$wgExtensionCredits['parserhook'][] = array(
	'name'=>'OneButtonSpoiler',
	'version'=>$OneButtonSpoilerVersion,
	'author'=>'ddsx, Thomas Candrian',
	'url'=>'https://www.mediawiki.org/wiki/Extension:OneButtonSpoiler',
	'description' => htmlentities('Adds a <spoiler [text="string"]> tag')
    );
 
function wfSpoilerExtension() {
    global $wgParser;
    # register the extension with the WikiText parser
    $wgParser->setHook( "spoiler", "renderSpoiler" );
}
 
function wfSpoilerJavaScript() {
        global $spoilerCSS;
        global $spoilerHeaderCSS;
        global $spoilerLinkCSS;
return  "<script language=\"JavaScript\">\n" .
        "\n" . 
        "function getStyleObject(objectId) {\n" .
        "    // checkW3C DOM, then MSIE 4, then NN 4.\n" .
        "    //\n" .
        "    if(document.getElementById) {\n" .
        "      if (document.getElementById(objectId)) {\n" .
        "	     return document.getElementById(objectId).style;\n" .
        "      }\n" . 
        "    } else if (document.all) {\n" .
        "      if (document.all(objectId)) {\n" .
        "	     return document.all(objectId).style;\n" .
        "      }\n" . 
        "    } else if (document.layers) { \n" . 
        "      if (document.layers[objectId]) { \n" .
        "	     return document.layers[objectId];\n" .
        "      }\n" . 
        "    } else {\n" .
        "	   return false;\n" .
        "    }\n" .
        "}\n" .
        "\n" .
        "function toggleObjectVisibility(objectId) {\n" .
        "    // first get the object's stylesheet\n" .
        "    var styleObject = getStyleObject(objectId);\n" .
        "\n" .
        "    // then if we find a stylesheet, set its visibility\n" .
        "    // as requested\n" .
        "    //\n" .
        "    if (styleObject) {\n" .
        "        if (styleObject.display == 'none') {\n" .
        "            styleObject.display = 'block';\n" .
        "        } else {\n" .
        "            styleObject.display = 'none';\n" .
        "        }\n" .
        "        return true;\n" .
        "    } else {\n" .
        "        return false;\n" .
        "    }\n" .
        "}\n" .
        "</script>\n" .
        "<style type=\"text/css\"><!--\n" .
        "div.spoiler {" . $spoilerCSS . "}\n" .
        "span.spoilerHeader {" . $spoilerHeaderCSS . "}\n" . 
        "button.spoilerLink {" . $spoilerLinkCSS . "}\n" . 
        "--></style>\n";
}
 
function spoilerParserHook( &$parser , &$text ) { 
    $text = wfSpoilerJavaScript() . $text;
    return true;
}
 
function wfMakeSpoilerId() {
    $result = "";
    for ($i=0; $i<20; $i++)
        $result .= chr(rand(ord('A'), ord('Z')));
    return $result;
}
 
# The callback function for converting the input text to HTML output
function renderSpoiler( $input, $argv, $parser ) {
    global $defaultText;
    $localParser = new Parser();
    $outputObj = $localParser->parse($input, $parser->mTitle, $parser->mOptions);
    $spoilerId = wfMakeSpoilerId();
    $output  = "<button href=\"#\"onclick=\"toggleObjectVisibility('" . $spoilerId . "'); return false;\" class=\"spoilerLink\">";
    if (!isset($argv['text']) or $argv["text"] == "") {
			$output .= $defaultText . "</button>\n";
		} else {
			$output .= $argv["text"] . "</button>\n";
		}
    $output .= "<div id=\"" . $spoilerId . "\" class=\"spoiler\" style=\"display: none;\">\n";
    $output .= $outputObj->getText() . "\n";
    $output .= "</div>\n";
    return $output;
}