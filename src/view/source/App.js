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
	 * Handlers
	 * - onCueTapped: handle a tap on a cue
	 * - onHelpT
	 */
	handlers: {
		onCueSelect: 'cueSelect',
		onCueDeselect: 'cueDeselect',
		onHelpTapped: 'helpTapped',
		onTrackChanged: 'trackChanged',
		ontimeupdate: 'timeUpdated'
	},

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
		switch (inError)
		{
			case 400:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Bad Request')).render().show();
				break;
			case 401:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Unauthorized')).render().show();
				break;
			case 403:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Forbidden')).render().show();
				break;
			case 404:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Not Found')).render().show();
				break;
			case 500:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Internal Server Error')).render().show();
				break;
			case 501:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Not Implemented')).render().show();
				break;
			case 503:
				this.$.modal.setData($L('Error'), 'danger', inError, $L('Service Unavailable')).render().show();
				break;
			case 'timeout':
				this.$.modal.setData($L('Error'), 'danger', $L('Timeout'), $L('Timeout with the server')).render().show();
				break;
		}
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
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	cueSelect: function (inSender, inEvent)
	{
		this.$.video.pause();
		this.$.video.setBegin(inEvent.cue.begin);
		this.$.video.setTime(inEvent.cue.begin);
		this.$.video.setEnd(inEvent.cue.end);

		this.$.input.setCue(inEvent.cue);

		this.$.progressbar.setBegin(inEvent.cue.begin).setWarning(inEvent.cue.begin).setEnd(inEvent.cue.end);
		this.$.progressbar.show();
	},

	/**
	 * Handle deselect cue event
	 *
	 * @protected
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	cueDeselect: function (inSender, inEvent)
	{
		this.$.video.setBegin(0);
		this.$.video.setEnd(Infinity);

		this.$.input.setCue(null);

		this.$.progressbar.hide();
	},

	/**
	 * Handle help event on a input
	 *
	 * @protected
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	helpTapped: function (inSender, inEvent)
	{
	},

	trackChanged: function (inSender, inEvent)
	{
		this.$.video.changeCue(inEvent.number, inEvent.text);
	},

	timeUpdated: function (inSender, inEvent)
	{
		this.$.progressbar.setWarning(inEvent.time);
	},

	cueValidated: function (inSender,inEvent)
	{
	  this.$.cues.setType('verified');
	},
});
