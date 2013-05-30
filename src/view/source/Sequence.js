var tabSequences = new Array();

enyo.kind({
    name: "Sequences",
	tag: "div", 
	classes:"pagination",
	components: [
	{
		components: [
		{
			name: "list",
			tag: "ul"
		},
		{
			name: "Table",
			tag: "table",
			classes: "table table-striped table-bordered table-condensed"			
		}]
	}],
    datasource:[
		{kind:"Sequence", id:0, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:1, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:2, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:3, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:4, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:5, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:6, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
		{kind:"Sequence", id:7, titre:"titre0", texte:"le texte du titre0", debut:5, fin:10},
        {kind:"Sequence", id:8, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:9, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:10, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:11, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:12, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:13, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:14, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:15, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:16, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:17, titre:"titre1", texte:"le texte du titre1", debut:10, fin:15},
		{kind:"Sequence", id:18, titre:"titre2", texte:"le texte du titre2", debut:15, fin:20},
		{kind:"Sequence", id:19, titre:"titre3", texte:"le texte du titre3", debut:25, fin:30},
		{kind:"Sequence", id:20, titre:"titre4", texte:"le texte du titre3", debut:25, fin:30},
		{kind:"Sequence", id:21, titre:"titre4", texte:"le texte du titre3", debut:25, fin:30},
		{kind:"Sequence", id:22, titre:"titre5 treeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee tres tres tres tres tres long ", texte:"le texte du titre3", debut:25, fin:30}
    ],
    create: function(){
		this.inherited(arguments);
		this.letsGo();
		
		this.createSequence('addSequence');
		this.createSequence('addSequence');
		this.createSequence('addSequence');
		this.createSequence('addSequence');
		this.createSequence('addSequence');
    },
	
    setupItem:function(inSender,inEvent) {
		this.titre = this.datasource[inEvent.index].titre; 
		var newtitre = (this.titre.length > 20)?this.titre.substring(0,17)+"...":this.titre;
		this.$.sequenceItem.addRemoveClass("list-selected", inSender.isSelected(inEvent.index));
		this.$.itemTitle.setContent(newtitre );
    },
	
    sequenceItemTapped:function(inSender,inEvent){

	//alert("L'id de "+this.datasource[inEvent.index].titre+" est "+this.datasource[inEvent.index].id);
	//  alert("test");
    },
	
	createTab: function(title){
		this.$.list.createComponent({
			tag:'li',
			class:'active',
			components: [
				{
					tag: "a",
					attributes: {
						href:'#'
					},
					content: title,
					ontap: 'sequenceItemTapped'
				}
			]
		},
		{owner: this});
    },
	
	createSequence: function(title){
		this.$.Table.createComponent({
			tag:'tr',
			ontap: 'sequenceItemTapped',
			components: [
				{
					tag:'td',
					//ontap: 'sequenceItemTapped',
					components: [
						{
							content: title
						}
					]
				}
			]
		},
		{owner: this});
    },
	
	letsGo: function(){
		nbSequenceDerniereTab = (this.datasource.length%10); //modulo pour savoir combien de sequences seront affichées sur la derniere tab
		if(nbSequenceDerniereTab==0) { //si il n'y a pas de séquence sur la dernière tab, soit nombre de séquence division par 10 sans reste
			nbTab = Math.floor(this.datasource.length/10); //alors on ne fait rien
		}
		else {
			nbTab = Math.floor(this.datasource.length/10)+1; //sinon on ajoute une tab pour afficher les dernière séquences
		}
		for(t=1; t<=nbTab; t++) { //on créer les tab 
			this.createTab(t);
		}
		for(d=0; d<this.datasource.length; d++) { //on rempli le tableau tabSequences avec toutes les séquences
			this.tabSequences = this.datasource[d];
		}
		//alert(this.tabSequences.toString());
    },
	
	// Function to get the title and the description
	getListInputs: function(){
		// Request creation
		var request = new enyo.Ajax({
	    		url: document.URL,
				// url = "serveur.php";
	    		method: "POST", //"GET" or "POST"
	    		handleAs: "text", //"json", "text", or "xml"
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "getListInputsResponse")); 
		//makes the Ajax call with parameters
        request.go({task: 'ListInputs'}); 
	},
	
	getListSequences: function(){
		// Request creation
		var request = new enyo.Ajax({
	    		url: document.URL,
				// url = "serveur.php";
	    		method: "POST", //"GET" or "POST"
	    		handleAs: "text", //"json", "text", or "xml"
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "getListSequencesResponse")); 
		//makes the Ajax call with parameters
        request.go({task: 'ListSequences'}); 
	},
	
	getListSequencesResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem when I try to get the title, please try again later...');
	        return;
	    }
		// We update the video title with the value in the response
		var response = JSON.parse(inResponse);
		//this.$.title.content = response.title;
		//this.$.title.render();
	}
});

enyo.kind({
	name:"Sequence",
	published:{
		id : null,
		titre : null,
		texte : null,
		debut : null,
		fin : null
	},
	create: function(){
		this.inherited(arguments);
	}
});
