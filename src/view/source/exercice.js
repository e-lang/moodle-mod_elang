window.oncomplete = init();

function init() {
	enyo.kind({
		name: "exercice",
		kind: enyo.Control,
		tag: 'div',
        
		//Call at first
	    create: function() {
	    	this.inherited(arguments);	
	    	this.getReponseExo(); //Display the content of the exercise with inputs for blanks
	    	this.createComponent ( { kind: "enyo.Button", content: "Correct", ontap: "corriger" 	} );
	    	this.createComponent ( { kind: "enyo.Button", content: "Show errors", ontap: "error" 	} );
	    },
	
	    //Call when tap on the button "Corriger"
	    //display the correct words into the inputs
	    //Send a response to the server (true or false)
	    corriger: function() {
	    	var request = new enyo.Ajax({
	    		url: "tabCorrectAnswers.php", //URL
	    		method: "GET", //options are "GET" or "POST"
	    		handleAs: "text", //options are "json", "text", or "xml"
	    	});	

        request.response(enyo.bind(this, "processResponseCorrect")); //tells Ajax what the callback function is
        request.go({e:this.getNumExoFromURL()}); //makes the Ajax call with parameters
	    },
	    
	    processResponseCorrect: function(inRequest, inResponse) {
	        if (!inResponse) { //if there is nothing in the response then return early.
	        	alert('There is a problem when i try to get the corrects answers, please try again later...');
	        	return;
	        }
	        var reponse = enyo.json.parse(inResponse);
	        for (var k=0; k<reponse.length; k++) { //k is index of input too (same as j)
	        	var input = enyo.dom.byId("exercice_input_"+k);
	        	if(input.value == reponse[k]) {
	        		input.className = "enyo-input texteGreen";
	        	}
	        	else {
	        		input.value = reponse[k];
	        		input.className = "enyo-input texteRed";
	        	}
	        	
	        }
	        
	        var request = new enyo.Ajax({
	    		url: "returnResponseCorrect.php", //URL
	    		method: "GET", //options are "GET" or "POST"
	    		handleAs: "text", //options are "json", "text", or "xml"
	    	});	
	        //Say to the bdd if the student cliqued on the "Correct" button
	        request.go({r:true}); //makes the Ajax call with parameters
	      },
	    
	    //Call when tap on the button "Afficher Erreurs"
	    error: function() {
			var strRequete = "";			
			var countInput = document.getElementsByTagName("input").length;
			
			for(var i=0; i<countInput; i++) {
				var input = enyo.dom.byId("exercice_input_"+i);
				if(input.value == "" || input.value == " ") {
					strRequete = strRequete + " " + "_";
				}
				else { strRequete = strRequete + input.value + "_"; }
			}
			var request = new enyo.Ajax({
		    		url: "tabCompareAnswers.php", //URL
		    		method: "GET", //options are "GET" or "POST"
		    		handleAs: "text", //options are "json", "text", or "xml"
		    	});	

	        request.response(enyo.bind(this, "processResponseError")); //tells Ajax what the callback function is
	        request.go({e:this.getNumExoFromURL(), a:strRequete}); //makes the Ajax call with parameters
	    },
	      
	    //call at the ajax request
	      processResponseError: function(inRequest, inResponse) {
	        if (!inResponse) { //if there is nothing in the response then return early.
	        	alert('There is a problem when i try to get the result, please try again later...');
	        	return;
	        }
	        var reponse = enyo.json.parse(inResponse);;
	        for (var k=0; k<reponse.length; k++) { //k is index of input too (same as i)
	        	var input = enyo.dom.byId("exercice_input_"+k);
	        	if(reponse[k] == "true") {
	        		input.setAttribute("class", "enyo-input texteGreen");
	        	}
	        	else {
	        		input.setAttribute("class", "enyo-input texteRed");
	        	}
	        }
	      },	    
		
		//Return the id of exercise
		getNumExoFromURL: function() {
			//var URL = document.URL;
			return 1;
		},
		
		inputChange: function(inSender, inEvent) {
			inSender.setAttribute("class", "enyo-input inputDefault");
		},
		
		//Return a string that represents the answer of the exercise
		getReponseExo: function () {
			var request = new enyo.Ajax({
	    		url: "getExerciseText.php", //URL
	    		method: "GET", //options are "GET" or "POST"
	    		handleAs: "text", //options are "json", "text", or "xml"
	    		sync: true,
	    	});	

			request.response(enyo.bind(this, "processResponseExo")); //tells Ajax what the callback function is
			request.go({e:this.getNumExoFromURL()}); //makes the Ajax call with parameters
		},
		
		processResponseExo: function(inRequest, inResponse) {
			if (!inResponse) { //if there is nothing in the response then alert and return early.
	        	alert('There is a problem when i try to get the content, please try again later...');
	        	return;
	        }
			else {
				var i = 1; //for json
				var j = 0; //id of inputs
				
				var texte = enyo.json.parse(inResponse);
				for(key in texte) { //count of sequences
					var mots = texte[key][i].split(' '); //texte[key][i] is a sequence
					for(var l=0; l<mots.length; l++) { //for each word
						if(mots[l].indexOf("[") !== -1 && mots[l].indexOf("]") !== -1) {
							var input = this.createComponent( { kind: "enyo.Input",
													name: "input_"+j,
													style: "width:" + mots[l].length*7 + "px",
													onchange: "inputChange",
													selectOnFocus: true } );
							input.setAttribute("seq", i);
							j = j + 1;
						}
						else {
							var span = this.createComponent( { tag: "span",
													name: "texte_"+ i + "_" + l,
													content: mots[l] + " " } );
							span.setAttribute("seq", i);
						}
					}
					i = i + 1;
				}
			}
		}
	});
	
	var exo = new exercice();	
	exo.renderInto(document.body);
}
