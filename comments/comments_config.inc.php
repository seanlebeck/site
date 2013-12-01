<?php

####################################################################################

    /* -- Some Settings, edit as you wish -- */

    //how to format dates
    $DATEFORMAT = '%a %d %b %G %H:%M'; //see http://at2.php.net/manual/en/function.strftime.php for other possibilities

    //How many comments are shown before SHOW MORE link is displayed
    $CCOUNT     = 5;

    //Name Input Field Visible?
    $SHOWNAME   = TRUE;

    //eMail Input Field Visible?
    $SHOWMAIL   = FALSE;
   

    //enable tags (list tags you wish to enable eg 'IMG,A,B,SPAN')?
    $ENABLETAGS = 'B,I,A';
    
    
    
    //comment moderator email 
    $MODMAIL    = "email@website.com";
    
    //moderate comments? (will also send them via email)
	// We strongly reccommend setting this to TRUE in order to prevent abuse and spam
	//If TRUE, you will receive an email with a choise to approve or delete the comment
    $MODCOM     = false;
    
    //email all new comments to the email address above?
    $MAILCOM    = false;

    //the address from which new comments are sent from
    $MAILFROM   = "email@website.com";





    /* -- Language Settings -- */
    $lang['view']           = 'View all';
    $lang['view2']          = 'comments';
    $lang['name']           = 'Name';
    $lang['enterName']      = 'Enter your name';
    $lang['mail']           = 'eMail';
    $lang['enterMail']      = 'Enter you email address';
    $lang['enterComment']   = 'Add a Comment';
    $lang['comment']        = 'Comment';
    $lang['hide']           = 'Hide all';
    
    $lang['ilike']          = 'I like this comment';
    $lang['youlikethis']    = 'You like this';
    $lang['youandone']      = 'You and 1 other person like this';
    $lang['youandxx']       = 'You and XXX other people like this';
    $lang['onelikes']       = '1 person likes this';
    $lang['xxlikethis']     = 'XXX people like this';
    
    $lang['cid']            = 'The comment ID';
    $lang['wassuc']         = 'was successfully';
    $lang['deleted']        = 'deleted';
    $lang['moderated']      = 'moderated';


$db_name = "dojoep_messaging_db";
$db_pass =  "5s[NqPUt[AR9";
$db_user = "dojoep_dojoep";
####################################################################################################
    /* ----- DO NOT EDIT BELOW THIS LINE ----- */
    //open the actual DB connection
    try{
        $db = new PDO('mysql:host='.$db_host.';dbname='.$db_name,$db_user,$db_pass,array());
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $db->exec("SET NAMES 'utf8'");
    }catch (exception $e){
        header('Content-type: application/x-json');
        echo json_encode(array('dberror' => $e->getMessage()));
        exit;
    }

    