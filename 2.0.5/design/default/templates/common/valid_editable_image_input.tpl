<tmpl:script>
<script>
	function change_action(sel, variation)
	{
		obj = document.getElementById(variation + '_generate_div');
		if (obj)
		  obj.style.display = 'none';
			
		obj = document.getElementById(variation + '_upload_div');
		if (obj)
		  obj.style.display = 'none';
			
		obj = document.getElementById(variation + '_nothing_div');
		if (obj)
		  obj.style.display = 'none';
			
		obj = document.getElementById(variation + '_' + sel.value + '_div');
		if (obj)
		  obj.style.display = 'block';
	}
</script>
</tmpl:script>
<tr>
<td>
	<!--<<input_title>>-->
</td>
<td align='left' nowrap width=300>
<tmpl:error_descr>
	<span style="color:#ff0000" ><!--<<error>>--></span><br>
</tmpl:error_descr>
<table>
<tr>
	<td class='text'>
		<!--<<variations_explanation>>--> <!--<<supported_types>>-->
	</td>
</tr>
<tmpl:variation>
<tr>
	<td>
		<fieldset>
			<legend style='color:#000000'>
  				<select name='<!--<<select_name>>-->' onchange="change_action(this, '<!--<<variation>>-->')" class='input'>
  					<tmpl:action>
  					<option value='<!--<<action>>-->' <tmpl:if_selected>selected='1'</tmpl:if_selected>><!--<<action_title>>-->
  					</tmpl:action>
  				</select>
			  <!--<<variation_title>>-->
			 </legend>
			<table>
			<tr>
				<td class='text'>
				  <tmpl:variation_data>
				    <a href='<!--<<variation_href>>-->' target='_blank'>Click here to view <!--<<variation>>--></a><br>
				    <!--<<width_title>>-->:<!--<<variation_width>>--> <!--<<height_title>>-->:<!--<<variation_height>>--> <!--<<size_title>>-->:<!--<<variation_size>>--><!--<<kb>>-->
				  </tmpl:variation_data>
				  <tmpl:action_div>
					<div id='<!--<<variation_div_id>>-->' class='text' <tmpl:if_hidden>style='display:none'</tmpl:if_hidden>>
					  <tmpl:generate_action>
  						<!--<<generate_from>>--> <select name='<!--<<base_variation_name>>-->' class='input'><!--<<options>>--></select><br>
    				  <nobr><!--<<resize_max_dimension_to>>--> <input type=text name='<!--<<max_size_name>>-->' class=input size=3 value='<!--<<max_size_value>>-->'> <!--<<pixels>>--></nobr>
					  </tmpl:generate_action>
					  <tmpl:upload_action>
  						<input type=file name='<!--<<variation_name>>-->' class=input size=25><br>
    				  <nobr><!--<<resize_max_dimension_to>>--> <input type=text name='<!--<<max_size_name>>-->' class=input size=3 value='<!--<<max_size_value>>-->'> <!--<<pixels>>--></nobr>
					  </tmpl:upload_action>
					  <tmpl:nothing_action>
					  </tmpl:nothing_action>
					</div>
				  </tmpl:action_div>
				</td>
			</tr>
			</table>
		</fieldset>
	</td>
</tr>
</tmpl:variation>
</table>
</td>
</tr>
