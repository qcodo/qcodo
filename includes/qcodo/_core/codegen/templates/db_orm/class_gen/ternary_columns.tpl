<% if ($objTable->TernaryColumns && count($objTable->TernaryColumns) > 0) { %>


		/////////////////////////
		// TERNARY COLUMN HELPERS
		/////////////////////////
<% foreach ($objTable->TernaryColumns as $arrTernary) { %>

		/**
		 * Gets the ternary "mode" value based on the <%= $arrTernary['property'] %> schema token
		 * @param string $token
		 * @return boolean|null
		 */
		public static function Get<%= $arrTernary['columnPropertyName'] %>ForToken($token) {
			return match ($token) {
				self::<%= $arrTernary['property'] %>_Token_<%= $arrTernary['trueToken'] %> => self::<%= $arrTernary['property'] %>_Mode_<%= $arrTernary['trueToken'] %>,
				self::<%= $arrTernary['property'] %>_Token_<%= $arrTernary['falseToken'] %> => self::<%= $arrTernary['property'] %>_Mode_<%= $arrTernary['falseToken'] %>,
				self::<%= $arrTernary['property'] %>_Token_<%= $arrTernary['nullToken'] %> => self::<%= $arrTernary['property'] %>_Mode_<%= $arrTernary['nullToken'] %>,
				default => throw new QCallerException('Unhandled <%= $arrTernary['property'] %> Token: ' . $token),
			};
		}

		/**
		 * Gets the <%= $arrTernary['property'] %> schema token based on a ternary "mode" value
		 * @param boolean|null $<%= $arrTernary['columnVariableName'] %>
		 * @return string
		 */
		public static function Get<%= $arrTernary['property'] %>TokenForMode($<%= $arrTernary['columnVariableName'] %>) {
			if ($<%= $arrTernary['columnVariableName'] %> === self::<%= $arrTernary['property'] %>_Mode_<%= $arrTernary['trueToken'] %>) return self::<%= $arrTernary['property'] %>_Token_<%= $arrTernary['trueToken'] %>;
			if ($<%= $arrTernary['columnVariableName'] %> === self::<%= $arrTernary['property'] %>_Mode_<%= $arrTernary['falseToken'] %>) return self::<%= $arrTernary['property'] %>_Token_<%= $arrTernary['falseToken'] %>;
			if ($<%= $arrTernary['columnVariableName'] %> === self::<%= $arrTernary['property'] %>_Mode_<%= $arrTernary['nullToken'] %>) return self::<%= $arrTernary['property'] %>_Token_<%= $arrTernary['nullToken'] %>;
			throw new QCallerException('Unhandled <%= $arrTernary['columnPropertyName'] %> Ternary Mode: ' . $<%= $arrTernary['columnVariableName'] %>);
		}
<% } %>
<% } %>
