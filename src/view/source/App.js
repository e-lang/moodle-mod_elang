//enyo.dispatcher.listen(document, "myEvent");
// Main application structure
enyo.kind({
	name : "App",
	classes: "container-fluid",
	style:"height: 200%;",
	handlers: {
		onSequenceItemTapped : "sequenceTapped",
		onRenderTapped : "renderTapped",
		onHelpTapped : "helpTapped",	
	},
	components:[
		// Title and description
		{tag: "div", name: "header", classes:"row-fluid", components:[
			{tag: "div", name: "title_span12", classes:"span12", components:[
				{tag: "div", classes:"well well-small", components:[
					{kind: "Head", name:"head"}
				]
				}

			]}
		]},
		{tag:"div", classes:"well", components:[
		{tag: "div", name: "body", classes:"row-fluid", components:[
				{tag: "div", name: "video_span6", classes:"span6", components:[
					// Video
					{kind: "Video", name : "video"}
				]},
			
				// Sequence list
				{tag: "div", name: "list_span6", classes:"span6", components:[
					{tag: "h1", content: "Liste."},
					{kind: "Sequences", name:"sequences"}
				]}
			]},
			
		// Exercise
		{tag: "div", name: "footer", classes:"row-fluid", components:[
			{tag: "div", name: "input_span12", classes:"span12", components:[
				{tag: "h1", content: "footer."},
				{kind: "elang.input", name:"input"}
			]}
		]}
		]}
	], 
	sequenceTapped:function(inSender,inEvent){
      alert("sequenceTapped");
	  //this.$.input.setInput();
    },
	
	helpTapped:function(inSender,inEvent){
      alert("helpTapped");
	  //this.$.sequences.setHelp();
    },
	
	renderTapped:function(inSender,inEvent){
      alert("renderTapped");
	  //this.$.sequences.setType();
    },
	create: function(){
		this.inherited(arguments);
		this.getData();
	},
	
	// Function to get the video data 
	getData: function(){
		// Request creation
		var request = new enyo.Ajax({
	    		url: document.URL,
				// url = "serveur.php";
	    		method: "POST", //"GET" or "POST"
	    		handleAs: "text", //"json", "text", or "xml"
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "getDataResponse")); 
		//makes the Ajax call with parameters
        request.go({task: 'data'}); 
	},
	
	getDataResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem when I try to get the title, please try again later...');
	        return;
	    }		// We update the video title with the value in the response

		var response = JSON.parse(inResponse);
		
		// Broadcast the data to the children fields 
		this.$.head.setHeadTitle(response.title);
		this.$.head.setHeadDescription(response.description);
		// Call the function to update the children
		this.$.head.updateData();
		
		this.$.input.setInputList(response.inputs);
		this.$.input.updateDataInput();
		
		//this.$.sequences.setListSequences(response.sequences);
		//this.$.sequences.updateSequences(response.sequences);

	}

});
