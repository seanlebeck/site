<link rel="stylesheet" type="text/css" href="<?php echo $path_escape; ?>editor/wmd/wmd.css">
<div id="wmd-button-bar"></div>
<textarea id="wmd-input" name="<?php echo $wmd_editor['name']; ?>"><?php echo $wmd_editor['content']; ?></textarea><br>
<br><b><?php echo $lang['PREVIEW']; ?></b>
<div id="wmd-preview"></div>
<script type="text/javascript">
    wmd_options = {
        output: "Markdown", 
		buttons: "bold italic | link blockquote code | ol ul heading hr"
	};</script>
<script type="text/javascript" src="<?php echo $path_escape; ?>editor/wmd/showdown.js"></script>
<script type="text/javascript" src="<?php echo $path_escape; ?>editor/wmd/wmd.js"></script>
<script type="text/javascript">
    // IE(6) editor width fix
    if (Attacklab.Global.isIE) {
        document.getElementById('wmd-input').style.width = '98%';
    }
</script>