<?php
require_once("./functions.php");

if (isset($_REQ['s']))  { $s=$_REQ['s'];   } if(!isset($s))  { $s=""; }
if (isset($_REQ['ID'])) { $ID=$_REQ['ID']; } if(!isset($ID)) { $ID=0; }
if (isset($_SESSION['result'])) { $resultBox=$_SESSION['result']; unset($_SESSION['result']); }

LoadSettings();
if ((isset($_SESSION['db']))&&($_SESSION['db']!="")) { $db=$_SESSION['db']; };

if (function_exists('mysqli_connect')) {
  $dbTest = @mysqli_connect($db['Hostname'], $db['Username'], $db['Password']); 
  if ($dbTest) { $dbLink = @mysqli_connect($db['Hostname'], $db['Username'], $db['Password'], $db['Database']); } else { $dbLink=false; }
  if ($dbLink) { $dbTable= @$dbLink->Query("SHOW TABLES LIKE 'contacts';"); } else { $dbTable=false; }
}

if (basename(__FILE__)==$PHP_SELF) { switch ($s) {
  case "dbSave" : SaveSettings();  break;
  case "Add"    : ContactsEdit();  break;
  case "Edit"   : ContactsEdit();  break;
  case "Save"   : ContactsSave();  break;
  case "Delete" : ContactsDelete();break;
  case "crDb"   : CreateDatabase();break;
  case "crTable": CreateTable();   break;
  case "dpTable": DropTable();     break;
  case "crDummy": CreateDummies(); break;
  default       : Display();       break;
}; };

if ($dbTest) { @$dbTest->close(); }
if ($dbLink) { @$dbLink->close(); }

