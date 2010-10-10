<% require javascript(ecommerce_vote/javascript/EcommerceVote.js) %>
<% require themedCSS(EcommerceVote) %>
<div class="addToEcommerceVote">
<% if HasEcommerceVote %>
	<span>voted</span>
<% else %>
	<a href="{$Link}addecommercevote/$ID/" class="addEcommerceVote">vote</a>
<% end_if %>
</div>