enyo.kind({
	name: "Elang.Input",

	components: [{tag: 'p', name: 'content'}],

	published: {sequence: null, url: null, timeout: null},

	events: {onCueChanged: ''},

	sequenceChanged: function (oldValue)
	{
		this.$.content.destroyClientControls();
		for (var i in this.sequence.elements)
		{
			var element = this.sequence.elements[i];
			switch (element.type)
			{
				case 'text':
					this.$.content.createComponents(
						[
							{tag: 'span', 'content': this.sequence.elements[i].content},
							{tag: 'span', 'content': ' '},
						],
						{owner: this}
					);
 					break;
				case 'success':
					this.$.content.createComponents(
						[
							{tag: 'span', classes: 'alert-success', 'content': this.sequence.elements[i].content},
							{tag: 'span', 'content': ' '},
						],
						{owner: this}
					);
 					break;
				case 'help':
					this.$.content.createComponents(
						[
							{tag: 'span', classes: 'alert-info', 'content': this.sequence.elements[i].content},
							{tag: 'span', 'content': ' '},
						],
						{owner: this}
					);
 					break;
				case 'input':
					this.$.content.createComponents(
						[
							{
								tag: 'span',
								classes: 'input-append control-group',
								components: [
									{
										kind: 'Input',
										name: i,
										classes: element.size > 50 ? 'input-xxlarge' : element.size > 40 ? 'input-xlarge' : element.size > 30 ? 'input-large' : '',
										onchange: 'textChanged',
										attributes: {type: 'text'}
									},
									{
										tag: 'a',
										ontap: 'helpTapped',
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
		request.inputNumber = inSender.name;

		// Makes the Ajax call with parameters
		request.go({task: 'check', s: this.sequence.id, n: inSender.name});
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
				this.sequence.elements[inRequest.inputNumber] = {content: inResponse.text, type: 'success'};
				this.sequenceChanged();
				this.render();
				this.doCueChanged({number: this.sequence.number, text: inResponse.cue});
				break;
			case 'failure':
				this.sequence.elements[inRequest.inputNumber] = {content: inResponse.text, type: 'success'};
				this.sequenceChanged();
				this.render();
				this.doCueChanged({number: this.sequence.number, text: inResponse.cue});
				break;
		}
	}
});

