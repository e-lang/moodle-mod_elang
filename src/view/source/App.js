/**
 * Application kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */
enyo.kind({
	name: 'Elang.App',

	classes: 'container-fluid',

	style: 'overflow: auto;',

	published:
	{
		url: '',
		timeout:''
	},

	handlers:
	{
		onCueTapped: 'cueTapped',
		onValidCue: 'cueValidated',
		onHelpTapped: 'helpTapped',
		onCueChanged: 'cueChanged',
		ontimeupdate: 'timeUpdated',
	},

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
						// Video
						{kind: 'Elang.Video', name: 'video'},

						// Progress bar
						{kind: 'Elang.Progressbar', name: 'progressbar'},

						// Input
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

	cueTapped: function (inSender, inEvent)
	{
		if (inEvent.cue != null)
		{
			this.$.video.pause();
			this.$.video.setBegin(inEvent.cue.begin);
			this.$.video.setTime(inEvent.cue.begin);
			this.$.video.setEnd(inEvent.cue.end);
			this.$.input.setCue(inEvent.cue);
			this.$.progressbar.show();
			this.$.progressbar.setBegin(inEvent.cue.begin).setCurrent(inEvent.cue.begin).setEnd(inEvent.cue.end).update();
		}
		else
		{
			this.$.video.setBegin(0);
			this.$.video.setEnd(Infinity);
			this.$.input.setCue(inEvent.cue);
			this.$.progressbar.hide();
		}
	},

	helpTapped: function (inSender, inEvent){
	  this.$.cues.setType('help');
	},

	timeUpdated: function (inSender, inEvent)
	{
		this.$.progressbar.setCurrent(inEvent.time).update();
	},

	cueValidated: function (inSender,inEvent)
	{
	  this.$.cues.setType('verified');
	},

	cueChanged: function (inSender, inEvent)
	{
		this.$.video.changeCue(inEvent.number, inEvent.text);
	},

	/**
	 * Get the video data
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

		this.$.progressbar.hide();
	}
});
