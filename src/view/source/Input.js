enyo.kind({
	name: "elang.input",
		components: [		
		{
			components:[]// Nous insérions ici des boutons afin de tester 
			// nos modifications, et ce avant de lier notre Input au reste 
			// de l'application. Ce n'est plus utile maintenant
		},
		
		{name:"result", components:[]}		// c'est ici que nous insérons les 
		//séquences à la volée (inputs et textes)
	],
	
	published: {
		inputList: '',// Remplis depuis la liste des séquences lors du clic
		gblID: '',// Permet de sauvegarder l'id lors du clic sur check et 		
		// help pour vérifier les réponses
		inputCpt: 0,// Sert d'ID aux inputs. On l'a en global car on en a 		
		// besoin pour valider toute la sequence (une fois que tous est remplis).
		totalCheck: 0// Sert à compter les checks (on veut autant de check que 
		// d'input. Comparé à inputCpt
	},

	
	// Fonction appelée par la Liste de Séquence(lors d'un clic sur une séquence
	displaySequence: function(id)
	{
		this.reset();// On supprime l'ancienne séquence
		for (x in this.inputList)
		{
			sequence = this.inputList[x];
			if (sequence.seq_id == id)// lors qu'on se positonne sur la bonne
			{
				for (y in sequence.content)
				{
					// On traite chaque contenu en fonction de son type
					seq_content = sequence.content[y];
					switch(seq_content.type)
					{
						// On utilise id-1 car les id commencent à 1
						// alors que les indexes des tableaux à 0
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
	
	// Fonction utilisée pour ajouter du texte au résult
	addText: function(noSec, text) {
		this.$.result.createComponent({tag:"p", content: text });
		this.$.result.render();	// Le rendu est fait à chaque fois, on  aussi peut 
		// le faire une fois que tous est généré.
	},
	
	// Fonction utilisée pour ajouter des inputs au résult
	addInput: function(noSec, content, ident, input_cpt) {
		this.$.result.createComponent(		
			// Le div est indispensable pour changer la couleur de l'input en fonction du check et help.
			// Nous avons généré les même id pour chacun des 3 éléments d'un input. Seul un premier caractère
			// permet de les différencier. Cette comodité nous a bien aidé.
			{tag:'div', name :"divseq" ,classes:"input-append", id: 'd'+ noSec + '_' + ident, components: [
				{kind:"Input", id:noSec + '_' + ident + '_' + input_cpt ,  name:"Render", value:content},
				{tag:"button", classes:"btn btn-success", type:"button", ontap:"checkTapped", id:'c' + noSec + '_' + ident+ '_' + input_cpt, name:"Check", content:"Check"},
				{tag:"button", classes:"btn btn-info",    type:"button", ontap:"helpTapped",  id: 'h' + noSec + '_' + ident+ '_' + input_cpt, name:"Help",  content:"Help"},	
			]},
			{owner: this}// Indispensable pour que result (le parent) et les enfants créés se connaissent. Dans le cas contraire, impossible d'intercepter les onTap.
		);
		this.$.result.render();
	},

	// Fonction utilisée avant chaque nouvelle séquence pour supprimer le result
	reset: function() {
		this.$.result.destroy();// Détruit le result.
		// Nous avons essayé de détruire les enfants,
		// mais impossible d'y parvenir. Seuls les 
		// textes se supprimaient, pas les input
		// ni les boutons. On recrée donc un autre 
		// result vide une fois que tous est supprimé.
		
		// On remet à jour les globales
		this.setGblID('');
		this.setInputCpt(0);
		this.setTotalCheck(0);
		
		// On crée alors le nouveau result vide
		this.createComponent(
			{ name:"result", components:[]}
		);
		this.render();
	},		
		
	// Fonction appelée lors d'un clic sur le bouton Check
	checkTapped: function(inSender, inEvent) {			
		var id = inSender.id.substr(1);//supprimer le c
		var tabID = id.split("_");
		var i = tabID[0];
		var j = tabID[1];
		var k = tabID[2];
		gblID = i + '_' + j;// On sauvegarde cet id pour vérifier
		// On appelle enfin la requette AJAX en donnant les indentifiants
		// de l'input remplir ainsi que la valeur de ce dernier pour comparer
		this.verify(document.getElementById(id).getAttribute('value'), i, k);		
	},	
	
		
	// Fonction pour demander de vérifier les infos de l'input
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
		request.go({task: 'check', answer:rep, seq_id:seqId, input_id:inputId}); 
	},
	
	
	// Fonction pour vérifier les infos de l'input
	getVerifyResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem, please try again later...');
	        return;
	    }
		var response = JSON.parse(inResponse);		
		// Broadcast the data to the children fields 
		var isOk = (response.check);
		if(isOk == 'true')
		{
			// On vérifie si tous les check sont survenus ...
			this.totalCheck = this.totalCheck + 1;
			if (this.totalCheck == this.inputCpt)
			{
				// ... pour remonter une séquence valide
				this.bubble('onValidSequence');
			}
			// On change le design en vert
			document.getElementById('d' + gblID).setAttribute('class', 'control-group success');
			//On bloque également lorsque le check est OK
			var child = document.getElementById('d' + gblID).firstChild;
			child.disabled = 'true';
			while(child.nextSibling != null)// Si on change l'ordre des Input et Boutons, osef ici
			{
				child = child.nextSibling;
				child.disabled = 'true';
			}		
		}
		else
			document.getElementById('d' + gblID).setAttribute('class', 'control-group error');
	},	
	
	
	// Fonction appelée lors d'un clic sur le bouton Help
	helpTapped: function(inSender, inEvent) {		
		var id = inSender.id.substr(1);//supprimer le h
		var tabID = id.split("_");
		var i = tabID[0];
		var j = tabID[1];
		var k = tabID[2];
		gblID = i + '_' + j;// Même chose
		// on appelle enfin la requette AJAX en ne donnant que 
		// les identifiants de l'input (on souhaite récupérer
		// la valeur)
		this.help(i, k);
		// On fait remonter l'événement à App.js pour Sequence.js.
		// A chaque clic sur help on récupère pour logger son action
		this.bubble('onHelpTapped');
	},		
	
		
	// Function AJAX pour demander de récupérer les infos de l'input
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
	
	
	// Function AJAX pour demander de récupérer les infos de l'input
	getResponse: function(inRequest, inResponse){
		// If there is nothing in the response then return early.
		if (!inResponse) { 
	        alert('There is a problem, please try again later...');
	        return;
	    }				
		var response = JSON.parse(inResponse);		
		// Broadcast the data to the children fields 
		var reponsehelp = (response.help);
		// On change le design en bleu
		document.getElementById('d' + gblID).setAttribute('class', 'control-group info');	
		var child = document.getElementById('d' + gblID).firstChild;		
		if (child.toString() == '[object HTMLInputElement]')
		{	
			// On affiche la réponse bloquée à l'étudiant
			child.value=reponsehelp;
			child.disabled = 'true';
			while(child.nextSibling != null)// Pareil, osef
			{
				child = child.nextSibling;
				child.disabled = 'true';
			}
		}				
	}
	
	
});

