<form name="<!--<<form_name>>-->"  <!--<<form_enctype>>--> method=post action="<!--<<form_action>>-->">
			<table width=100% border=0 cellspacing=0 cellpadding=8>
					<tr>
					<td class=title align=center colspan='2'><!--<<title>>--></td>
					</tr>
					<tmpl:if_error>
					<tr>
					<td colspan='2'>
						<small style="color:#f00" ><!--<<error>>-->
						<ul>
						<tmpl:error_items>
							<li><!--<<error_item>>--></li>
						</tmpl:error_items>
						</ul></small>
					</td>
					</tr>
					</tmpl:if_error>
					<tmpl:if_success>
					<tr>
					<td colspan='2'>
						<small style="color:green" ><!--<<success>>--></small>
					</td>
					</tr>
					</tmpl:if_success>
					<!--<<form_elements>>-->
			</table>
</form>