<?php

require_once("initvars.inc.php");
require_once("config.inc.php");
?>

<script type="text/javascript" src="bookmarkAds.js"></script>
<script>window.onload=setCheckedSelectedBookmarksTotal;</script>

<?php

$bookmarkCookies = trim($_COOKIE["bookmark"], ".");
$bookmarkAds = explode(".", $bookmarkCookies);
$bookmarkAdsCount = count($bookmarkAds);
$bookmarksSql = "";
for($i=0; $i<=$bookmarkAdsCount; $i++){
	if(strlen($bookmarkAds[$i])>0){
		$bookmarksSql .= "'" . $bookmarkAds[$i] . "',";
	}
}
$bookmarksSql = trim($bookmarksSql, ",");

if($bookmarksSql != ""){
	$ads_condn = "AND a.adid in ($bookmarksSql)";
	$sql = "SELECT a.*, ct.cityname, UNIX_TIMESTAMP(a.createdon) AS timestamp, feat.adid AS isfeat,
				COUNT(*) AS piccount, p.picfile AS picfile, scat.subcatname, scat.catid, cat.catname
			FROM $t_ads a
				INNER JOIN $t_cities ct ON a.cityid = ct.cityid
				INNER JOIN $t_subcats scat ON a.subcatid = scat.subcatid
				INNER JOIN $t_cats cat ON scat.catid = cat.catid
				LEFT OUTER JOIN $t_featured feat ON a.adid = feat.adid AND feat.adtype = 'A' AND feat.featuredtill >= NOW()
				LEFT OUTER JOIN $t_adpics p ON a.adid = p.adid AND p.isevent = '0'
			WHERE $visibility_condn
				$loc_condn
				$ads_condn
			GROUP BY a.adid
			ORDER BY a.createdon DESC";
		$res_bookmark_ads = mysql_query($sql) or die($sql.mysql_error());
}	

?>


<form action="bookmarkstotal.html" method="post" id="frmBookmarks" name="frmBookmarks">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="postlisting">
			<tr>
				<td align="center"><?=$lang['BOOKMARKS_TITLE']?></td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="10" bgcolor="#cccccc" class="postlisting">
			<tr>
				<td bgcolor="#f5f5f5" align="left" width="100%">
					<table border="0" cellspacing="0" cellpadding="5" width="100%" class="postlisting">
						<tr>
							<td align="left"><b><i><?=$lang['BOOKMARKS_TITLE_TTL']?></i></b></td>
							<td align="center"><b><i><?=$lang['BOOKMARKS_PRICE']?></i></b></td>
							<td align="center"><b><i><?=$lang['BOOKMARKS_DATE']?></i></b></td>
							<td align="center"><b><i><?=$lang['BOOKMARKS_HITS']?></i></b></td>
							<td align="right"><b><i><?=$lang['BOOKMARKS_SEL']?></i></b></td>
							<td align="center"><b><i><?=$lang['BOOKMARKS_PIC']?></i></b></td>
						</tr>
						
						<?php
						if($bookmarksSql != ""){
							$i=0;
							while($row = mysql_fetch_array($res_bookmark_ads)){
								$i++;
								$cssalt = ($i%2 ? "" : "alt");
								$url = buildURL("showad", array($xcityid, $row['catid'], $row['catname'], 
		    				$row['subcatid'], $row['subcatname'], $row['adid'], $row['adtitle']));
		    				
		    				if($row['picfile']) {
									$picfile = $row['picfile'];
									$imgsize = GetThumbnailSize("{$datadir[adpics]}/{$picfile}", $tinythumb_max_width, $tinythumb_max_height);
								}else{
									$picfile = "";
								}
						?>
								<tr  class="bookmarkcell<?=$cssalt;?>">
									<td align="left">
										<b><a href="<?php echo $url; ?>" <?php echo $feat_class; ?>><?php echo $row['adtitle']; ?></a></b> 
										<?php if(0&&$row['picfile']) { ?><img src="images/adwithpic.gif" align="absmiddle"><?php } ?>
										<?php echo $feat_img; ?><br>
										<span class="adcat">
										<?php echo "$row[catname] $path_sep $row[subcatname]"; ?>
										<?php 
										$loc = "";
										if($row['area']) $loc = $row['area'];
										if($xcityid < 0) $loc .= ($loc ? ", " : "") . $row['cityname'];
										if($loc) echo "<br>$loc";
										?>			
										</span>
										</td>
										
										<td align="center">
											<?php echo $row['price'];?>
										</td>
										<td align="center">
											<?php echo $langx['months_short'][date("n", $row[timestamp])-1] . "," . date("j", $row[timestamp]); ?>
										</td>
										<td align="center">
											<?php echo $row['hits'];?>
										</td>
										
										<td width="90" align="right">
										<table >
											<tr>
												<td style="border-top: 0px solid #FFFFFF;">
													<span id = "bookmarkadspan<?php echo $row['adid'];?>" class="bookmarktext"></span>
												</td>
												<td style="border-top: 0px solid #FFFFFF;">
													<input id = "bookmarkad<?php echo $row['adid'];?>" name="bookmarkad" type="submit" class="savead" value="" onmouseout="javascript:setHout(<?php echo $row['adid']?>);" onclick="javascript:writeCookie('bookmark',<?php echo $row['adid']?>, '.');" onmouseover="javascript:setHover(<?php echo $row['adid']?>);" >
												</td>
											</tr>
										</table>
										</td>
										
										<td  align="right" width="<?php echo $tinythumb_max_width; ?>">
										<?php if($picfile) { ?>
										<a href="<?php echo $url; ?>"><img src="<?php echo "{$datadir[adpics]}/{$picfile}"; ?>" border="0" width="<?php echo $imgsize[0]; ?>" height="<?php echo $imgsize[1]; ?>" style="border:1px solid black"></a>
									<?php } ?>
									</td>
									
								</tr>
						<?php
							}//end while
						}else{
							?>
								<tr>
									<td colspan = "6" align="center"><b><i><u><?=$lang['BOOKMARKS_NO_ADS']?></u></i></b></td>
								</tr>
						<?php
						}
						?>
						<!--tr>
							<td align="center"><strong><br><span style=\"color:#f00;\"><?php echo $msg; ?></span></strong></td>
						</tr-->
						
					</table>
				</td>
			</tr>	
		</table>
	</form>