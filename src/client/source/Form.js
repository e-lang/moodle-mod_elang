/**
 * Form kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: "Elang.Form",

	/**
	 * Tag of the kind
	 */
	tag: 'p',

	/**
	 * css classes
	 */
	classes: 'form-inline',

	/**
	 * Published properties:
	 * - cue: the current cue
	 * - request: the request object
	 * Each property will have public setter and getter methods
	 */
	published: {cue: null, request: null, focus: null},

	/**
	 * Handlers:
	 - onTextChange: fired when a text is changed
	 - onHelpTap: fired when an help button is tapped
	 */
	handlers: {onTextChange: 'textChange', onHelpTap: 'helpTap'},

	/**
	 * Events:
	 * - onTrackChange: fired when the text track associated with the cue change
	 * - onHelpIncrement: fired when the help count is incremented
	 * - onSuccessIncrement: fired when the success count is incremented
	 * - onErrorIncrement: fired when the error count is incremented
	 * - onErrorDecrement: fired when the error count is decremented
	 * - onFail: fired when an ajax request failed
	 */
	events: {onTrackChange: '', onHelpIncrement: '', onSuccessIncrement: '', onErrorIncrement: '', onErrorDecrement: '', onFail: ''},

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
		this.destroyClientControls();
		if (this.cue != null)
		{
			var elements = this.cue.getData().elements;
			for (var i in elements)
			{
				var element = elements[i];
				switch (element.type)
				{
					case 'text':
						this.createComponents(
							[
								{tag: 'span', 'content': element.content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
						break;
					case 'success':
						this.createComponents(
							[
								{tag: 'span', classes: 'alert-success', 'content': element.content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
						break;
					case 'help':
						this.createComponents(
							[
								{tag: 'span', classes: 'alert-info', 'content': element.content},
								{tag: 'span', 'content': ' '},
							],
							{owner: this}
						);
						break;
					case 'input':
						var data = {
							name: i,
							kind: 'Elang.Input',
							value: element.content,
							error: element.content != '',
							help: element.help,
							size: element.size,
							number: i
						};

						if ('link' in element)
						{
							data.link = element.link;
						}

						this.createComponents([data, {tag: 'span', content: ' '}], {owner: this});
						
						break;
				}
			}
		}

		if (this.focus == null)
		{
			this.firstFocus();
		}
		else
		{
			this.$[this.focus].$.input.set('defaultFocus', true);
		}

		this.render();
	},

	/**
	 * Set the focus to the first input field available
	 *
	 * @return  void
	 *
	 * @since 1.0.0
	 */
	firstFocus: function ()
	{
		for (var component in this.$)
		{
			if (typeof this.$[component].$.input !== 'undefined')
			{
				this.$[component].$.input.set('defaultFocus', true);
				break;
			}
		}
	},

	/**
	 * Set the focus to the next input field available
	 *
	 * @param   current  string  Current focus
	 *
	 * @return  void
	 *
	 * @since 1.0.0
	 */
	nextFocus: function (current)
	{
		found = false;

		for (var component in this.$)
		{
			if (component == current)
			{
				found = true;
			}
			else if (found && typeof this.$[component].$.input !== 'undefined')
			{
				this.focus = component;
				return;
			}
		}

		if (found)
		{
			for (var component in this.$)
			{
				if (typeof this.$[component].$.input !== 'undefined')
				{
					this.focus = component;
					break;
				}
			}
		}

		this.focus = null;
	},

	/**
	 * Handle text change event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.1
	 */
	textChange: function(inSender, inEvent)
	{
		// Tells Ajax what the callback success function is
		this.request.response(enyo.bind(this, 'success'));

		// Tells Ajax what the callback failure function is
		this.request.error(enyo.bind(this, 'failure'));

		// Set the input number
		this.request.sender = inSender;

		// Makes the Ajax call with parameters
		this.request.go({task: 'check', id_cue: this.cue.getData().id, number: inSender.getNumber(), text: inSender.getValue()});

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
				if (data.elements[inRequest.sender.name].content != '')
				{
					this.doErrorDecrement();
					data.error--;
				}
				else
				{
					data.remaining--;
				}

				data.elements[inRequest.sender.name] = {content: inResponse.content, type: 'success'};
				data.success++;
				this.doSuccessIncrement();
				this.nextFocus(inRequest.sender.name);
				this.render();
				break;
			case 'failure':
				if (inRequest.sender.getValue() == '')
				{
					inRequest.sender.setError(false);

					data.error--;
					data.remaining++;
					this.doErrorDecrement();
				}
				else
				{
					inRequest.sender.setError(true);

					if (data.elements[inRequest.sender.name].content == '')
					{
						data.remaining--;
						data.error++;
						this.doErrorIncrement();
					}
				}

				data.elements[inRequest.sender.name].content = inRequest.sender.getValue();
				this.setFocus(inRequest.sender.name);
				break;
		}

		this.cue.setSuccess(data.success).setError(data.error).setRemaining(data.remaining).render();
		this.cueChanged();

		this.doTrackChange({number: data.number, text: inResponse.cue});
	},	

	/**
	 * Handle help tap event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
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
		this.request.go({task: 'help', id_cue: this.cue.getData().id, number: inSender.getNumber()});

		// Prevents event propagation
		return true;
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
		var data = this.cue.getData();

		if (data.elements[inRequest.sender.name].content == '')
		{
			data.remaining--;
		}
		else
		{
			data.error--;
		}

		data.elements[inRequest.sender.number] = {content: inResponse.content, type: 'help'};
		data.help++;
		this.cue.setHelp(data.help).setError(data.error).setRemaining(data.remaining).render();
		this.nextFocus(inRequest.sender.name);
		this.cueChanged();
		this.render();
		this.doTrackChange({number: this.cue.getData().number, text: inResponse.cue});
		this.doHelpIncrement();
		if (inRequest.sender.getValue() != '')
		{
			this.doErrorDecrement();
		}
	}
});
