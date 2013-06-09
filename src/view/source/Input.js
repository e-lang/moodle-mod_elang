enyo.kind({
	name: "Elang.Input",

	components: [{tag: 'p', name: 'content'}],

	published: {cue: null, url: null, timeout: null},

	events: {onCueChanged: ''},

	cueChanged: function (oldValue)
	{
		this.$.content.destroyClientControls();
		if (this.cue != null)
		{
			for (var i in this.cue.elements)
			{
				var element = this.cue.elements[i];
				switch (element.type)
				{
					case 'text':
						this.$.content.createComponents(
							[
								{tag: 'span', 'content': this.cue.elements[i].content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
	 					break;
					case 'success':
						this.$.content.createComponents(
							[
								{tag: 'span', classes: 'alert-success', 'content': this.cue.elements[i].content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
	 					break;
					case 'help':
						this.$.content.createComponents(
							[
								{tag: 'span', classes: 'alert-info', 'content': element.content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
	 					break;
					case 'input':
					case 'failure':
						this.$.content.createComponents(
							[
								{
									tag: 'span',
									classes: 'input-append control-group' + (element.type == 'failure' ? ' error' : ''),
									components: [
										{
											kind: 'Input',
											name: i,
											classes: element.size > 50 ? 'input-xxlarge' : element.size > 40 ? 'input-xlarge' : element.size > 30 ? 'input-large' : '',
											onchange: 'textChanged',
											value: element.content,
											attributes: {type: 'text'}
										},
										{
											tag: 'a',
											ontap: 'helpTapped',
											number: i,
											classes: 'btn',
											attibutes: {href: '#'},
											components: [{tag: 'i', classes: 'icon-info-sign'}]
										}
									]
								},
								{tag: 'span', content: ' '},
							],
							{owner: this}
						);
					break;
				}
			}
		}
		this.render();
	},

	textChanged: function (inSender, inEvent)
	{
		// Request creation. The handleAs parameter is 'json' by default
		var request = new enyo.Ajax(
			{
				// Set the URL
				url: this.url,

				// Choose the method 'GET' or 'POST'
				method: 'POST',

				// Set the timeout
				timeout: this.timeout
			}
		);

		// Tells Ajax what the callback success function is
		request.response(enyo.bind(this, 'success'));

		// Tells Ajax what the callback failure function is
		request.error(enyo.bind(this, 'failure'));

		// Set the input number
		request.sender = inSender;

		// Makes the Ajax call with parameters
		request.go({task: 'check', id_cue: this.cue.id, number: inSender.name, text: inSender.value});
	},

	helpTapped: function (inSender, inEvent)
	{
		// Request creation. The handleAs parameter is 'json' by default
		var request = new enyo.Ajax(
			{
				// Set the URL
				url: this.url,

				// Choose the method 'GET' or 'POST'
				method: 'POST',

				// Set the timeout
				timeout: this.timeout
			}
		);

		// Tells Ajax what the callback success function is
		request.response(enyo.bind(this, 'help'));

		// Tells Ajax what the callback failure function is
		request.error(enyo.bind(this, 'failure'));

		// Set the input number
		request.sender = inSender;

		// Makes the Ajax call with parameters
		request.go({task: 'help', id_cue: this.cue.id, number: inSender.number});
	},

	/**
	 * Callback failure function
	 *
	 * @param   inRequest  enyo.Ajax      Request use for Ajax
	 * @param   inError    string|number  Error code
	 *
	 * @return  void
	 */
	failure: function (inRequest, inError)
	{
		alert(inError);
	},

	/**
	 * Callback success function
	 *
	 * @param   inRequest   enyo.Ajax  Request use for Ajax
	 * @param   inResponse  object     Response bject
	 *
	 * @return  void
	 */
	success: function (inRequest, inResponse)
	{
		switch (inResponse.status)
		{
			case 'success':
				this.cue.elements[inRequest.sender.name] = {content: inResponse.content, type: 'success'};
				this.cueChanged();
				this.render();
				this.doCueChanged({number: this.cue.number, text: inResponse.cue});
				break;
			case 'failure':
				if (inRequest.sender.value == '')
				{
					this.cue.elements[inRequest.sender.name] = {
						content: '', type: 'input',
						size: this.cue.elements[inRequest.sender.name].size
					};
				}
				else
				{
					this.cue.elements[inRequest.sender.name] = {
						content: inRequest.sender.value,
						type: 'failure',
						size: this.cue.elements[inRequest.sender.name].size
					};
				}
				this.cueChanged();
				this.render();
				this.doCueChanged({number: this.cue.number, text: inResponse.cue});
				break;
		}
	},

	/**
	 * Callback success function
	 *
	 * @param   inRequest   enyo.Ajax  Request use for Ajax
	 * @param   inResponse  object     Response bject
	 *
	 * @return  void
	 */
	help: function (inRequest, inResponse)
	{
		this.cue.elements[inRequest.sender.number] = {content: inResponse.content, type: 'help'};
		this.cueChanged();
		this.render();
		this.doCueChanged({number: this.cue.number, text: inResponse.cue});
	}
});

