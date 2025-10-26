<% if ($objTable->TernaryColumns && count($objTable->TernaryColumns) > 0) { %>

		/////////////////////////
		// TERNARY COLUMN CONSTANTS
		/////////////////////////
<% foreach ($objTable->TernaryColumns as $arrTernary) { %>

		// <%= $arrTernary['property'] %> Mode Constants
		const <%= $arrTernary['property'] %>_Mode_<%= $arrTernary['trueToken'] %> = true;
		const <%= $arrTernary['property'] %>_Mode_<%= $arrTernary['falseToken'] %> = false;
		const <%= $arrTernary['property'] %>_Mode_<%= $arrTernary['nullToken'] %> = null;

		const <%= $arrTernary['property'] %>_Token_<%= $arrTernary['trueToken'] %> = '<%= $arrTernary['trueToken'] %>';
		const <%= $arrTernary['property'] %>_Token_<%= $arrTernary['falseToken'] %> = '<%= $arrTernary['falseToken'] %>';
		const <%= $arrTernary['property'] %>_Token_<%= $arrTernary['nullToken'] %> = '<%= $arrTernary['nullToken'] %>';
<% } %>
<% } %>
