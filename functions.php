<?php

if (session_id()=="") { session_start(); }
foreach ($_GET  as $key => $value) { $_REQ[$key]=$value; }
foreach ($_POST as $key => $value) { $_REQ[$key]=$value; }
$PHP_SELF = basename($_SERVER['PHP_SELF']);


Function ResultBox($text) {
  if (trim($text)=="") { return false;  }
  return('<div align="center"><div class="resultBox">'.$text.'</div></div>'."\r\n");
};

Function mkFormField($txtLabel, $txtName, $defValue="", $maxLenght=50, $classLabel="sText1", $classField="sField1") { global $_REQ;
  $defValue=GetFormData($_REQ[$txtName], $nothing ,$defValue);
  echo('<tr>');
  echo('<td width="27%" class="'.$classLabel.'">'.$txtLabel.':</td>');
  echo('<td width="70%" class="'.$classField.'"><input class="t1" type="text" name="'.$txtName.'" maxlength="'.$maxLenght.'" value="'.$defValue.'" /></td>');
  echo('<td width="3%"  class="'.$classLabel.'" >&nbsp;</td>');
  echo('</tr>'."\r\n");
}

Function mkFormTitle($txtTitle, $align="center", $spanCols="3") {
  echo('<tr><td class="sHeader" align="'.$align.'" colspan="'.$spanCols.'">'.$txtTitle.'</td></tr>'."\r\n");
}

Function mkFormBtns($saveLabel="Submit Form", $spanCols="3", $hasBack=false) {
  echo('<tr><td colspan="'.$spanCols.'" class="sFinal"><input class="b1" type="submit" name="bSubmit" value="'.$saveLabel.'" /><input class="b1" type="reset" name="bReset" value="Restore Form" />');
  if ($hasBack) { echo('<input class="b1" type="button" value="Cancel Edit" onclick="javascript:history.back();" />'); }
  echo('</td></tr>'."\r\n");
}

Function DataRow($width, $class1, $class2, $label, $text, $g=false) {
  if (trim($text)=="") { $text="<font class=\"Disabled\">n/a.</font>"; }
  if ($g){ return("\r\n<tr><td width=\"".$width."%\" class=\"".$class1."\">".$label.":</td><td width=\"".(100-$width)."%\" class=\"".$class2."\">".nl2br($text)."</td></tr>"); }
  else   {   echo("\r\n<tr><td width=\"".$width."%\" class=\"".$class1."\">".$label.":</td><td width=\"".(100-$width)."%\" class=\"".$class2."\">".nl2br($text)."</td></tr>"); }
};

Function GetFormData(&$postValue, &$dataValue, $default="") {
  if (isset($postValue)) { return ($postValue); }
  if (isset($dataValue)) { return ($dataValue); }
  if ($default!="") { return ($default); }
};

Function qFix($value,$dotrim=true) {
  if ($dotrim) { $value=trim($value); }
  $value=stripslashes($value);
  $value=stripslashes($value);
  $value=str_replace("'","''",$value);
  return ($value);
};

Function Put($text) {
  if ($text=="") { return('<font class="Disabled">n/a.</font>'); } else { return($text); }
};
?>