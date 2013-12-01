<?php
function &getcategory($link){
$chunk = explode("/", $link);
switch ($chunk[5]) {

case 'acc':
    $category=546;
    break;
case 'act':
    $category=458;
    break;
case 'aos':
    $category=536;
    break;
case 'apa':
    $category=481;
    break;
case 'ara':
    $category=508;
    break;
case 'ata':
    $category=491;
    break;
case 'ats':
    $category=459;
    break;
case 'baa':
    $category=510;
    break;
case 'bar':
    $category=492;
    break;
case 'bfa':
    $category=496;
    break;
case 'bia':
    $category=493;
    break;
case 'biz':
    $category=542;
    break;
case 'bka':
    $category=495;
    break;
case 'boo':
    $category=494;
    break;
case 'bts':
    $category=526;
    break;
case 'bus':
    $category=551;
    break;
case 'cal':
    $category=464;
    break;
case 'cas':
    $category=478;
    break;
case 'cba':
    $category=516;
    break;
case 'cla':
    $category=515;
    break;
case 'cls':
    $category=471;
    break;
case 'com':
    $category=461;
    break;
case 'cpg':
    $category=583;
    break;
case 'cps':
    $category=528;
    break;
case 'crg':
    $category=584;
    break;
case 'crs':
    $category=552;
    break;
case 'cta':
    $category=512;
    break;
case 'cwg':
    $category=579;
    break;
case 'cys':
    $category=529;
    break;
case 'dmg':
    $category=585;
    break;
case 'edu':
    $category=553;
    break;
case 'egr':
    $category=548;
    break;
	
case 'ela':
    $category=517;
    break;
		
case 'ema':
    $category=513;
    break;
		
case 'eng':
    $category=558;
    break;
		
case 'etc':
    $category=577;
    break;
		
case 'evg':
    $category=580;
    break;
		
case 'evs':
    $category=530;
    break;
		
case 'fbh':
    $category=554;
    break;
		
case 'fgs':
    $category=537;
    break;
		
case 'fns':
    $category=531;
    break;
		
case 'foa':
    $category=500;
    break;
		
case 'fua':
    $category=499;
    break;
		
case 'gms':
    $category=519;
    break;
		
case 'gov':
    $category=556;
    break;
		
case 'gra':
    $category=518;
    break;
		
case 'grp':
    $category=462;
    break;
		
case 'haa':
    $category=511;
    break;
		
case 'has':
    $category=520;
    break;
		
case 'hea':
    $category=562;
    break;
		
case 'hss':
    $category=538;
    break;
		
case 'hsw':
    $category=484;
    break;
		
case 'hum':
    $category=557;
    break;
		
case 'jwa':
    $category=501;
    break;
		
case 'kid':
    $category=460;
    break;
		
case 'lab':
    $category=555;
    break;
		
case 'laf':
    $category=465;
    break;
		
case 'lbg':
    $category=581;
    break;
		
case 'lbs':
    $category=539;
    break;
		
case 'lgl':
    $category=559;
    break;
		
case 'lgs':
    $category=532;
    break;
		
case 'lss':
    $category=533;
    break;
		
case 'm4m':
    $category=476;
    break;
		
case 'm4w':
    $category=475;
    break;
		
case 'maa':
    $category=502;
    break;
		
case 'mar':
    $category=561;
    break;
		
case 'mas':
    $category=534;
    break;
		
case 'mca':
    $category=521;
    break;
		
case 'med':
    $category=549;
    break;
		
case 'mis':
    $category=479;
    break;
		
case 'mnu':
    $category=560;
    break;
		
case 'moa':
    $category=514;
    break;
		
case 'msa':
    $category=522;
    break;
		
case 'msr':
    $category=477;
    break;
		
case 'muc':
    $category=466;
    break;
		
case 'npo':
    $category=563;
    break;
		
case 'ofc':
    $category=547;
    break;
		
case 'off':
    $category=488;
    break;
		
case 'pas':
    $category=535;
    break;
		
case 'pet':
    $category=463;
    break;
		
case 'pha':
    $category=523;
    break;
		
case 'pol':
    $category=468;
    break;
		
case 'ppa':
    $category=490;
    break;
		
case 'prk':
    $category=487;
    break;
		
case 'pta':
    $category=509;
    break;
		
case 'rea':
    $category=489;
    break;
		
case 'rej':
    $category=564;
    break;
		
case 'res':
    $category=587;
    break;
		
case 'ret':
    $category=565;
    break;
		
case 'rid':
    $category=469;
    break;
		
case 'rnr':
    $category=480;
    break;
		
case 'roo':
    $category=482;
    break;
		
case 'rts':
    $category=541;
    break;
		
case 'rva':
    $category=503;
    break;
		
case 'sad':
    $category=571;
    break;
		
case 'sci':
    $category=550;
    break;
		
case 'sec':
    $category=568;
    break;
		
case 'sga':
    $category=504;
    break;
		
case 'sks':
    $category=540;
    break;
		
case 'sls':
    $category=566;
    break;
		
case 'sof':
    $category=570;
    break;
		
case 'spa':
    $category=567;
    break;
		
case 'stp':
    $category=472;
    break;
		
case 'sub':
    $category=483;
    break;
		
case 'swp':
    $category=485;
    break;
		
case 'sya':
    $category=497;
    break;
		
case 'taa':
    $category=524;
    break;
		
case 'tch':
    $category=572;
    break;
		
case 'tfr':
    $category=574;
    break;
		
case 'thp':
    $category=543;
    break;
		
case 'tia':
    $category=505;
    break;
		
case 'tla':
    $category=506;
    break;
		
case 'tlg':
    $category=582;
    break;
		
case 'trd':
    $category=569;
    break;
		
case 'trp':
    $category=573;
    break;
		
case 'trv':
    $category=544;
    break;
		
case 'vac':
    $category=486;
    break;
		
case 'vga':
    $category=525;
    break;
		
case 'vnn':
    $category=467;
    break;
		
case 'vol':
    $category=470;
    break;
		
case 'w4m':
    $category=474;
    break;
		
case 'w4w':
    $category=473;
    break;
		
case 'wan':
    $category=507;
    break;
		
case 'web':
    $category=575;
    break;
		
case 'wet':
    $category=545;
    break;
		
case 'wrg':
    $category=586;
    break;
		
case 'wri':
    $category=576;
    break;
		
case 'zip':
    $category=498;
    break;
	
	default:
		$category=512;
		break;
}
	return $category;
}

?>
