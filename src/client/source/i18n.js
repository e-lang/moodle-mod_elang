/**
 * Internationalisation
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */
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
