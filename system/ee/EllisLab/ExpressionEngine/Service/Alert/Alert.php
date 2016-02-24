<?php
namespace EllisLab\ExpressionEngine\Service\Alert;

use Serializable;
use BadMethodCallException;
use InvalidArgumentException;
use EllisLab\ExpressionEngine\Service\View\View;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Alert Class
 *
 * @package		ExpressionEngine
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Alert {

	/**
	 * @var string $title The title of the alert
	 */
	public $title;

	/**
	 * @var string $body The body/content of the alert
	 */
	public $body = '';

	/**
	 * @var bool $has_close_button Flag for rendering a close button
	 */
	protected $has_close_button = FALSE;

	/**
	 * @var string $name The name of the alert, used for identity
	 */
	protected $name;

	/**
	 * @var string $severity The severity of the alert (issue, warn, success)
	 */
	protected $severity;

	/**
	 * @var Alert $sub_alert A sub alert to render inside the body of this alert
	 */
	protected $sub_alert;

	/**
	 * @var string $type The type of alert (standard, inline, banner)
	 */
	protected $type;

	/**
	 * @var AlertCollection $collection A collection of alerts for use with
	 *  deferring or immediately displaying alerts
	 */
	private $collection;

	/**
	 * @var View $view A View object for rendering this alert
	 */
	private $view;

	/**
	 * Constructor: sets the type and name of the alert, and injects the
	 * AllertCollection and View dependencies.
	 *
	 * @param string $type The type of alert (standard, inline, banner)
	 * @param string $name The name of the alert
	 * @param AlertCollection $collection A collection of alerts for use with
	 *  deferring or immediately displaying alerts
	 * @param View $view A View object for rendering this alert
	 * @return self This returns a reference to itself
	 */
	public function __construct($type = 'standard', $name = '', AlertCollection $collection, View $view)
	{
		$this->type = $type;
		$this->name = $name;
		$this->collection = $collection;
		$this->view = $view;
		return $this;
	}

	/**
	 * Allows for read-only access to our protected and private properties
	 *
	 * @throws InvalidArgumentException If the named property does not exist
	 * @param string $name The name of the property
	 * @return mixed The value of the requested property
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}

		throw new InvalidArgumentException("No such property: '{$name}' on ".get_called_class());
	}

	/**
	 * Adds content to the body of the alert.
	 *
	 * @param string|array $item The item to display. If it's an array it will
	 *  be rendred as a list.
	 * @param string $class An optional CSS class to add to the item
	 * @return self This returns a reference to itself
	 */
	public function addToBody($item, $class = NULL)
	{
		if ($class)
		{
			$class = ' class="' . $class . '"';
		}

		if (is_array($item))
		{
			$this->body .= '<ul>';
			foreach ($item as $i)
			{
				$this->body .= '<li>' . $i . '</li>';
			}
			$this->body .= '</ul>';
		}
		else
		{
			$this->body .= '<p' . $class . '>' . $item . '</p>';
		}
		return $this;
	}

	/**
	 * Adds a separator to the body of the alert.
	 *
	 * @return self This returns a reference to itself
	 */
	public function addSeparator()
	{
		$this->body .= '<hr>';
		return $this;
	}

	/**
	 * Marks the alert as an issue alert.
	 *
	 * @return self This returns a reference to itself
	 */
	public function asIssue()
	{
		$this->severity = 'issue';
		$this->cannotClose();
		return $this;
	}

	/**
	 * Marks the alert as a success alert.
	 *
	 * @return self This returns a reference to itself
	 */
	public function asSuccess()
	{
		$this->severity = 'success';
		$this->canClose();
		return $this;
	}

	/**
	 * Marks the alert as a warning alert.
	 *
	 * @return self This returns a reference to itself
	 */
	public function asWarning()
	{
		$this->severity = 'warn';
		$this->canClose();
		return $this;
	}

	/**
	 * Sets the title of the alert.
	 *
	 * @param string $title The title of the alert
	 * @return self This returns a reference to itself
	 */
	public function withTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Allows the alert to be closed by rendering a close icon.
	 *
	 * @return self This returns a reference to itself
	 */
	public function canClose()
	{
		$this->has_close_button = TRUE;
		return $this;
	}

	/**
	 * Does not render a close icon in the alert.
	 *
	 * @return self This returns a reference to itself
	 */
	public function cannotClose()
	{
		$this->has_close_button = FALSE;
		return $this;
	}

	/**
	 * Adds an alert to the alert to be rendered in the body.
	 *
	 * @param Alert $alert An alert to add to the body
	 * @return self This returns a reference to itself
	 */
	public function setSubAlert(Alert $alert)
	{
		$this->sub_alert = $alert;
		return $this;
	}

	/**
	 * Renders the alert to HTML.
	 *
	 * @return string The rendered HTML of the alert
	 */
	public function render()
	{
		return $this->view->render(array('alert' => $this));
	}

	/**
	 * Defers rendering and displaying of the alert until the next CP request.
	 *
	 * @return self This returns a reference to itself
	 */
	public function defer()
	{
		$this->collection->defer($this);
		return $this;
	}

	/**
	 * Saves the alert to be rendered and displayed during this request.
	 *
	 * @return self This returns a reference to itself
	 */
	public function now()
	{
		$this->collection->save($this);
		return $this;
	}
}
// EOF
