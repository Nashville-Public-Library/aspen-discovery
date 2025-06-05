{strip}
{if !empty($lightbox)}
<div onmouseup="this.style.cursor='default';" id="popupboxHeader" class="header">
	<a onclick="AspenDiscovery.closeLightbox(); return false;" href="">{translate text="close" isPublicFacing=true}</a>
	{translate text='Title Citation' isPublicFacing=true}
</div>
<div id="popupboxContent" class="content">
{/if}
{if $citationCount < 1}
	{translate text="No citations are available for this record" isPublicFacing=true}.
{else}
	<div style="text-align: left;">
		{if false && $ama}
			<b>{translate text="AMA Citation" isPublicFacing=true}</b>
			<p style="width: 95%; padding-left: 25px; text-indent: -25px;">
				{include file=$ama}
			</p>
		{/if}

		{if !empty($apa)}
			<b>{translate text="APA Citation, 7th Edition" isPublicFacing=true}</b> {if !empty($showCitationStyleGuides)}<span class="styleGuide"><a href="https://owl.purdue.edu/owl/research_and_citation/apa_style/apa_formatting_and_style_guide/reference_list_books.html" target="_blank">({translate text="Style Guide" isPublicFacing=true})</a></span>{/if}
			<p style="width: 95%; padding-left: 25px; text-indent: -25px;">
				{include file=$apa}
			</p>
		{/if}

		{if !empty($chicagoauthdate)}
			<b>{translate text="Chicago / Turabian - Author Date Citation, 18th Edition" isPublicFacing=true}</b> {if !empty($showCitationStyleGuides)}<span class="styleGuide"><a href="https://www.chicagomanualofstyle.org/tools_citationguide/citation-guide-2.html" target="_blank">({translate text="Style Guide" isPublicFacing=true})</a></span>{/if}
			<p style="width: 95%; padding-left: 25px; text-indent: -25px;">
				{include file=$chicagoauthdate}
			</p>
		{/if}

		{if !empty($chicagohumanities)}
			<b>{translate text="Chicago / Turabian - Humanities (Notes and Bibliography) Citation, 18th Edition" isPublicFacing=true}</b> {if !empty($showCitationStyleGuides)}<span class="styleGuide"><a href="https://www.chicagomanualofstyle.org/tools_citationguide/citation-guide-1.html" target="_blank">({translate text="Style Guide" isPublicFacing=true})</a></span>{/if}
			<p style="width: 95%; padding-left: 25px; text-indent: -25px;">
				{include file=$chicagohumanities}
			</p>
		{/if}

		{if !empty($harvard)}
			<b>{translate text="UCL Harvard Citation" isPublicFacing=true}</b> {if !empty($showCitationStyleGuides)}<span class="styleGuide"><a href="https://library-guides.ucl.ac.uk/harvard/a-z" target="_blank" aria-label="{translate text='Style Guide' isPublicFacing=true} ({translate text='opens in new window' isPublicFacing=true})">({translate text="Style Guide" isPublicFacing=true})</a></span>{/if}
			<p style="width: 95%; padding-left: 25px; text-indent: -25px;">
				{include file=$harvard}
			</p>
		{/if}

		{if !empty($mla)}
			<b>{translate text="MLA Citation, 9th Edition" isPublicFacing=true}</b> {if !empty($showCitationStyleGuides)}<span class="styleGuide"><a href="https://owl.purdue.edu/owl/research_and_citation/mla_style/mla_formatting_and_style_guide/mla_formatting_and_style_guide.html" target="_blank">({translate text="Style Guide" isPublicFacing=true})</a></span>{/if}
			<p style="width: 95%; padding-left: 25px; text-indent: -25px;">
				{include file=$mla}
			</p>
		{/if}

	</div>
	<div class="alert alert-info">
		<strong>{translate text="Note:" isPublicFacing=true}</strong> {translate text="Citations contain only title, author, edition, and publisher. Only UCL Harvard citations contain the year published. Citations should be used as a guideline and should be double checked for accuracy. Citation formats are based on standards as of May 2025." isPublicFacing=true}
	</div>
{/if}
{if !empty($lightbox)}
</div>
{/if}
{/strip}