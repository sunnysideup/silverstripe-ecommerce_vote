<% if EcommerceVoteTopFive %>
<ol id="EcommerceVoteTopFive">
	<% control EcommerceVoteTopFive %>
	<li><a href="$Link">$Title ($EcommerceVotes)</a><li>
	<% end_control %>
</ol>
<% end_if %>