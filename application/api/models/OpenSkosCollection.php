<?php


class Api_Models_OpenSkosCollection extends Api_Models_Concepts {
	
	protected $_queryParameters = array();
	
	/**
	 * @return Editor_Models_Concepts
	 */
	public static function factory()
	{
		return new Api_Models_OpenSkosCollection();
	}
	
	public function getQueryParam($key, $default = null)
	{
		return isset($this->_queryParameters[$key])
		? $this->_queryParameters[$key]
		: $default;
	}
	
	public function setQueryParam($key, $value)
	{
		$this->_queryParameters[$key] = $value;
		return $this;
	}
	
	public function setQueryParams(Array $parameters)
	{
		$this->_queryParameters += $parameters;
		return $this;
	}
	
	public function getConcepts($q, $includeDeleted = false, $forAutocomplete = false)
	{
		$solr = $this->solr();
		if(true === (bool)ini_get('magic_quotes_gpc')) {
			$q = stripslashes($q);
		}
		if (null !== ($lang = $this->lang)) {
			$solr->setLang($lang);
		}
	
		//if user request fields, make sure that some fields are always included:
		if (isset($this->_queryParameters['fl'])) {
			$this->_queryParameters['fl'] .= ',xmlns,xml';
		}
	
		$params = array('wt' => $this->format === 'xml' ? 'xml' : 'phps') + $this->_queryParameters;
	
		if (isset($this->_queryParameters['lang'])) {
			$q='LexicalLabelsText@'.$lang.':('.$q.')';
		}
	
		//only return non-deleted items:
		if (false === $includeDeleted) {
			$q = "($q) AND deleted:false";
		}
	
		if (true === $forAutocomplete) {
			$labelReturnField = $this->_getLabelReturnField();
			$params = $params + array(
					'facet' => 'true',
					'facet.field' => $labelReturnField,
					'fq' => $q,
					'facet.mincount' => 1
			);
			$response = $this->solr()
			->setFields(array('uuid', $labelReturnField))
			->limit(0,0)
			->search($q, $params);
			$this->solr()->setFields(array());
			$labels = array();
			foreach ($response['facet_counts']['facet_fields'][$labelReturnField] as $label => $count) {
				$labels[] = $label;
			}
			return $labels;
		}
	
		return $solr->search($q, $params);
	}
	
	/**
	 * @return OpenSKOS_Solr
	 */
	protected function solr()
	{
		return Zend_Registry::get('OpenSKOS_Solr');
	}
}