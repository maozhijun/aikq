
function setPage(){
	var data = [];
	$('#LatinWords a').each(function(){
		var arr = {
			name: $(this).html(),
			level: $(this).attr('level'),
			href: $(this).attr('href')
		};
		data = data.concat(arr)
	})
	
	var string_ = "";
	for (var i = 0; i < data.length; i++) {
		var string_f = data[i].name;
		var string_n = data[i].level;
		var string_l = data[i].href;
		string_ += "{text: '" + string_f + "', weight: '" + string_n + "', link: '" + string_l + "', html: {'class': 'span_list'}},";
	}

	var string_list = string_;
	var word_list = eval("[" + string_list + "]");
	
	$("#LatinWords_in").html('');

	$("#LatinWords_in").jQCloud(word_list);
}







