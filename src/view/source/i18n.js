$L = function (string) {
	if (typeof Elang.strings == 'undefined' || typeof Elang.strings[string] === 'undefined')
	{
		return string;
	}
	else
	{
		return Elang.strings[string];
	}
};

