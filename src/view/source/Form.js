/**
 * Form kind
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
	published: {cue: null, request: null},

	/**
	 * Handlers:
	 - onTextChange: fired when a text is changed
	 - onHelpTap: fired when an help button is tapped
	 */
	handlers: {onTextChange: 'textChange', onHelpTap: 'helpTap'},

	/**
	 * Events:
	 * - onTrackChange: fired when the text track associated with the cue change
	 * - onFail: fired when an ajax request failed
	 */
	events: {onTrackChange: '', onFail: ''},

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
						this.createComponents(
							[
								{name: i, kind: 'Elang.Input', value: element.content, error: element.content != '', help: element.help, size: element.size, number: i},
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
				data.elements[inRequest.sender.name] = {content: inResponse.content, type: 'success'};
				this.cueChanged();
				this.render();
				break;
			case 'failure':
				data.elements[inRequest.sender.name].content = inRequest.sender.getValue();
				inRequest.sender.setError(inRequest.sender.getValue() != '');
				break;
		}
		this.doTrackChange({number: data.number, text: inResponse.cue});
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
		this.cue.getData().elements[inRequest.sender.number] = {content: inResponse.content, type: 'help'};
		this.cueChanged();
		this.render();
		this.doTrackChange({number: this.cue.getData().number, text: inResponse.cue});
	}
});
