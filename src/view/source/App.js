// Main application structure
enyo.kind({
	name : "App",
	classes: "container-fluid",
	components:[
		// Title and description
		{tag: "div", name: "header", classes:"row-fluid", components:[
			{tag: "div", name: "span12", classes:"span12", components:[
				{tag: "div", classes:"well well-small", components:[
					{tag: "h1", name:"title", content: "Vidéo"}
				]}
			]}
		]},
		{tag: "div", name: "body", classes:"row-fluid", components:[
			{tag: "div", name: "span6", classes:"span6", components:[
			// Video
			{kind: "Video", name : "video"},
			]},
			// Sequence list
			{tag: "div", name: "span6", classes:"span6", components:[
				{tag: "h1", content: "Liste."},
				{kind: "Sequences", style: "height: 200px;"}
			]}
		]},
		// Exercise
		{tag: "div", name: "footer", classes:"row-fluid", components:[
			{tag: "div", name: "span12", classes:"span12", components:[
				{tag: "h1", content: "footer."}
			]}
		]}
	], 
	
	create: function(){
		this.inherited(arguments);
		this.getTitle();
		
	},
	
	// Function to get the title and the description
	getTitle: function(){
		// Request creation
		var request = new enyo.Ajax({
	    		url: document.URL,
				// url = "serveur.php";
	    		method: "POST", //"GET" or "POST"
	    		handleAs: "text", //"json", "text", or "xml"
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "getTitleResponse")); 
		//makes the Ajax call with parameters
        request.go({task: 'title'}); 
	},
	
	getTitleResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem when I try to get the title, please try again later...');
	        return;
	    }
		// We update the video title with the value in the response
		var response = JSON.parse(inResponse);
		this.$.title.content = response.title;
		this.$.title.render();
	}
});

