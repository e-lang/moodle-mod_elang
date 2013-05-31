enyo.kind({
	name: "elang.input",
		components: [		
		{components:
			[
				// {kind:'enyo.Scroller', vertical:'scroll'},
				// {kind:"Button", classes:"btn", name:" Text", content: "Text", id:1, ontap:"buttonTapped"},
				// {kind:"Button", classes:"btn", name:" Input", content: "Input", id:2, ontap:"buttonTapped"},
				// {kind:"Button", classes:"btn", name:" Check", content: "Check", id:3, ontap:"buttonTapped"},
				// {kind:"Button", classes:"btn", name:" Help", content: "Help", id:4, ontap:"buttonTapped"},
				// {kind:"Button", classes:"btn", name:" Reset", content: "Reset", id:5, ontap:"reset"},
				// {kind:"Button", classes:"btn", name:" Render", content: "Render", id:6, ontap:"buttonTapped"},
				// {kind:"Button", classes:"btn", name:" ReplaceVideo", content: "TestListe", id:6, ontap:"updateDataInput"},
				
				//{kind:"Button",  content:"Check", onclick:"buttonTapped"}
				
			]
		}/* a supprimper lors du push*/,
		
		{ name:"result", components:[]}		
		//{kind: enyo.Control, name:"result", components:[]}	
	],
	
	published: {
		inputList: '',
		gblID: '',
		inputCpt: 0,
		IdinputHelp:'',
		totalCheck: 0
	},
	

	
	
	displaySequence: function(id)
	{
		this.reset();
		for (x in this.inputList)
		{
			sequence = this.inputList[x];
			if (sequence.seq_id == id)
			{
				for ( y in sequence.content)
				{
					seq_content = sequence.content[y];
					switch(seq_content.type)
					{
						case 'text' :
							this.addText(id-1, seq_content.content);
							break;
						case 'input' :
							this.addInput(id-1, '', y, this.inputCpt);
							this.inputCpt = this.inputCpt + 1;
							break;
					}
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
			{tag:'div', name :"divseq" ,classes:"input-append", id: 'd'+ noSec + '_' + ident, components: [
				{kind:"Input", id:noSec + '_' + ident + '_' + input_cpt ,  name:"Render", value:content},
				{tag:"button", classes:"btn btn-success", type:"button", ontap:"checkTapped", id:'c' + noSec + '_' + ident+ '_' + input_cpt, name:"Check", content:"Check"},
				{tag:"button", classes:"btn btn-info",    type:"button", ontap:"helpTapped", id: 'h' + noSec + '_' + ident+ '_' + input_cpt, name:"Help",  content:"Help"},	
			]},
			{owner: this}
		);
		this.$.result.render();
	},

	
	reset: function() {
		//alert(this.$.result.getC);
		
		this.$.result.destroy();
		
		this.setGblID('');
		this.setInputCpt(0);
		this.setIdinputHelp('');
		this.setTotalCheck(0);
		
		this.createComponent(
			{ name:"result", components:[]}
		);
		this.render();
		
		

	},		
		
	checkTapped: function(inSender, inEvent) {		
	
		//alert(inSender.id);
		var id = inSender.id.substr(1);//supprimer le c
		var tabID = id.split("_");
		var i = tabID[0];
		var j = tabID[1];
		var k = tabID[2];
		gblID = i + '_' + j;
		IdinputHelp =  i + '_' + j+'_'+k;
		//alert('i : ' + i + ', j : ' + j);
		//alert(document.getElementById(id).getAttribute('value') + ' ? = ' + this.inputList[i].content[j].content);
		//alert(gblID);
		//alert(document.getElementById(id).getAttribute('value'));
	
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
	
	
	helpTapped: function(inSender, inEvent) {		
		var id = inSender.id.substr(1);//supprimer le c
		var tabID = id.split("_");
		var i = tabID[0];
		var j = tabID[1];
		var k = tabID[2];
		gblID = i + '_' + j;
		//alert('i : ' + i + ', j : ' + j+ ', k : ' + k);
		//alert(document.getElementById(id).getAttribute('value') + ' ? = ' + this.inputList[i].content[j].content);
		//alert(gblID);		
		this.help(i, k);
		this.bubble('onHelpTapped');
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
	
	help: function(seqId, inputId){
		// Request creation
		var request = new enyo.Ajax({
	    		url: document.URL,
				// url = "serveur.php";
	    		method: "POST", //"GET" or "POST"
	    		handleAs: "text", //"json", "text", or "xml"
	    	});	

		//tells Ajax what the callback function is
        request.response(enyo.bind(this, "getResponse")); 
		request.go({task: 'help', seq_id:seqId, input_id:inputId}); 
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
		//alert(isOk);
		//alert(response.check+"		base : "+response.answer+"	rep reçue:"+response.answer_received);
		if(isOk == 'true')
		{
			this.totalCheck = this.totalCheck + 1;
			if (this.totalCheck == this.inputCpt)
			{
				this.bubble('onValidSequence');
			}
			document.getElementById('d' + gblID).setAttribute('class', 'control-group success');
			//On bloque également lorsque le check est OK
			var child = document.getElementById('d' + gblID).firstChild;
			child.disabled = 'true';
			while(child.nextSibling != null)
			{
				child = child.nextSibling;
				child.disabled = 'true';
			}		
		}
			//document.getElementById('d' + gblID).class = 'control-group success';
		else
			document.getElementById('d' + gblID).setAttribute('class', 'control-group error');
	},
	
	getResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem, please try again later...');
	        return;
	    }		
		
		//TODO gestion reponse
		var response = JSON.parse(inResponse);
		
		// Broadcast the data to the children fields 
		var reponsehelp = (response.help);
		document.getElementById('d' + gblID).setAttribute('class', 'control-group info');
	
		var child = document.getElementById('d' + gblID).firstChild;
		
		if (child.toString() == '[object HTMLInputElement]')
		{	
			child.value=reponsehelp;
			child.disabled = 'true';
			while(child.nextSibling != null)
			{
				child = child.nextSibling;
				child.disabled = 'true';
			}
		}
		
		
	}
	
});

