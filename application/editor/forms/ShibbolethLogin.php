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
 * @copyright  Copyright (c) 2014 Meertens Instituut. (http://www.meertens.knaw.nl)
 * @author     Matthijs Brouwer
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 */

class Editor_Forms_ShibbolethLogin extends Zend_Form
{
	public function init()
	{
		
	}
	
	
	public function checkShibbolethEnabled() {
		$shibbolethOptions = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('shibboleth');
		if(!$shibbolethOptions || !$shibbolethOptions['enabled']) {
			return false;
		} else if(!isset($shibbolethOptions['authentication']) || !$shibbolethOptions['authentication']['eppn'] || !is_string($shibbolethOptions['authentication']['eppn'])) {
			return false;
		} else {
		  return true;
		}  
	}
	
	public function checkShibbolethForm() {
		if(!$this->checkShibbolethEnabled()) {
		  return false;
	  } else {
	    $shibbolethOptions = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('shibboleth');
			if(!isset($shibbolethOptions['form']) || !isset($shibbolethOptions['form']['enabled']) || !$shibbolethOptions['form']['enabled']) {
				return false;
			} else {
				return true;
			}
	  }		
	}
	
	public function getShibbolethEppn() {
		if(!$this->checkShibbolethEnabled()) {
		  return false;
	  } else {
	    $shibbolethOptions = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('shibboleth');
			if(!isset($_SERVER[$shibbolethOptions['authentication']['eppn']])) {
				return false;
			} else if(!is_string($_SERVER[$shibbolethOptions['authentication']['eppn']]) || !$_SERVER[$shibbolethOptions['authentication']['eppn']]) {
				return false;
			} else {	
				$eppn = $_SERVER[$shibbolethOptions['authentication']['eppn']];	
				if(preg_match("/;/",$eppn)) {
					$list = explode(";",$eppn);
					$eppn = "";
					foreach($list AS $item) {
						if(strlen($item)>strlen($eppn)) {
							$eppn = $item;
						}
					}					
				}		
				return $eppn;
			}
	  }	
	}
	
	public function getShibbolethName() {
		if(!$this->checkShibbolethEnabled()) {
			return false;
		} else {
			$shibbolethOptions = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('shibboleth');
			if(!isset($shibbolethOptions['authentication']['name'])) {
				return false;
			} else if(!is_string($shibbolethOptions['authentication']['name'])) {
				return false;
			} else {
				return $shibbolethOptions['authentication']['name'];
			}
		}
	}
	
	public function getShibbolethEmail() {
		if(!$this->checkShibbolethForm()) {
			return false;
		} else {
			$shibbolethOptions = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('shibboleth');
			if(!isset($shibbolethOptions['form']['email'])) {
				return false;
			} else if(!is_string($shibbolethOptions['form']['email']) || !$shibbolethOptions['form']['email']) {
				return false;
			} else {
				return $shibbolethOptions['form']['email'];
			}
		}
	}
	
	public function sendShibbolethEmail($eppn, $text) {
		if(!$email = $this->getShibbolethEmail()) {
			return false;
		} else {
			$html = "";
			$html.= "<body style=\"background: #EEEEEE;\">\n";
			$html.= "  <p>\n";
			$html.= "  Hi administrator from ".htmlentities($_SERVER['HTTP_HOST']).",<br />\n";
			$html.= "  <br />\n";
			$html.= "  Someone submitted a request from <strong>".htmlentities($_SERVER['REQUEST_URI'])."</strong> on <strong>".htmlentities($_SERVER['HTTP_HOST'])."</strong> to get access\n";
			if($name = $this->getShibbolethName()) {
				$html.="  using <strong>".htmlentities($name)."</strong>.\n";
			} else {
				$html.="  using shibboleth.\n";
			}
			$html.= "  </p>\n";
			$html.= "  <div style=\"background: #FFFFFF; padding: 10px;border-style: solid; border-width: 5px; border-color: #0000FF; -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px;\">\n";
			$html.= "    <table>\n";
			$html.= "      <tr><td style=\"font-weight: bold; width: 200px;\">EPPN</td><td>".htmlentities($eppn)."</td></tr>\n";
			$html.= "      <tr><td style=\"font-weight: bold; width: 200px;\">Info</td><td>".htmlentities($text)."</td></tr>\n";
			$html.= "    </table>\n";
			$html.= "  </div>\n";
			$html.= "  <br />\n";
			$html.= "  <p>Click <a href=\"".htmlentities($this->getView()->serverUrl().$this->getView()->url(array('controller' => 'login', 'action' => ''), 'default'))."\">here</a> to login and manually add a new user with EPPN '".htmlentities($eppn)."'</p>\n";
			$html.= "  <p>Additional information:</p>\n";
			$html.= "  <div style=\"background:F5F5F5; padding: 10px;border-style: solid; border-width: 5px; border-color: #DDDDDD; -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; border-radius: 5px;\">\n";
			$html.= "    <table>\n";
			foreach($_SERVER AS $key => $value) {
				$html.= "      <tr><td style=\"width: 200px;\">".htmlentities($key)."</td><td>".htmlentities($value)."</td></tr>\n";
			}
			$html.= "    </table>\n";
			$html.= "  </div>\n";
			$html.= "</body>\n";
			$mail = new Zend_Mail();		
			$mail->setBodyText(strip_tags($html));
			$mail->setBodyHtml($html);
			$mail->setFrom('noreply@'.$_SERVER['HTTP_HOST'], 'Visitor '.$eppn);
			$mail->addTo($email, 'Administrator '+$_SERVER['HTTP_HOST']);
			$mail->setSubject('Shibboleth access requested on '.$_SERVER['HTTP_HOST']);
			return $mail->send();
		}		
	}
	
	
	/**
	 * @return Editor_Forms_ShibbolethLogin
	 */
	public static function getInstance()
	{
		static $instance;
	
		if (null === $instance) {
			$instance = new Editor_Forms_ShibbolethLogin();
		}
	
		return $instance;
	}
}