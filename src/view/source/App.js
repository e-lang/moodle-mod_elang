enyo.kind({
	name : "App",
	classes: "container-fluid",
	components:[
		{tag: "div", name: "header", classes:"row-fluid", components:[
			{tag: "div", name: "span12", classes:"span12", components:[
				{tag: "div", classes:"well well-small", components:[
					{tag: "h1", content: "Vidéo"}
				]}
			]}
		]},
		{tag: "div", name: "body", classes:"row-fluid", components:[
			{tag: "div", name: "span6", classes:"span6", components:[
			//Video
			{kind: "Video", name : "video"},
			]},
			{tag: "div", name: "span6", classes:"span6", components:[
				{tag: "h1", content: "Liste."}
			]}
		]},
		{tag: "div", name: "footer", classes:"row-fluid", components:[
			{tag: "div", name: "span12", classes:"span12", components:[
				{tag: "h1", content: "footer."}
			]}
		]}
	]
});

