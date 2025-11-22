<?php

print ' </td>
	</tr>
</table>
<table border="0" cellpadding="3" cellspacing="1" width="100%">
	<tr>
		<td align="center" class="footer" height="30" valign="middle">'.$retooFetis.'</td>
	</tr>
</table>
</body>
</html>

<script type="text/javascript">
//Call dropdowncontent.init("anchorID", "positionString", glideduration, "revealBehavior") at the end of the page:
	dropdowncontent.init("searchlink1", "right-top", 500, "mouseover")
	dropdowncontent.init("searchlink2", "right-top", 500, "mouseover")
</script>';
ob_end_flush();
?>
