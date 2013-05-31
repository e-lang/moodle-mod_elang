enyo.kind({
	name: "elang.input",
		components: [		
		{components:
			[
				{kind:'enyo.Scroller', vertical:'scroll'},
				{kind:"Button", classes:"btn", name:" Text", content: "Text", id:1, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Input", content: "Input", id:2, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Check", content: "Check", id:3, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Help", content: "Help", id:4, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Reset", content: "Reset", id:5, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Render", content: "Render", id:6, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" ReplaceVideo", content: "TestListe", id:6, ontap:"updateDataInput"},
				
				//{kind:"Button",  content:"Check", onclick:"buttonTapped"}
				
			]
		}/* a supprimper lors du push*/,
		
		{name:"result", kind: enyo.Control, components:[]}		
	
	],
	
	published: {
		inputList: '',
		gblID: '',
		inputCpt: 0
	},
	

	
	updateDataInput: function()
	{
		for (i = 0; i < this.inputList.length; i++) 
		{
			for (j = 0; j < this.inputList[i].content.length; j++) 
			{
				switch(this.inputList[i].content[j].type) {
				case 'text' :
					this.addText(i, this.inputList[i].content[j].content);
					break;
				case 'input' :
					this.addInput(i, '', j, this.inputCpt);
					this.inputCpt = this.inputCpt + 1;
					break;
				}				
			}
		}	
	},
	
	
	addText: function(noSec, text) {
		this.$.result.createComponent({tag:"p", content: text });
		this.$.result.render();	
	},
	
	addInput: function(noSec, content, ident, input_cpt) {
		this.$.result.createComponent(		
			{tag:"div", classes:"input-append", id: 'd'+ noSec + '_' + ident, components: [
				{kind:"Input", id:noSec + '_' + ident + '_' + input_cpt ,  name:"Render", value:content},
				{tag:"button", classes:"btn btn-success", type:"button", ontap:"checkTapped", id:'c' + noSec + '_' + ident+ '_' + input_cpt, name:"Check", content:"Check"},
				{tag:"button", classes:"btn btn-info",    type:"button", ontap:"helpTapped", id: 'h' + noSec + '_' + ident+ '_' + input_cpt, name:"Help",  content:"Help"},	
			]},
			{owner: this}
		);
		this.$.result.render();
	},

	
	reset: function() {
		this.$.result.destroyComponents();		
		//faire aussi pour ceux créés : 
		this.destroyComponents();
		this.render();

	},		
		
	checkTapped: function(inSender, inEvent) {		
		var id = inSender.id.substr(1);//supprimer le c
		var tabID = id.split("_");
		var i = tabID[0];
		var j = tabID[1];
		var k = tabID[2];
		gblID = i + '_' + j;
		//alert('i : ' + i + ', j : ' + j);
		//alert(document.getElementById(id).getAttribute('value') + ' ? = ' + this.inputList[i].content[j].content);
		//alert(gblID);
		this.verify(document.getElementById(id).getAttribute('value'), i, k);
		/*if(document.getElementById(id).getAttribute('value') == this.inputList[i].content[j].content)
		{
			document.getElementById('d'+id).setAttribute('class', 'control-group success');
		}
		else
		{
			document.getElementById('d'+id).setAttribute('class', 'control-group error');
			this.bubble(onCheckTapped);
		}*/
		
	},
	
	
	

	handlers: {
		onItemTapped : "itemTapped"
	},
	itemTapped:function(inSender,inEvent){
		myContent = inEvent.originator.content;
		alert(myContent+" button was tapped");
		//this.addText.setContent(myContent);
    },
	
	// Function to get the video data 
	verify: function(rep, seqId, inputId){
		// Request creation
		var request = new enyo.Ajax({
	    		url: document.URL,
				// url = "serveur.php";
	    		method: "POST", //"GET" or "POST"
	    		handleAs: "text", //"json", "text", or "xml"
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "getVerifyResponse")); 
		//makes the Ajax call with parameters
		//alert(rep+"  "+seqId+" 	"+inputId);
		request.go({task: 'check', answer:rep, seq_id:seqId, input_id:inputId}); 
	},
	
	getVerifyResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem, please try again later...');
	        return;
	    }		
		
		//TODO gestion reponse
		var response = JSON.parse(inResponse);
		
		// Broadcast the data to the children fields 
		var isOk = (response.check);
		//alert(response.check+"		base : "+response.answer+"	rep reçue:"+response.answer_received);
		if(isOk == 'true')
			document.getElementById('d' + gblID).setAttribute('class', 'control-group success');
		else
			document.getElementById('d' + gblID).setAttribute('class', 'control-group error');
			

	}
	
});

