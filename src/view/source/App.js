/**
 * Application kind
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
	name: 'Elang.App',

	/**
	 * Published properties:
	 * - url: server url
	 * - timeout: server timeout in milliseconds
	 * - callbackName: the callback name for a jsonp request
	 * Each property will have public setter and a getter methods
	 */
	published: {url: null, timeout: null, callbackName: null},

	/**
	 * Handlers:
	 - onCueSelect: fired when a cue is selected
	 - onPageChange: fired when a new page is selected
	 - onCueDeselect: fired when a cue is deselected
	 - onTrackChange: fired when a text track is changed
	 - onHelpIncrement: fired when an help increment is fired
	 - onSuccessIncrement: fired when a success increment is fired,
	 - onErrorIncrement: fired when an error increment is fired,
	 - onErrorDecrement: fired when an error decrement is fired,
	 - ontimeupdate: fired when the video time changed
	 - onFail: fired when an ajax error occurred
	 */
	handlers: {
		onCueSelect: 'cueSelect',
		onPageChange: 'cueDeselect',
		onCueDeselect: 'cueDeselect',
		onTrackChange: 'trackChange',
		onHelpIncrement: 'helpIncrement',
		onSuccessIncrement: 'successIncrement',
		onErrorIncrement: 'errorIncrement',
		onErrorDecrement: 'errorDecrement',
		ontimeupdate: 'timeUpdate',
		onFail: 'fail'
	},

	/**
	 * Events:
	 * - onFail: fired when an ajax request failed
	 */
	events: {onFail: ''},

	/**
	 * css classes
	 */
	classes: 'container-fluid',

	/**
	 * css styles
	 */
	style: 'overflow: auto;',

	/**
	 * Named components:
	 * - head (Elang.Head): The head of the application containing the title and a pdf button
	 * - indicator (Elang.Progressbar): Student's progress
	 * - video (Elang.Video): Video exercise
	 * - progressbar (Elang.Progressbar): Video progress bar
	 * - cues (Elang.Cues): Cues list
	 */
	components: [
		// Title and indicator
		{
			classes: 'row-fluid',
			components: [
				{
					classes: 'span12',
					components: [
						// title
						{kind: 'Elang.Head', name: 'head'},

						// indicator
						{kind: 'Elang.Progressbar', name: 'indicator'}
					]
				}
			]
		},

		// Video, progressbar, input and cues list
		{
			classes: 'row-fluid',
			components: [
				{
					classes: 'span6',
					components: [
						// video
						{kind: 'Elang.Video', name: 'video'},

						// progressbar
						{kind: 'Elang.Progressbar', name: 'progressbar'},

						// input
						{kind: 'Elang.Form', name: 'form'}
					]
				},
				{
					classes: 'span6',
					components: [
						// cues
						{kind: 'Elang.Cues', name: 'cues'}
					]
				}
			]
		},

		// Modal to alert when the ajax request failed
		{kind: 'Elang.Modal', name: 'modal'}
	],

	/**
	 * The request object
	 *
	 * @protected
	 *
	 * @since  0.0.3
	 */
	request: null,

	/**
	 * Create method
	 *
	 * @protected
	 *
	 * @since  0.0.3
	 */
	create: function ()
	{
		this.inherited(arguments);
		this.callbackNameChanged();
	},

	/**
	 * Detect a change in the callbackName property
	 *
	 * @protected
	 *
	 * @param   oldValue  string|null  The callbackName old value
	 *
	 * @since  0.0.3
	 */
	callbackNameChanged: function (oldValue)
	{
		if (this.callbackName == null)
		{
			// Request creation. The handleAs parameter is 'json' by default
			this.request = new enyo.Ajax(
				{
					// Set the URL
					url: this.url,

					// Choose the method 'GET' or 'POST'
					method: 'POST',

					// Set the timeout
					timeout: this.timeout,
				}
			);
		}
		else if (oldValue == null)
		{
			this.request = new enyo.JsonpRequest(
				{
					// Set the URL
					url: this.url,

					// Choose the method 'GET' or 'POST'
					method: 'POST',

					// Set the timeout
					timeout: this.timeout,

					// Set the jsonp callback
					callbackName: this.callbackName
				}
			);
		}
		else
		{
			this.request.setCallbackName(this.callbackName);
		}
		this.$.form.setRequest(this.request);
	},


	/**
	 * Detect a change in the url property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The url old value
	 *
	 * @since  0.0.3
	 */
	urlChanged: function (oldValue)
	{
		this.request.setUrl(this.url);
	},

	/**
	 * Detect a change in the timeout property
	 *
	 * @protected
	 *
	 * @param   oldValue  integer  The timeout old value
	 *
	 * @since  0.0.3
	 */
	timeoutChanged: function (oldValue)
	{
		this.request.setTimeout(this.timeout);
	},

	/**
	 * Handle fail event
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
	fail: function (inSender, inEvent)
	{
		var error = inEvent.error;
		switch (inEvent.error)
		{
			case 0:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Request failed')).render().show();
				break;
			case 400:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Bad Request')).render().show();
				break;
			case 401:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Unauthorized')).render().show();
				break;
			case 403:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Forbidden')).render().show();
				break;
			case 404:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Not Found')).render().show();
				break;
			case 500:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Internal Server Error')).render().show();
				break;
			case 501:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Not Implemented')).render().show();
				break;
			case 503:
				this.$.modal.setData($L('Error'), 'danger', error, $L('Service Unavailable')).render().show();
				break;
			case 'timeout':
				this.$.modal.setData($L('Error'), 'danger', $L('Timeout'), $L('Timeout with the server')).render().show();
				break;
		}

		// Prevents event propagation
		return true;
	},

	/**
	 * Request the data
	 *
	 * @public
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	requestData: function ()
	{
		// Tells Ajax what the callback success function is
		this.request.response(enyo.bind(this, 'success'));

		// Tells Ajax what the callback failure function is
		this.request.error(enyo.bind(this, 'failure'));

		// Makes the Ajax call with parameters
		this.request.go({task: 'data'});
	},

	/**
	 * Callback failure function
	 *
	 * @protected
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
	 * @protected
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
		// Broadcast the data to the children fields

		// Construct the header
		this.$.head.setTitle(inResponse.title);
		this.$.head.setPdf(inResponse.pdf);
		this.$.head.render();

		// Construct the indicator
		this.$.indicator.setEnd(inResponse.total);
		this.$.indicator.setSuccess(inResponse.success);
		this.$.indicator.setDanger(inResponse.error);
		this.$.indicator.setInfo(inResponse.help);

		// Construct the cues object
		this.$.cues.setLimit(inResponse.limit).setElements(inResponse.cues).render();

		// Construct the form object
		this.$.form.setRequest(this.request);

		// Construct the video object
		for (var source in inResponse.sources)
		{
			this.$.video.addSource(inResponse.sources[source].url, inResponse.sources[source].type);
		}
		if (inResponse.poster)
		{
			this.$.video.setPoster(inResponse.poster);
		}
		this.$.video.setLanguage(inResponse.language);
		this.$.video.setTrack(inResponse.track);
		this.$.video.render();

		// Hide the progressbar
		this.$.progressbar.hide();
	},

	/**
	 * Handle select cue event
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
	cueSelect: function (inSender, inEvent)
	{
		var cue = inEvent.originator, data = cue.getData(), begin = data.begin, end = data.end;

		this.$.video.pause();
		this.$.video.setBegin(begin);
		this.$.video.setTime(begin);
		this.$.video.setEnd(end);

		this.$.form.setCue(cue);

		this.$.progressbar.setBegin(begin).setWarning(begin).setEnd(end);
		this.$.progressbar.show();

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle deselect cue event
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
	cueDeselect: function (inSender, inEvent)
	{
		this.$.video.setBegin(0);
		this.$.video.setEnd(Infinity);

		this.$.form.setCue(null);

		this.$.progressbar.hide();

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle text track change event
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
	trackChange: function (inSender, inEvent)
	{
		this.$.video.changeCue(inEvent.number, inEvent.text);

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle help increment event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.3
	 */
	helpIncrement: function (inSender, inEvent)
	{
		this.$.indicator.setInfo(this.$.indicator.getInfo() + 1);

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle success increment event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.3
	 */
	successIncrement: function (inSender, inEvent)
	{
		this.$.indicator.setSuccess(this.$.indicator.getSuccess() + 1);

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle error increment event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.3
	 */
	errorIncrement: function (inSender, inEvent)
	{
		this.$.indicator.setDanger(this.$.indicator.getDanger() + 1);

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle error decrement event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.3
	 */
	errorDecrement: function (inSender, inEvent)
	{
		this.$.indicator.setDanger(this.$.indicator.getDanger() - 1);

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle time update event
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
	timeUpdate: function (inSender, inEvent)
	{
		this.$.progressbar.setWarning(inEvent.time);

		// Prevents event propagation
		return true;
	},
});