Function bHeader() { global $_REQ, $resultBox;
  if (!headers_sent()) { echo('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\r\n"); }; ?>
  <html>
   <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta http-equiv="Content-Language" content="en">
    <title>Simple phpAgenda App</title>
    <link rel="stylesheet" href="./style.css" type="text/css">
   </head>
   <body text="#000000" vlink="#000000" link="#000000" alink="#000000"><?php
   if ($resultBox!="") { echo ($resultBox); } 
}

Function bFooter() { ?>
   </body>
  </html><?php
}

Function Display() { global $_REQ, $resultBox, $ID, $s, $PHP_SELF, $db, $dbLink, $dbTest, $dbTable;
  bHeader() ?>
 <div align="center"><table border="0" width="90%" cellpadding="0" cellspacing="0"><tr><td width="60%" align="left" valign="top">
  <form method="post" action="<?= $PHP_SELF ?>" style="margin:0px;">
    <input type="hidden" name="s"  value="dbSave" />
  <table width="98%" cellpadding="1" cellspacing="1" class="sTable2"><?php
   mkFormTitle('Database Configuration Settings');
   mkFormField('MySQL Host'    ,'txtdbHost',GetFormData($_REQ['txtdbHost'], $db['Hostname']));
   mkFormField('MySQL Username','txtdbUser',GetFormData($_REQ['txtdbUser'], $db['Username']));
   mkFormField('MySQL Password','txtdbPass',GetFormData($_REQ['txtdbPass'], $db['Password']));
   mkFormField('MySQL Database','txtdbBase',GetFormData($_REQ['txtdbBase'], $db['Database']));
   mkFormBtns('Save Settings & Connect');
  ?></table></form></div>
  </td><td width="40%" align="right" valign="top">
  <table width="98%" cellpadding="1" cellspacing="1" class="sTable2"><?php 
    mkFormTitle("Database connection status","center");
    DataRow(45,"sText1","sField1","Found Config File", file_exists('config.php')?'Yep, its there':'Nope, not found');
    DataRow(45,"sText1","sField1","MySQL module loaded", function_exists('mysqli_connect')?'Yep, module loaded':'Nope, module not found');
    DataRow(45,"sText1","sField1","Connected to MySQL", $dbTest?'Yep, connected':'Nope, could not connect <br/>'.mysqli_connect_error());
    DataRow(45,"sText1","sField1","Database created", $dbLink?'Yep, database is there':'Nope, database not found <br/>'.mysqli_connect_error());
    DataRow(45,"sText1","sField1","Table Contacts created", @$dbTable->num_rows?'Yep, table is there':'Nope, table not found');
    ?>
    <tr><td class="sField1" colspan="2" style="text-align:center;padding:5px;">
    <a href="?s=crDb">Create Database</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="?s=crTable">Create Table</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="?s=dpTable">Drop Table</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="?s=crDummy">Create Dummy Contacts</a></td>
  </table>
  </td></tr></table><?php
  if (@$dbTable->num_rows) { ?>
   <hr style="width:95%; height:1px; border:5px; background-color:black; color:black; margin:15px 0px;">
   <table width="90%" cellpadding="1" cellspacing="1" class="sTable2">
    <tr><td class="sHeader" align="center" colspan="6" width="80%">Contacts Records</td><td class="sHeader" align="center" width="10%"><a href="?s=Add">Add New</a></td></tr>
    <?php
    $result=mysqli_query($dbLink, "SELECT * FROM contacts ORDER BY ID;");
    if (($result)&&(mysqli_num_rows($result)>0)) { $sc="sRow1";
      echo('<tr><td class="sHeader" align="center" width="5%">ID</td>');
      echo('<td class="sHeader" align="center" width="15%">First Name</td>');
      echo('<td class="sHeader" align="center" width="15%">Last Name</td>');
      echo('<td class="sHeader" align="center" width="15%">Phone</td>');
      echo('<td class="sHeader" align="center" width="25%">Notes</td>');
      echo('<td class="sHeader" align="center" width="15%">Date Added</td>');
      echo('<td class="sHeader" align="center" width="10%">Actions</td></tr>'."\r\n");
      while ($row = mysqli_fetch_assoc($result)) {
        $sc = $sc=="sRow1"?$sc="sRow2":$sc="sRow1"; ?>
        <tr class="<?= $sc ?>">
        <td class="sData1c"><?= Put($row['ID']) ?></td>
        <td class="sData1l"><?= Put($row['FirstName']) ?></td>
        <td class="sData1l"><?= Put($row['LastName']) ?></td>
        <td class="sData1l"><?= Put($row['Phone']) ?></td>
        <td class="sData1l"><?= Put($row['Notes']) ?></td>
        <td class="sData1l"><?= Put($row['DateAdded']) ?></td>
        <td class="sData1c"><a href="?s=Edit&amp;ID=<?= $row['ID'] ?>">Edit</a>&nbsp;&nbsp;<a href="?s=Delete&amp;ID=<?= $row['ID'] ?>">Delete</a></td>
        </tr><?php
      }; ?><tr><td class="sSummary" colspan="7">Total Records:<?= mysqli_num_rows($result)?></td></tr><?php
      mysqli_free_result($result); 
    } else {
      echo('<tr><td colspan="7" class="sFinal">No rows to display.</td></tr>'."\r\n");
    };
    ?></table><?php
  };
  ?></div><?php
  bFooter();
}

Function SaveSettings() { global $_REQ, $resultBox, $PHP_SELF;
  $db['Hostname']=$_REQ['txtdbHost'];
  $db['Username']=$_REQ['txtdbUser'];
  $db['Password']=$_REQ['txtdbPass'];
  $db['Database']=$_REQ['txtdbBase'];
  $_SESSION['db']=$db;
  if (file_put_contents('config.php', '<?php $config = ' . var_export($db, true) . ';')) {
    $resultBox=ResultBox('Configuration file saved correctly.');
    $_SESSION['result']=$resultBox;
  } else {
    $resultBox=ResultBox('Cannot write configuration file.');
    $_SESSION['result']=$resultBox;
  }
  header("Location: ".$PHP_SELF); exit;
}

Function LoadSettings() {
  if (file_exists('config.php')) {
    @include_once('config.php');
    if (isset($config)) { $_SESSION['db']=$config; }
  }  
}

Function CreateDatabase() { global $_REQ, $resultBox, $PHP_SELF, $db, $dbTest;
  $sql = "CREATE DATABASE ".$db['Database'];
  if (mysqli_query($dbTest, $sql)) {
    $resultBox=ResultBox("Database created successfully");
  } else {
    $resultBox=ResultBox("Error creating database: " . mysqli_error($dbTest));
  }
  $dbLink->close();
  $_SESSION['result']=$resultBox;
  header("Location: ".$PHP_SELF); exit;
}

Function CreateTable() { global $_REQ, $resultBox, $PHP_SELF, $db, $dbLink;
  $sql = "CREATE TABLE contacts (`ID` BIGINT NOT NULL AUTO_INCREMENT,
    `FirstName` VARCHAR(50), `LastName` VARCHAR(50),
    `Phone` VARCHAR(30), `Notes` VARCHAR(100), `DateAdded` DATETIME,
    PRIMARY KEY (`ID`) );";
  if (mysqli_query($dbLink, $sql)) {
    $resultBox=ResultBox("Table created successfully");
  } else {
    $resultBox=ResultBox("Error creating table: " . mysqli_error($dbLink));
  }
  $_SESSION['result']=$resultBox;
  header("Location: ".$PHP_SELF); exit;
}

Function DropTable() { global $_REQ, $resultBox, $PHP_SELF, $db, $dbLink;
  $sql = "DROP TABLE contacts;";
  if (mysqli_query($dbLink, $sql)) {
    $resultBox=ResultBox("Table dropped successfully");
  } else {
    $resultBox=ResultBox("Error dropping table: " . mysqli_error($dbLink));
  }
  $_SESSION['result']=$resultBox;
  header("Location: ".$PHP_SELF); exit;
}

Function CreateDummies() { global $_REQ, $resultBox, $PHP_SELF, $db, $dbLink;
  $sql = "INSERT INTO contacts SET FirstName='Jon', LastName='Doe', Phone='+1 123 456789', Notes='Dummy Contact', DateAdded=NOW(); ";
  $sql.= "INSERT INTO contacts SET FirstName='Kimi', LastName='Raikkonen', Phone='+598 99 123456', Notes='F1 Driver', DateAdded=NOW(); ";
  $sql.= "INSERT INTO contacts SET FirstName='Valentino', LastName='Rossi', Phone='+46 987 654321', Notes='MotoGP Driver', DateAdded=NOW(); ";
  if (mysqli_multi_query($dbLink, $sql)) {
    $resultBox=ResultBox("Contacts created successfully");
  } else {
    $resultBox=ResultBox("Error creating contacts: " . mysqli_error($dbLink));
  }
  $_SESSION['result']=$resultBox;
  header("Location: ".$PHP_SELF); exit;
}

Function ContactsEdit() { global $ID, $_REQ, $resultBox, $PHP_SELF, $db, $dbLink;
  if ($ID!="0") {
   $rs=mysqli_query($dbLink,"SELECT * FROM contacts WHERE ID='".$ID."';") or die(mysqli_error($dbLink));
   $row=mysqli_fetch_array($rs); mysqli_free_result($rs); $isNew=false;
  } else { $isNew=true; }
  bHeader(); ?>
  <div align="center">
  <form method="post" action="<?= $PHP_SELF ?>" name="frmContact" style="margin:0px;">
    <input type="hidden" name="s"  value="Save" />
    <input type="hidden" name="ID"  value="<?= $ID ?>" />
  <table width="98%" cellpadding="1" cellspacing="1" class="sTable2"><?php
  mkFormTitle('Contacts Details');
  mkFormField('First Name','txtFirstName',GetFormData($_REQ['txtFirstName'], $row['FirstName']),50);
  mkFormField('Last Name','txtLastName',GetFormData($_REQ['txtLastName'], $row['LastName']),50);
  mkFormField('Phone','txtPhone',GetFormData($_REQ['txtPhone'], $row['Phone']),30);
  mkFormField('Notes','txtNotes',GetFormData($_REQ['txtNotes'], $row['Notes']),100);
  mkFormBtns('Save Record',3,true); 
  ?></table></form></div><?php
  bFooter();
};

Function ContactsSave() { global $ID, $_REQ, $resultBox, $PHP_SELF, $db, $dbLink;
  $sql ="FirstName='".qFix($_REQ['txtFirstName'])."', ";
  $sql.="LastName='".qFix($_REQ['txtLastName'])."', ";
  $sql.="Phone='".qFix($_REQ['txtPhone'])."', ";
  $sql.="Notes='".qFix($_REQ['txtNotes'])."' ";

  if ($ID!="0") {
    $sql="UPDATE contacts SET ".$sql." WHERE ID='".$ID."' LIMIT 1;";
    if (mysqli_query($dbLink,$sql)) {
      $resultBox=ResultBox("Contact updated succesfully.");
    } else {
      $resultBox=ResultBox("Failed to update Contact:<br/>".@mysqli_error($dbLink));
    }
  } else {
    $sql="INSERT INTO contacts SET ".$sql.", DateAdded=NOW();";
    if (mysqli_query($dbLink,$sql)) {
      $resultBox=ResultBox("New contact saved succesfully.");
    } else {
      $resultBox=ResultBox("Failed to add Contact:<br/>".@mysqli_error($dbLink));
    }
  };
  $_SESSION['result']=$resultBox;
  header("Location: ".$PHP_SELF); exit;
};

Function ContactsDelete() { global $ID, $_REQ, $resultBox, $PHP_SELF, $db, $dbLink;
  $sql="DELETE FROM contacts WHERE ID='".$ID."' LIMIT 1;";
  if (mysqli_query($dbLink,$sql)) {
    $resultBox=ResultBox("Contact deleted succesfully.");
  } else {
    $resultBox=ResultBox("Failed to delete Contact:<br/>".@mysqli_error($dbLink));
  }  
  $_SESSION['result']=$resultBox;
  header("Location: ".$PHP_SELF); exit;
};