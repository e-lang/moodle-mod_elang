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
		onSequenceTapped: 'sequenceTapped',
		onValidSequence: 'sequenceValidated',
		onHelpTapped: 'helpTapped',
		onReloadTapped: 'reloadTapped',
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

		// Video, Input and sequences list
		{
			classes: 'row-fluid',
			components:
			[
				{
					classes: 'span7',
					components:
					[
						// Video
						{kind: 'Elang.Video', name: 'video'},

						// Reload and progress bar
						{
							classes: 'row-fluid',
							components:
							[
								{
									// Reload
									classes: 'span1',
									components: [{kind: 'Elang.Reload', name: 'reload'}]
								},
								{
									// Progress bar
									classes: 'span11',
									components: [{kind: 'Elang.Progressbar', name: 'progressbar'}]
								}
							]
						},
						{
							// Input
							classes: '',
							components: [{kind: 'Elang.Input', name: 'input'}]
						}						
					]
				},
				{
					classes: 'span5',
					components:
					[
						// Sequence list
						{
							kind: 'Elang.Sequences',
							name: 'sequences'
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

	sequenceTapped: function (inSender, inEvent)
	{
		this.$.video.pause();
		this.$.video.setTime(inEvent.sequence.begin);
		this.$.video.setEnd(inEvent.sequence.end);
		this.$.reload.setSequence(inEvent.sequence);
		this.$.input.setSequence(inEvent.sequence);
		this.$.progressbar.setBegin(inEvent.sequence.begin).setCurrent(inEvent.sequence.begin).setEnd(inEvent.sequence.end).update();
	},

	helpTapped: function (inSender, inEvent){
	  this.$.sequences.setType('help');
	},

	reloadTapped: function (inSender, inEvent)
	{
		this.$.video.setTime(inEvent.sequence.begin);
		this.$.video.setEnd(inEvent.sequence.end);
		this.$.video.play();
	},

	timeUpdated: function (inSender, inEvent)
	{
		this.$.progressbar.setCurrent(inEvent.time).update();
	},

	sequenceValidated: function (inSender,inEvent)
	{
	  this.$.sequences.setType('verified');
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
		this.$.head.setDescription(inResponse.description);
		this.$.head.setNumber(inResponse.number);
		this.$.head.setSuccess(inResponse.success);
		this.$.head.setError(inResponse.error);
		this.$.head.setHelp(inResponse.help);
		this.$.head.update();

		// Construct the sequences object
		this.$.sequences.setSequences(inResponse.sequences).setPage(inResponse.page);
		this.$.sequences.update();

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
		this.$.video.update();
	}
});
