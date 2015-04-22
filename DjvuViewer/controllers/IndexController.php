<?php
/**
 * @package DjvuViewer
 * @author Gjergj Sheldija, <gjergj.sheldija@gmail.com>
 * @copyright Copyright 2012-2015 Gjergj Sheldija
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Controller for djvu viewer plugin
 */
class DjvuViewer_IndexController extends Omeka_Controller_AbstractActionController {

	public function showAction() {
		$this->view->filename = $this->getRequest()->getParam('filename');
	}

}
