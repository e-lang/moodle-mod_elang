/**
 * Modal to alert the user
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: 'Elang.Modal',

	/**
	 * Published properties:
	 * - head: head of the modal
	 * - type: type of the modal ('success', 'info', 'warning', 'danger')
	 * - title: title of the modal
	 * - message: message to be displayed
	 * Each property will have public setter and a getter methods
	 */
	published: {head: '', type: '', title: '', message: ''},

	/**
	 * css classes
	 */
	classes: 'modal fade',

	/**
	 * Named components:
	 * - head: head of the alert
	 * - type: used to set the type of the alert
	 * - title: used to set the title of the alert
	 * - message: message displayed
	 */
	components: [
		{kind: enyo.Signals, onkeydown: 'keyDown'},
		{
			classes: 'modal-dialog',
			components: [
				{
					classes: 'modal-content',
					components: [
						{
							classes: 'modal-header',
							components: [
								{
									tag: 'button',
									type: 'button',
									classes: 'close',
									attributes: {'data-dismiss': 'modal', 'aria-hidden': 'true'},
									allowHtml: true,
									content: '&times;'
								},

								// head
								{name: 'head', tag: 'h3'},
							]
						},
						{
							classes: 'modal-body',
							components: [
								// type
								{
									name: 'type',
									classes: 'alert alert-block',
									components: [
										// title
										{name: 'title', tag: 'h4'},

										// message
										{name: 'message'}
									],
								}
							]
						},
						{
							classes: 'modal-footer',
							components: [
								{
									tag: 'button',
									classes: 'btn',
									attributes: {'data-dismiss': 'modal', 'aria-hidden': 'true'},
									content: $L('Close')
								}
							]
						},
					]
				}
			]
		}
	],

	/**
	 * Set the data for the alert
	 *
	 * @public
	 *
	 * @param   head     string  The head content
	 * @param   type     string  The type content
	 * @param   title    string  The title content
	 * @param   message  string  The message content
	 *
	 * @return  this
	 *
	 * @since  0.0.1
	 */
	setData: function (head, type, title, message)
	{
		this.setHead(head);
		this.setType(type);
		this.setTitle(title);
		this.setMessage(message);
		return this;
	},

	/**
	 * Toggle the state of the alert
	 *
	 * @public
	 *
	 * @return  this
	 *
	 * @since  0.0.1
	 */
	toggle: function()
	{
		$('#' + this.id).modal('toggle');
		return this;
	},

	/**
	 * Show the alert
	 *
	 * @public
	 *
	 * @return  this
	 *
	 * @since  0.0.1
	 */
	show: function ()
	{
		$('#' + this.id).modal('show');
		return this;
	},

	/**
	 * Hide the alert
	 *
	 * @public
	 *
	 * @return  this
	 *
	 * @since  0.0.1
	 */
	hide: function ()
	{
		$('#' + this.id).modal('hide');
		return this;
	},

	/**
	 * Detect a change in the head property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The head old value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	headChanged: function (oldValue)
	{
		this.$.head.content = this.head;
	},

	/**
	 * Detect a change in the type property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The type old value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	typeChanged: function (oldValue)
	{
		if (['success', 'info', 'warning', 'danger'].indexOf(this.type) < 0)
		{
			var type = this.type;
			this.type = oldValue;
			throw new RangeError('Type value "' + type + '" is incorrect');
		}
		this.$.type.addClass('alert-' + this.type);
		this.$.type.removeClass('alert-' + oldValue);
	},

	/**
	 * Detect a change in the title property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The title old value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	titleChanged: function (oldValue)
	{
		this.$.title.content = this.title;
	},

	/**
	 * Detect a change in the message property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The message old value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	messageChanged: function (oldValue)
	{
		this.$.message.content = this.message;
	},

	/**
	 * Handle keydown event on a modal
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
	keyDown: function(inSender, inEvent)
	{
		// Detect escape character
		if (inEvent.keyCode == 27)
		{
			this.hide();
		}

		// Prevents event propagation
		return true;
	},
});

