/**
 * Application kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
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
	 * Each property will have public setter and a getter methods
	 */
	published: {url: null, timeout: null},

	/**
	 * Handlers:
	 - onCueSelect: fired when a cue is selected
	 - onPageChange: fired when a new page is selected
	 - onCueDeselect: fired when a cue is deselected
	 - onTrackChange: fired when a text track is changed
	 - ontimeupdate: fired when the video time changed
	 - onFail: fired when an ajax error occurred
	 */
	handlers: {
		onCueSelect: 'cueSelect',
		onPageChange: 'cueDeselect',
		onCueDeselect: 'cueDeselect',
		onTrackChange: 'trackChange',
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
						{kind: 'Elang.Input', name: 'input'}
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
	 * Handle fail event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		    Event fired
	 *
	 * @return  true
	 */
	fail: function (inSender, inEvent)
	{
		var error = inEvent.error;
		switch (inEvent.error)
		{
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
	 */
	requestData: function ()
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

		// Makes the Ajax call with parameters
		request.go({task: 'data'});
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
	 */
	success: function (inRequest, inResponse)
	{
		// Broadcast the data to the children fields

		// Construct the header
		this.$.head.setTitle(inResponse.title);
/*		this.$.head.setNumber(inResponse.number);
		this.$.head.setSuccess(inResponse.success);
		this.$.head.setError(inResponse.error);
		this.$.head.setHelp(inResponse.help);*/
		this.$.head.setPdf(inResponse.pdf);
		this.$.head.render();

		// Construct the cues object
		this.$.cues.setLimit(inResponse.limit).setElements(inResponse.cues).render();

		// Construct the input object
		this.$.input.setUrl(this.url);
		this.$.input.setTimeout(this.timeout);

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
	 */
	cueSelect: function (inSender, inEvent)
	{
		var cue = inEvent.originator, data = cue.getData(), begin = data.begin, end = data.end;

		this.$.video.pause();
		this.$.video.setBegin(begin);
		this.$.video.setTime(begin);
		this.$.video.setEnd(end);

		this.$.input.setCue(cue);

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
	 */
	cueDeselect: function (inSender, inEvent)
	{
		this.$.video.setBegin(0);
		this.$.video.setEnd(Infinity);

		this.$.input.setCue(null);

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
	 * @param   inEvent   Object		    Event fired
	 *
	 * @return  true
	 */
	trackChange: function (inSender, inEvent)
	{
		this.$.video.changeCue(inEvent.number, inEvent.text);

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
	 */
	timeUpdate: function (inSender, inEvent)
	{
		this.$.progressbar.setWarning(inEvent.time);

		// Prevents event propagation
		return true;
	},
});
