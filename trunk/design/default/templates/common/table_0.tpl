<table <!--<<table_width1>>--> border=0 cellspacing=0 cellpadding=0 table_id=<!--<<table_id>>-->>
<tr class=ddframe>
<td class=border2 style='padding:3px; background-color:#ffffff'>
	<table <!--<<table_width2>>--> border=0 cellpadding=0 cellspacing=0 class=border1>
	<tbody class=com4>
	<col class=com7>
		<tmpl:header>
			<tr class=com6> 
			<tmpl:cell>
			<th nowrap class=th>
				<!--<<cell_data>>-->
			</th>
			</tmpl:cell>
			</tr>
		</tmpl:header>
	<tr><td height=1 colspan=100 class=com5></td></tr>
	
        <tmpl:row>
        
             <tmpl:odd>
                 <tr class=oddrow row_id=<!--<<row_id>>--> ondrop="handle_drop(this)">
             </tmpl:odd>

             <tmpl:even>
                  <tr class=row row_id=<!--<<row_id>>--> ondrop="handle_drop(this)">
             </tmpl:even>

             <tmpl:cell>
                  <td class=col>
                  <!--<<cell_data>>-->
                  </td>
             </tmpl:cell>

             </tr>
      </tmpl:row>
					
	</tbody>
	</table>
</td>
</tr>
</table>