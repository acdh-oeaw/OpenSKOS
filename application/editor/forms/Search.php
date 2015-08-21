<?php 
/**
 * OpenSKOS
 *
 * LICENSE
 *
 * This source file is subject to the GPLv3 license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenSKOS
 * @package    OpenSKOS
 * @copyright  Copyright (c) 2012 Pictura Database Publishing. (http://www.pictura-dp.nl)
 * @author     Alexandar Mitsev
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 */

class Editor_Forms_Search extends Zend_Form
{
	/**
	 * @var int
	 */
	const DEFAULT_ROWS_COUNT = 20;
	
	/**
	 * Holds the currently logged user's tenant.
	 *
	 * @var OpenSKOS_Db_Table_Row_Tenant
	 */
	protected $_currentTenant;
	
	public function init() 
	{
		$this->setName('searchform')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller'=>'search', 'action' => 'index')))
		->setMethod('post');

		$this->buildSearchText()
		->buildConceptScheme()
		->buildUser()
		->buildPaginationFields()
		->buildSearchProfiles()
		->buildSearchButton();
	}
	
	protected function buildSearchText()
	{
		$this->addElement('text', 'searchText', array(
            'filters' => array('StringTrim'),
            'label' => '',
        ));
		return $this;
	}
	
	protected function buildTruncate()
	{
		$options = array('right', 'left', 'both');
		$this->addElement('radio', 'truncate', array(
            'label' => _('Truncate'),
            'required' => true,
            'multiOptions' => array_combine($options, $options),
            'value' => 'right'
        ));
		return $this;
	}
	
	protected function buildConceptScheme()
	{	
		$apiClient = new Editor_Models_ApiClient();
		$conceptSchemes = $apiClient->getAllConceptSchemeUriTitlesMap();
		
		$this->addElement('multiCheckbox', 'conceptScheme', array(
            'label' => _('Concept scheme'),
            'multiOptions' => $conceptSchemes
        ));
		return $this;
	}
	
	protected function buildInstantResults()
	{
		$this->addElement('checkbox', 'instantResults', array('label' => _('Instant results')));
        $this->getElement('instantResults')->setChecked(true);
		return $this;
	}
	
	protected function buildUser()
	{
		$user = OpenSKOS_Db_Table_Users::fromIdentity();
		if (null === $user) {
			throw new Zend_Controller_Action_Exception('User not found', 404);
		}
		
		$this->addElement('hidden', 'user', array('value' => $user['id']));
		return $this;
	}
	
	protected function buildPaginationFields()
	{
		$this->addElement('hidden', 'start', array('value' => 0));
		$this->addElement('hidden', 'rows', array('value' => self::DEFAULT_ROWS_COUNT));
		return $this;
	}
	
	/**
	 * @return Editor_Forms_Search
	 */
	protected function buildSearchProfiles()
	{
		$profilesModel = new OpenSKOS_Db_Table_SearchProfiles();
		$profiles = $profilesModel->fetchAll($profilesModel->select()->where('tenant=?', $this->_getCurrentTenant()->code));
		
		$user = OpenSKOS_Db_Table_Users::fromIdentity();
		if (null === $user) {
			throw new Zend_Controller_Action_Exception('User not found', 404);
		}
		
		$profilesOptions = array();		
		$profilesOptions[''] = _('Default');
		foreach ($profiles as $profile) {
			$profilesOptions[$profile->id] = $profile->name;
		}
		$profilesOptions['custom'] = _('Custom');
		
		$userOptions = $user->getSearchOptions();
	
		$this->addElement('select', 'searchProfileId', array(
			'label' => _('Search Profile'),
			'multiOptions' => $profilesOptions,
			'value' => (isset($userOptions['searchProfileId']) ? $userOptions['searchProfileId'] : '')
		));
		
		$this->addDisplayGroup(array('searchProfileId'), 
				'search-profile-selector', 
				array('disableDefaultDecorators'=> true, 
					  'decorators'=> array('FormElements', array('HtmlTag', array('tag' => 'span', 'id' => 'search-profile-selector', 'class' => ($profiles->count() < 2 ? 'do-not-show' : ''))))));
		
		return $this;
	}
	
	protected function buildSearchButton()
	{
		$this->addElement('button', 'searchButton', array(
            'required' => false,
            'ignore' => true,
            'label' => _('Search')
        ));
        return $this;
	}
	
	/**
	 * Gets the currently logged user's tenant.
	 *
	 * @return OpenSKOS_Db_Table_Row_Tenant
	 */
	protected function _getCurrentTenant()
	{
		if ( ! $this->_currentTenant) {
			$this->_currentTenant = OpenSKOS_Db_Table_Tenants::fromIdentity();
			if (null === $this->_currentTenant) {
				throw new Zend_Exception('Tenant not found. Needed for request to the api.');
			}
		}
	
		return $this->_currentTenant;
	}
	
	/**
	 * @return Editor_Forms_Search
	 */
	public static function getInstance()
	{
		static $instance;
		
		if (null === $instance) {
			$instance = new Editor_Forms_Search();
		}
		
		return $instance;
	}
}