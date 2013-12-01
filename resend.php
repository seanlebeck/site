    <?php

    require_once("initvars.inc.php");
    require_once("config.inc.php");
    require_once("captcha.cls.php");

    $html_mails = FALSE;

    ?>
    
   <br>

    <h2>Resend Activation, View and Edit Links</h2>

    
    
    
    <table cellspacing="5">
    <tr>
	<td>	
		
    <?php
    if($_POST['email'] && ValidateEmail($_POST['email']))
    {

       $urls = "";

       $sql = "SELECT * FROM $t_ads WHERE email = '$_POST[email]'";
       $res = mysql_query($sql);

       if(@mysql_num_rows($res))
       {
          while($row = mysql_fetch_array($res))
          {
         
					
				$adurl = $shortscript_url . "A" . $row[adid];					
            $editurl = "$script_url/?view=edit&cityid=$row[cityid]&adid=$row[adid]&codemd5=".md5($row['code']);
            $verificationlink = "$script_url/?view=activate&cityid=$row[cityid]&type=ad&adid=$row[adid]&codemd5=".md5($row['code']);

             if($html_mails)
             {
                $urls .= "$row[adtitle]<br /> ";
                if(!$row['verified']) $urls.="<a href=\"$verificationlink\">Activate</a> - ";
                $urls .= "<a href=\"$adurl\">View</a> - <a href=\"$editurl\">Edit</a><br /><br />";
             }
             else
             {
                $urls .= "$row[adtitle]\n";
                if(!$row['verified']) $urls.="Activate - $verificationlink\n";
                $urls .= "View - $adurl\nEdit - $editurl\n\n";
             }
          }

       }

       $sql = "SELECT * FROM $t_events WHERE email = '$_POST[email]'";
       $res = mysql_query($sql);

       if(@mysql_num_rows($res))
       {
          while($row = mysql_fetch_array($res))
          {
             $adurl = $shortscript_url . "E" . $row[adid];
             $editurl = "$script_url/?view=edit&cityid=$row[cityid]&adid=$row[adid]&isevent=1&codemd5=".md5($row['code']);
             $verificationlink = "$script_url/?view=activate&cityid=$row[cityid]&type=event&adid=$row[adid]&codemd5=".md5($row['code']);

             if($html_mails)
             {
                $urls .= "$row[adtitle]<br /> ";
                if(!$row['verified']) $urls.="<a href=\"$verificationlink\">Activate</a> - ";
                $urls .= "<a href=\"$adurl\">View</a> - <a href=\"$editurl\">Edit</a><br /><br />";
             }
             else
             {
                $urls .= "$row[adtitle]\n";
                if(!$row['verified']) $urls.="Activate - $verificationlink\n";
                $urls .= "View - $adurl\nEdit - $editurl\n\n";
             }
          }
       }

       if($urls)
       {
          $mail = file_get_contents("mailtemplates/resend.txt");
          $mail = str_replace("{@LINKS}", $urls, $mail);
          $mail = str_replace("{@SITENAME}", $site_name, $mail);

          if($html_mails) HTMLMail($_POST['email'], "Edit Links", $mail, $site_email);
          else xMail($_POST['email'], "Edit Links", $mail, $site_email);
       }

    ?>

    <font color="#008000">The links for your current ads have been sent to your email address. <br /><br /></font>
    <a href="index.php">Return Home</a>

    <?php

    }
    else
    {

    ?>
    <br />
    <form action="index.php?view=page&amp;pagename=resend&amp;cityid=<?php echo $xcityid; ?>" method="post">
    <div><p><strong>Enter your email address:</strong></p></div>
    <div><input type="text" name="email" size="35">&nbsp;&nbsp; <button type="submit">Resend Links</button></div>

    </form>
    <br />
    <?php

    }

    ?>

	 </td></tr>
	 </table>
   