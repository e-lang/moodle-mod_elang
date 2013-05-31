enyo.kind({
    name: "Sequences",
	tag: "div", 
	classes:"pagination",

	published:{
		tabSequences : [],
		idSequenceCourante : null, // id séquence courante
		debutSequenceCourante : null, // début de la séquence courante
		finSequenceCourante : null // fin de la séquence courante
	},
	components: [
	{
		components: [
		{ //liste de la pagination
			name: "listPagination", 
			tag: "ul"
		},
		{ //tableau des séquences
			name: "tableauSequences",
			tag: "table",
			
			//table-bordered -> tableau avec des bordures
			//table-condensed -> lignes du tableau réduites
			classes: "table table-bordered table-condensed"			
		}]
	}],
	
    create: function(){
		this.inherited(arguments);
    },
	
	/*
	Méthode permettant l'initialisation des séquences 
	et le remplissage du tableau des séquences (tabSequences)
	*/
	updateSequences:function(listSequences){
		if(typeof(listSequences)!='undefined')
		{
			//Remplissage du tableau des séquences (tabSequences)
			for (i in listSequences){
				this.tabSequences[i]=this.createComponent(
					{
						kind:"Sequence",
						id:listSequences[i].id,
						titre:listSequences[i].titre,
						debut:listSequences[i].debut,
						fin:listSequences[i].fin,
						type: "notVerified"
					},
					{owner: this.tabSequences}
				);
			}
		}
		
		//Initialisation des séquences et du tableau
		this.letsGo();
	},

	// Evenement : lorsque l'on clique sur une ligne <tr> du tableau
    sequenceItemTapped:function(inSender,inEvent){
		//Récupération de l'id de la séquence qui est le nom (name) de la ligne 
		this.idSequenceCourante = inSender.name; 
		
		//On cherche la séquence courante (dans tabSequences)
		for (i in this.tabSequences)
		{
			if(this.tabSequences[i].id ==this.idSequenceCourante)
			{
				//On récupère le début et la fin de la séquence courante
				this.finSequenceCourante = this.tabSequences[i].fin;
				this.debutSequenceCourante = this.tabSequences[i].debut;
			}
		}
		
		/*
		On envoie un événement dans le bus pour le/les conteneur(s) parent(s) 
		(App.js récupérera l'événement)
		*/
		this.bubble("onSequenceItemTapped",inEvent);
    },
	
	//Méthode pour ajouter d'une page dans la liste de pagination
	createTab: function(title){
	
		this.$.listPagination.createComponent({
			tag:'li',
			class:'active',
			components: [
				{
					//Element cliquable
					tag: "a",
					classes: 'btn-link',
					attributes: {
						href:''
					},
					content: title,
					ontap: 'changeTab'
				}
			]
		},
		{owner: this}
		);
    },
	
	//Méthode pour créer la séquence
	createSequence: function(id, title, type){
		//Le status peut être notVerified, verified (par défaut) et help
		var status; 
		if(type=='notVerified') {status = 'error';}
		else if(type=='verified')  {status = 'success';}
		else if(type=='help')  {status = 'warning';}
		
		this.$.TBODY.createComponent({
			tag: 'tr', //Création d'une ligne
			name: id,
			classes: status,
			
			//Evénement
			ontap: 'sequenceItemTapped',
			components: [
				{
					//Création du seul élément de la ligne
					tag:'td',
					components: [
						{
							tag: "a",
							classes: 'btn-link',
							attributes: {
								href:''
							},
							content: title
						}
					]
				}
			]
		},
		{owner: this}
		);
    },
	
	//Création du corps du tableau
	createTbody: function(){
		this.$.tableauSequences.createComponent({
			name: 'TBODY',
			tag: 'tbody',
		},
		{owner: this}
		);
	},


	deleteTbody: function(){
		this.$.TBODY.destroy();
	},
	
	/*
	Initialisation des séquences et du tableau
	*/
	letsGo: function(){

			nbSequenceDerniereTab = (this.tabSequences.length%10); //modulo pour savoir combien de sequences seront affichées sur la derniere tab
			if(nbSequenceDerniereTab==0) { //si il n'y a pas de séquence sur la dernière tab, soit nombre de séquence division par 10 sans reste
				nbTotalTab = Math.floor(this.tabSequences.length/10); //alors on ne fait rien
			}
			else {
				nbTotalTab = Math.floor(this.tabSequences.length/10)+1; //sinon on ajoute une tab pour afficher les dernière séquences
			}
			for(t=1; t<=nbTotalTab; t++) { //on créer les tab (liste des pages)
				this.createTab(t);
			}
			this.remplissageSequences(0);
    },
	
	changeTab: function(inSender, inEvent){
			nbFirstSequence = (inSender.content-1)*10; //on recupere l'id de la tab, -1 car on ne commence pas à 0
			//exemple pour la premiere page : inSender.content --> 1, 1-1*10=0
			//exemple pour la seconde page : inSender.content --> 1, (2-1)*10=10
			//3 ieme --> 20, 4 ieme --> 30...
			this.deleteTbody(); //suppression du corps du tableau
			this.remplissageSequences(nbFirstSequence);
    },
	
	//Remplissage des séquences
	remplissageSequences: function(startSequence){
		this.createTbody(); //on créer un tobdy (corps du tableau) associé à la table pour lui insérer les séquences
			for(s=0; s<10; s++) { //on remplit le tbody de la tab cliquée avec les séquences corresponantes
				nbId = startSequence+s;
				if (typeof (this.tabSequences[nbId]) != 'undefined') {
					this.titre = this.tabSequences[nbId].titre;	
					this.newtitre = (this.titre.length > 100)?this.titre.substring(0,97)+"...":this.titre;
					this.createSequence(this.tabSequences[nbId].id, this.newtitre, this.tabSequences[nbId].type);
				}
			}
			this.render();
	},
	
	//Changement du type 'notVerified', 'verified', 'help'
	setType: function(type)
	{
		//On cherche la séquence courante (dans tabSequences)
		for (i in this.tabSequences)
		{
			if(this.tabSequences[i].id ==this.idSequenceCourante)
			{
				//Changement du type de la séquence
				this.tabSequences[i].type=type;
				
				var status;
				if(type=='notVerified') {status = 'error';}
				else if(type=='verified')  {status = 'success';}
				else if(type=='help')  {status = 'warning';}		
		
				//On récupère la ligne avec l'id de la séquence courante
				var ligne = document.getElementById('app_sequences_'+this.tabSequences[i].id);
				ligne.className = status;
			}
		}
	}
});

enyo.kind({
	name:"Sequence",
	published:{
		id : null,
		titre : null,
		debut : null,
		fin : null,
		type : null
	},
	create: function(){
		this.inherited(arguments);
	}
});
