	var active_chat_users = null;
	var update_chat_users = null;
	var chat_actions = new Array();

	chat_actions['init'] 								= {'status': 0, 'start_time': 0, 'finish_time': 0, 'max_time': 10000, 'onfail': window.location.reload};
	chat_actions['get_messages'] 				= {'status': 0, 'start_time': 0, 'finish_time': 0, 'max_time': 10000, 'refresh_time': 5000, 'onstart': get_messages, 'onfail': get_messages_failed};
	chat_actions['get_users'] 					= {'status': 0, 'start_time': 0, 'finish_time': 0, 'max_time': 10000, 'onstart': get_users, 'onfail': get_users_failed};
	chat_actions['send_message'] 				= {'status': 0, 'start_time': 0, 'finish_time': 0, 'max_time': 10000, 'onstart': send_message, 'onfail': send_message_failed};
	chat_actions['toggle_ignore_user'] 	= {'status': 0, 'start_time': 0, 'finish_time': 0, 'max_time': 10000, 'onfail': toggle_ignore_user_failed};
	chat_actions['exit'] 								= {'status': 0, 'start_time': 0, 'finish_time': 0, 'max_time': 10000, 'onstart': exit, 'onfail': exit};
	
	function start_action(action)
	{
//		if (get_action_status(action) == 1)
//			return;
		
		current_time = new Date()
		chat_actions[action]['status'] = 1;
		chat_actions[action]['start_time'] = current_time.getTime();
		
		if (typeof(chat_actions[action]['onstart']) == 'function')
			chat_actions[action]['onstart']();
	}
	
	function finish_action(action)
	{
		current_time = new Date()
		chat_actions[action]['status'] = 0;
		chat_actions[action]['finish_time'] = current_time.getTime();

		if (typeof(chat_actions[action]['onfinish']) == 'function')
			chat_actions[action]['onfinish']();
	}
	
	function get_action_status(action)
	{
		return chat_actions[action]['status'];
	}

	start_action('init');
	window.onload = window_loaded;
	
	window.setInterval(check_chat_actions, 1000);
	
	function check_chat_actions()
	{
		current_time = new Date()
		current_time = current_time.getTime();
		
		for(action in chat_actions)
		{
			if (chat_actions[action]['status'] == 1 
					&& (current_time - chat_actions[action]['start_time']) > chat_actions[action]['max_time'])
				if (typeof(chat_actions[action]['onfail']) == 'function')
					chat_actions[action]['onfail']();

			if (chat_actions[action]['status'] == 0 
					&& (current_time - chat_actions[action]['finish_time']) > chat_actions[action]['refresh_time'])
				if (typeof(chat_actions[action]['onstart']) == 'function')
					start_action(action);
		}
	}
	
	function window_loaded()
	{
		finish_action('init');
		
		start_action('get_messages');
		start_action('get_users');
	}

	function get_messages()
	{
		get_messages_frame.location = '/chat/get_messages.php';
	}
	
	function get_users()
	{
		get_users_frame.location = '/chat/get_users.php';
	}
	
	function get_messages_failed()
	{
		start_action('get_messages');
	}
	
	function get_users_failed()
	{
		start_action('get_users');
	}
	
	function send_message_failed()
	{
		finish_action('send_message');
	}
	
	function toggle_ignore_user_failed()
	{
		finish_action('toggle_ignore_user');
	}
	
	function exit()
	{
		exit_frame.location = '/chat/logout.php';
	}
	
	function emoticon(text) 
	{
		text = ' ' + text + ' ';

		msg = document.getElementById('_message');

		msg.value  += text;
		msg.focus();
		
//		count_chars();
	}	
	
	function add_message(time, id, name, message, span_class, image)
	{
		message_span = chat_messages_frame.document.createElement("SPAN");
		message_span.className = span_class;
		
		image_text = '';
		if (image)
		{
			image_text = "<a href='/chat/chat_file.php?id=" + image[0] + "' target='_blank'><img src='" + image[1] + "' border='0' align='center'></a>&nbsp;";
			image_text += image[2] + "&nbsp;bytes&nbsp;[" + image[3] + "x" + image[4] + "]<br>";
		}

		message_text = '[' + time + '] ' + name + ':<br>' ;
		message_text += image_text
		message_text += message + '<br>';
		
		message_span.innerHTML = message_text;
		
		chat_messages_frame.document.body.appendChild(message_span);
	}
	
	function add_system_message(time, id, message)
	{
		add_message(time, id, 'ChatRobot', message, 'system_message')
	}

	function add_warning_message(time, id, message)
	{
		add_message(time, id, 'ChatRobot', message, 'system_message')
	}

	function add_private_incoming_message(time, id, sender_name, message)
	{
		add_message(time, id, sender_name, message, 'private_message')
	}

	function add_private_outgoing_message(time, id, sender_name, message)
	{
		add_message(time, id, sender_name, message, 'private_message')
	}

	function add_common_message(time, id, sender_name, message, image)
	{
		add_message(time, id, sender_name, message, 'common_message', image)
	}
	
	function chat_user_event()
	{
		update_chat_users = 1;
	}
	
	function set_active_users(users)
	{
		finish_action('get_users');
		
		users_combobox = document.getElementById('chat_users_combobox');
		
		_remove_users_from_panel();
		_remove_users_from_combobox(users_combobox);
		
		for(i=0; i<users.length; i++)
		{
			_add_user_to_panel(users[i])
			_add_user_to_combobox(users_combobox, users[i])
		}
		
		active_chat_users = users;
	}
	
	function update_active_users_header(header)
	{
		h = document.getElementById('chat_users_panel_header');
		h.innerHTML = header;
	}
	
	function _remove_users_from_panel()
	{
		while(chat_users_panel_frame.document.body.hasChildNodes())
			chat_users_panel_frame.document.body.removeChild(chat_users_panel_frame.document.body.firstChild);
	}
	
	function _remove_users_from_combobox(users_combobox)
	{
		while(users_combobox.hasChildNodes())
			users_combobox.removeChild(users_combobox.firstChild);
		
		all_option = document.createElement('OPTION');
		all_option.value = -1;
		all_option.appendChild(document.createTextNode('To all'));
		
		users_combobox.appendChild(all_option);
	}
	
	function _add_user_to_panel(user)
	{
		user_span = chat_users_panel_frame.document.createElement("SPAN");
		
		if (user[2] & 1)
			gender = 'woman'
		else
			if (user[2] & 2)
				gender = 'man';
			else
				gender = 'ghost';
		
		if(user[3] > 0)
		{
			gender = gender + '_blocked';
			alt = 'unblock';
		}
		else
			alt = 'block'
		
		img = "<a href='javascript:top.toggle_ignore_user(\"" +user[0]+ "\")'><img src='/shared/images/user_" + gender + ".gif' align='center' border='0' alt='" + alt + "' title='"+ alt +"'></a>";

		user_span.id = 'chat_user_' + user[0];
		user_span.innerHTML = 
			img + 
			"&nbsp; <a href='javascript:top.emoticon(\"" + user[1] +":\")'>" + 
			user[1] + 
			"</a>&nbsp;<br>";
		chat_users_panel_frame.document.body.appendChild(user_span);
	}
	
	function _add_user_to_combobox(users_combobox, user)
	{
		user_option = document.createElement("OPTION");
		user_option.value = user[0];
		user_option.appendChild(document.createTextNode(user[1]));

		users_combobox.appendChild(user_option);
	}
	
	function send_message()
	{
		mform = document.getElementById('message_form');
		mform.message.value = mform._message.value;
		mform._message.value = '';
		mform.submit();
		file = document.getElementById('file_input');
		file_parent = file.parentNode;
		file_parent.removeChild(file);
		file_parent.innerHTML = "<input id='file_input' type='file' name='file' size=20>";
		
	//	count_chars();
	}
	
	function key_up()
	{ 
		if(event.ctrlKey && event.keyCode == 13)
		{
			send_message(document.forms.message_form);
			document.forms.message_form.submit();
		}
		//count_chars();
	}
	
	function count_chars()
	{
		chars_counter = document.getElementById('chars');
		
		if(chars_counter)
		 chars_counter.innerHTML = message_form.elements._message.value.length;
	}
	
	function fetch_finished(last_message_id)
	{
		finish_action('get_messages');
		
		chat_messages_frame.scroll(0, 1000000);
		
		if (update_chat_users)
		{
			start_action('get_users');
			update_chat_users = null;
		}
	}
	
	function send_message_finished()
	{
		finish_action('send_message');
		start_action('get_messages');
	}
	
	function toggle_ignore_user(ignorant_id)
	{
		toggle_ignore_user_frame.location = '/chat/get_users.php?ignorant_id=' + ignorant_id;
	}
