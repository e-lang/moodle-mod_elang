/**
 * Cues kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: "Elang.Input",

	/**
	 * Published properties:
	 * - cue: the current cue
	 * - request: the request object
	 * Each property will have public setter and getter methods
	 */
	published: {cue: null, request: null},

	/**
	 * Events:
	 * - onTrackChange: fired when the text track associated with the cue change
	 * - onFail: fired when an ajax request failed
	 */
	events: {onTrackChange: '', onFail: ''},

	/**
	 * Named components:
	 * - content: input content
	 */
	components: [{tag: 'p', name: 'content'}],

	/**
	 * Detect a change in the cue property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The cue old value
	 *
	 * @since  0.0.1
	 */
	cueChanged: function (oldValue)
	{
		this.$.content.destroyClientControls();
		if (this.cue != null)
		{
			var elements = this.cue.getData().elements;
			for (var i in elements)
			{
				var element = elements[i];
				switch (element.type)
				{
					case 'text':
						this.$.content.createComponents(
							[
								{tag: 'span', 'content': element.content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
	 					break;
					case 'success':
						this.$.content.createComponents(
							[
								{tag: 'span', classes: 'alert-success', 'content': element.content},
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
											onchange: 'textChange',
											value: element.content,
											attributes: {type: 'text'}
										},
										{
											tag: 'a',
											ontap: 'helpTap',
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

	/**
	 * Handle text change event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		    Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.1
	 */
	textChange: function (inSender, inEvent)
	{
		// Tells Ajax what the callback success function is
		this.request.response(enyo.bind(this, 'success'));

		// Tells Ajax what the callback failure function is
		this.request.error(enyo.bind(this, 'failure'));

		// Set the input number
		this.request.sender = inSender;

		// Makes the Ajax call with parameters
		this.request.go({task: 'check', id_cue: this.cue.getData().id, number: inSender.name, text: inSender.value});

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle help tap event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		    Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.1
	 */
	helpTap: function (inSender, inEvent)
	{
		// Tells Ajax what the callback success function is
		this.request.response(enyo.bind(this, 'help'));

		// Tells Ajax what the callback failure function is
		this.request.error(enyo.bind(this, 'failure'));

		// Set the input number
		this.request.sender = inSender;

		// Makes the Ajax call with parameters
		this.request.go({task: 'help', id_cue: this.cue.getData().id, number: inSender.number});

		// Prevents event propagation
		return true;
	},

	/**
	 * Callback failure function
	 *
	 * @param   inRequest  enyo.Ajax      Request use for Ajax
	 * @param   inError    string|number  Error code
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	failure: function (inRequest, inError)
	{
		this.doFail({error: inError});
		inRequest.fail(inError);
	},

	/**
	 * Callback success function
	 *
	 * @param   inRequest   enyo.Ajax  Request use for Ajax
	 * @param   inResponse  object     Response bject
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	success: function (inRequest, inResponse)
	{
		var data = this.cue.getData();
		switch (inResponse.status)
		{
			case 'success':
				data.elements[inRequest.sender.name] = {content: inResponse.content, type: 'success'};
				this.cueChanged();
				this.render();
				this.doTrackChange({number: data.number, text: inResponse.cue});
				break;
			case 'failure':
				if (inRequest.sender.value == '')
				{
					data.elements[inRequest.sender.name] = {
						content: '', type: 'input',
						size: data.elements[inRequest.sender.name].size
					};
				}
				else
				{
					data.elements[inRequest.sender.name] = {
						content: inRequest.sender.value,
						type: 'failure',
						size: data.elements[inRequest.sender.name].size
					};
				}
				this.cueChanged();
				this.render();
				this.doTrackChange({number: data.number, text: inResponse.cue});
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
	 *
	 * @since  0.0.1
	 */
	help: function (inRequest, inResponse)
	{
		this.cue.getData().elements[inRequest.sender.number] = {content: inResponse.content, type: 'help'};
		this.cueChanged();
		this.render();
		this.doTrackChange({number: this.cue.getData().number, text: inResponse.cue});
	}
});
