AspenDiscovery.Wikipedia = (function(){
	return{
		getWikipediaArticle: function(articleName){
			const url = Globals.path + "/Author/AJAX?method=getWikipediaData&articleName=" + articleName;
			$.getJSON(url, function(data){
				if (data.success) {
					$("#wikipedia_placeholder").html(data.formatted_article).fadeIn();
				} else if (Globals.ipDebugEnabled) {
					$("#wikipedia_placeholder").append(
						'<div ' + 'class="smallText text-muted" style="font-style:italic">' +
						'Wikipedia search for "' + data.searchedName + '" returned no result (' + (data.error || 'unknown') + ').' +
						'Consider using Wikipedia Integration (Author Enrichment) to correct the Wikipedia search or to prevent Wikipedia searching for this author.' +
						'</div>'
					).fadeIn();
				}
			});
		}
	};
}(AspenDiscovery.Wikipedia));