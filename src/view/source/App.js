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
	 * Each property will have a public setter and a getter method
	 */
	published: {url: null, timeout: null},

	/**
	 * Handlers
	 * - onCueTapped: handle a tap on a cue
	 * - onHelpT
	 */
	handlers: {onCueTapped: 'cueTapped', onHelpTapped: 'helpTapped', onTrackChanged: 'trackChanged', ontimeupdate: 'timeUpdated'},

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
	 * - progressbar (Elang.Progressbar): Student's progress
	 * - video (Elang.Video): Video exercise
	 * - slider (Elang.Progressbar): Cue cursor
	 * - 
	 */
	components:
	[
		// Title and description
		{
			classes: 'row-fluid',
			components:
			[
				{
					classes: 'span12',
					components: [{kind: 'Elang.Head', name: 'head'}]
				}
			]
		},

		// Video, Input and cues list
		{
			classes: 'row-fluid',
			components:
			[
				{
					classes: 'span6',
					components:
					[
						// video
						{kind: 'Elang.Video', name: 'video'},

						// slider
						{kind: 'Elang.Progressbar', name: 'slider'},

						// input
						{kind: 'Elang.Input', name: 'input'}
					]
				},
				{
					classes: 'span6',
					components:
					[
						// Cue list
						{
							kind: 'Elang.Cues',
							name: 'cues'
						}
					]
				}
			]
		},

		// Modal to alert when the ajax request failed
		{
			kind: 'Elang.Modal',
			name: 'modal'
		}
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
				this.$.modal.setMessage('400 Bad Request');
				break;
			case 401:
				this.$.modal.setMessage('401 Unauthorized');
				break;
			case 403:
				this.$.modal.setMessage('403 Forbidden');
				break;
			case 404:
				this.$.modal.setMessage('404 Not Found');
				break;
			case 500:
				this.$.modal.setMessage('500 Internal Server Error');
				break;
			case 501:
				this.$.modal.setMessage('501 Not Implemented');
				break;
			case 503:
				this.$.modal.setMessage('503 Service Unavailable');
				break;
			case 'timeout':
				this.$.modal.setMessage('Timeout with the server');
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
		this.$.head.setNumber(inResponse.number);
		this.$.head.setSuccess(inResponse.success);
		this.$.head.setError(inResponse.error);
		this.$.head.setHelp(inResponse.help);
		this.$.head.setPdf(inResponse.pdf);
		this.$.head.render();

		// Construct the cues object
		this.$.cues.setCues(inResponse.cues).setPage(inResponse.page);
		this.$.cues.update();

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

		// Hide the slider
		this.$.slider.hide();
	},

	/**
	 * Handle tap event on a cue
	 *
	 * @protected
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	cueTapped: function (inSender, inEvent)
	{
		if (inEvent.cue == null)
		{
			this.$.video.setBegin(0);
			this.$.video.setEnd(Infinity);

			this.$.input.setCue(null);

			this.$.slider.hide();
		}
		else
		{
			this.$.video.pause();
			this.$.video.setBegin(inEvent.cue.begin);
			this.$.video.setTime(inEvent.cue.begin);
			this.$.video.setEnd(inEvent.cue.end);

			this.$.input.setCue(inEvent.cue);

			this.$.slider.setBegin(inEvent.cue.begin).setWarning(inEvent.cue.begin).setEnd(inEvent.cue.end);
			this.$.slider.show();
		}
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
		this.$.slider.setWarning(inEvent.time);
	},

	cueValidated: function (inSender,inEvent)
	{
	  this.$.cues.setType('verified');
	},
});
