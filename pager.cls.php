<?php





define("PAGER_DEF_PAGELIMIT", 5);


class pager
{
	var $urlformat;
	var $total;
	var $perpage;
	var $totalpages;
	var $curpage;
	
	var $linkcaptions = array();


	function pager($urlformat="", $total=0, $perpage=0, $curpage=0)
	{
		$this->urlformat = $urlformat;
		$this->total = $total;
		$this->perpage = $perpage;
		$this->curpage = $curpage;
		$this->totalpages = ceil($total/$perpage);		
		$this->initializeLinkCaptions();
	}


	function pagelink($page)
	{
		$link = str_replace("{@PAGE}", $page, $this->urlformat);
		$link = str_replace("{@PERPAGE}", $this->perpage, $link);
		return $link;
	}


	function nextpage()
	{
		if ($this->totalpages && $this->curpage != $this->totalpages)
		{
			return $this->curpage+1;
		}
	}
	
	
	function nextlink()
	{
		if ($this->curpage != $this->totalpages)
		{
			$link = str_replace("{@PAGE}", $this->curpage+1, $this->urlformat);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}


	function prevpage()
	{
		if ($this->curpage != 1)
		{
			return $this->curpage-1;
		}
	}
	
	
	function prevlink()
	{
		if ($this->curpage != 1)
		{
			$link = $this->urlformat;
			$link = str_replace("{@PAGE}", $this->curpage-1, $link);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}


	function firstlink()
	{
		if ($this->totalpages != 0)
		{
			$link = $this->urlformat;
			$link = str_replace("{@PAGE}", 1, $link);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}


	function lastlink()
	{
		if ($this->totalpages != 0)
		{
			$link = $this->urlformat;
			$link = str_replace("{@PAGE}", $this->totalpages, $link);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}
	
	
	
	function getlinks($limit = PAGER_DEF_PAGELIMIT, $firstandlast = TRUE)
	{
		if ($this->curpage <= $limit+1)
		{
			$start = 1;
			$ellipse1 = "";
			$extra = $limit - ($this->curpage-1);
		}
		else
		{
			$start = $this->curpage - $limit;
			$ellipse1 = "<td class=\"pagetable_ellipses\">...</td>";
			$extra = 0;
		}

		if ($this->totalpages-$this->curpage <= $limit+$extra)
		{
			$end = $this->totalpages;
			$ellipse2 = "";
			$extra = $limit + $extra - ($this->totalpages-$this->curpage);
		}
		else
		{
			$end = $this->curpage + ($limit+$extra);
			$ellipse2 = "<td class=\"pagetable_ellipses\">...</td>";
			$extra = 0;
		}

		if ($extra > 0)
		{
			if ($start>$extra)
			{
				$start = $start-$extra;
				$extra = 0;
			}
			else
			{
				$extra -= $start-1;
				$start = 1;
				$ellipse1 = "";
			}
		}
		
		/*if ($extra)
		{
			if ($end+$extra >= $this->totalpages)
			{
				$end = $this->totalpages;
				$ellipse2 = "";
				$extra -= ($this->totalpages-$end);
			}
			else
			{
				$end += $extra;
				$extra = 0;
			}
		}

		if ($extra)
		{
			if ($start>$extra)
			{
				$start = $start-$extra;
				$extra = 0;
			}
			else
			{
				$extra -= $start-1;
				$start = 1;
				$ellipse1 = "";
			}
		}
		*/

		$links = "<table class=\"pagetable\" border=\"0\" cellspacing=\"1\"><tr>\n";

		if ($this->totalpages && $firstandlast)
			$links .= "<td><a href=\"".$this->firstlink()."\" class=\"pagelink_first\">".$this->linkcaptions['PAGE_FIRST']."</a> </td>\n";
		
		if ($this->prevpage())
			$links .= "<td><a href=\"".$this->prevlink()."\" class=\"pagelink_prev\">".$this->linkcaptions['PAGE_PREV']."</a> </td>\n";
		
		$links .= $ellipse1;

		for ($i=$start; $i<$this->curpage; $i++)
			$links .= "<td><a href=\"".$this->pagelink($i)."\" class=\"pagelink\">&nbsp;".$i."&nbsp;</a> </td>\n";

		$links .= "<td class=\"pagetable_activecell\">&nbsp;".$this->curpage."&nbsp;</td>\n";

		for ($i=$this->curpage+1; $i<=$end; $i++)
			$links .= "<td><a href=\"".$this->pagelink($i)."\" class=\"pagelink\">&nbsp;".$i."&nbsp;</a> </td>\n";

		$links .= $ellipse2;

		if ($this->nextpage())
			$links .= "<td><a href=\"".$this->nextlink()."\" class=\"pagelink_next\">".$this->linkcaptions['PAGE_NEXT']."</a> </td>\n";

		if ($this->totalpages && $firstandlast)
			$links .= "<td><a href=\"".$this->lastlink()."\" class=\"pagelink_last\">".$this->linkcaptions['PAGE_LAST']."</a> </td>\n";

		$links .= "</tr></table>\n";

		return $links;

	}


	function outputlinks()
	{
		echo $this->getlinks();
	}
	
	function initializeLinkCaptions() {
		global $lang;
		$this->linkcaptions['PAGE_FIRST'] = $lang['PAGE_FIRST'] ? $lang['PAGE_FIRST'] : "&nbsp;&#8249;&#8249;&nbsp;";
		$this->linkcaptions['PAGE_PREV'] = $lang['PAGE_PREV'] ? $lang['PAGE_PREV'] : "&nbsp;&#8249;&nbsp;";
		$this->linkcaptions['PAGE_NEXT'] = $lang['PAGE_NEXT'] ? $lang['PAGE_NEXT'] : "&nbsp;&#8250;&nbsp;";
		$this->linkcaptions['PAGE_LAST'] = $lang['PAGE_LAST'] ? $lang['PAGE_LAST'] : "&nbsp;&#8250;&#8250;&nbsp;";
	}
}

?>