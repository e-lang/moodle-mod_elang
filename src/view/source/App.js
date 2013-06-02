//enyo.dispatcher.listen(document, "myEvent");
// Main application structure
enyo.kind({
	name : "App",
	classes: "container-fluid",
	style:"overflow: auto;",
	published:
	{
		url: '',
		timeout:''
	},
	handlers: {
		onSequenceItemTapped : "sequenceTapped",
		onValidSequence : "sequenceValidated",
		onHelpTapped : "helpTapped",	
	},
	components:[
		// Title and description
		{tag: "div", name: "header", classes:"row-fluid", components:[
			{tag: "div", name: "title_span12", classes:"span12", components:[
				{tag: "div", classes:"well well-small", components:[
					{kind: "Elang.Head", name:"head"}
				]
				}

			]}
		]},
		{tag:"div", classes:"well", components:[
		{tag: "div", name: "body", classes:"row-fluid", components:[
				{tag: "div", name: "video_span6", classes:"span8", components:[
					// Video
					{kind: "Elang.Video", name : "video"}
				]},
			
				// Sequence list
				{tag: "div", name: "list_span6", classes:"span4", components:[
					{tag: "h1", content: "Liste des séquences"},
					{kind: "Sequences", name:"sequences"}
				]}
			]},
			
		// Exercise
		{tag: "div", name: "footer", classes:"row-fluid", components:[
			{tag: "div", name: "input_span12", classes:"span12", components:[
				{tag: "h1", content: "Exercice"},
				{kind: "elang.input", name:"input"}
			]}
		]}
		]}, 
		
		// Modal to alert when the ajax request failed 
		{tag: 'div', classes: 'modal hide fade', components: [
			{tag: 'div', classes: 'modal-header', components: [
				{tag: 'button', name: 'buttonAlert1', type: 'button', classes: 'close', attributes: {'data-dismiss': 'modal', 'aria-hidden': 'true'}, content:'x'}, 
				{tag: 'h1', content:'Error !'}, 
			]}, 
			{tag: 'div', classes: 'modal-body', components: [
				{tag: 'p', content:'There is a problem when I try to get the content, please try again later...'}
			]}, 
			{tag: 'div', classes: 'modal-footer', components: [
				{tag: 'button', name: 'buttonAlert2', classes: 'btn', attributes: {'data-dismiss': 'modal', 'aria-hidden': 'true'}, content:'Close'}, 
			]}, 
		]}
	], 
	sequenceTapped:function(inSender,inEvent){
		/*alert("Sequence id "+this.$.sequences.getIdSequenceCourante()+
		" de "+this.$.sequences.getDebutSequenceCourante()+" a "+this.$.sequences.getFinSequenceCourante());*/
		this.$.video.setTime(this.$.sequences.getDebutSequenceCourante());
		this.$.video.setEnd(this.$.sequences.getFinSequenceCourante());
		this.$.input.displaySequence(this.$.sequences.getIdSequenceCourante());	
    },
	
	helpTapped:function(inSender,inEvent){
	  this.$.sequences.setType('help');
    },
	
	sequenceValidated:function(inSender,inEvent)
	{
      this.$.sequences.setType('verified');
	},
	
	create: function(){
		this.inherited(arguments);
		this.getData();
	},
	
	
	// Function to get the video data 
	getData: function(){
		// Request creation
		var request = new enyo.Ajax({
	    		url: this.url, //document.URL,
	    		method: "POST", //"GET" or "POST"
	    		//handleAs: "json", //"json", "text", or "xml"
	    		timeout: this.timeout
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "handleDataResponse"));

		request.error(enyo.bind(this, 'fail'));
		//makes the Ajax call with parameters
        request.go({task: 'data'}); 
	},

	fail: function (inRequest, inError)
	{
		alert(inRequest + inError);
		inRequest.fail(inError);
	},
	
	handleDataResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
			$('.modal').modal('toggle');
	        return;
	    }

		//var response = JSON.parse(inResponse);
		
		// Broadcast the data to the children fields 
		this.$.head.setHeadTitle(inResponse.title);
		this.$.head.setHeadDescription(inResponse.description);
		this.$.input.setInputList(inResponse.inputs);
		
		// Call the function to update the children
		this.$.head.updateData();

		
		this.$.input.setInputList(inResponse.inputs);
		
		// Construct sequences object
		this.$.sequences.createSequences(inResponse.sequences);

		// Construct video object
		for (var source in inResponse.sources)
		{
			this.$.video.addSource(inResponse.sources[source].url, inResponse.sources[source].type);
		}
		this.$.video.render();

		if (inResponse.poster)
		{
			this.$.video.setPoster(inResponse.poster);
		}
		this.$.video.setLanguage(inResponse.language);
		this.$.video.setTrack(inResponse.track);
		captionator.captionify(document.getElementById(this.$.video.getAttribute('id')));
	}

});
