<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Breadcrumbs displays a list of links indicating the position of the current page in the whole site hierarchy.
 *
 * For example, breadcrumbs like "Home / Sample Post / Edit" means the user is viewing an edit page
 * for the "Sample Post". He can click on "Sample Post" to view that page, or he can click on "Home"
 * to return to the homepage.
 *
 * To use Breadcrumbs, you need to configure its [[links]] property, which specifiesthe links to be displayed. For example,
 *
 * ~~~
 * $this->widget('yii\widgets\Breadcrumbs', array(
 *     'links' => array(
 *         array('label' => 'Sample Post', 'url' => array('post/edit', 'id' => 1)),
 *         'Edit',
 *     ),
 * ));
 * ~~~
 *
 * Because breadcrumbs usually appears in nearly every page of a website, you may consider place it in a layout view.
 * You can then use a view parameter (e.g. `$this->params['breadcrumbs']`) to configure the links in different
 * views. In the layout view, you assign this view parameter to the [[links]] property like the following:
 *
 * ~~~
 * $this->widget('yii\widgets\Breadcrumbs', array(
 *     'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : array(),
 * ));
 * ~~~
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Breadcrumbs extends Widget
{
	/**
	 * @var array the HTML attributes for the breadcrumb container tag. The "tag" element is
	 * specially handled which specifies the tag name of the container element. If not set, it will default to "ul".
	 */
	public $options = array('tag' => 'ul', 'class' => 'breadcrumb');
	/**
	 * @var boolean whether to HTML-encode the link labels.
	 */
	public $encodeLabels = true;
	/**
	 * @var string the first hyperlink in the breadcrumbs (called home link).
	 * If this property is not set, it will default to a link pointing to [[\yii\web\Application::homeUrl]]
	 * with the label 'Home'. If this property is false, the home link will not be rendered.
	 */
	public $homeLink;
	/**
	 * @var array list of links to appear in the breadcrumbs. If this property is empty,
	 * the widget will not render anything. Each array element represents a single link in the breadcrumbs
	 * with the following structure:
	 *
	 * ~~~
	 * array(
	 *     'label' => 'label of the link',  // required
	 *     'url' => 'url of the link',      // optional, will be processed by Html::url()
	 * )
	 * ~~~
	 *
	 * If a link is active, you only need to specify its "label", and instead of writing `array('label' => $label)`,
	 * you should simply use `$label`.
	 */
	public $links = array();
	/**
	 * @var string the template used to render each inactive item in the breadcrumbs. The token `{link}`
	 * will be replaced with the actual HTML link for each inactive item.
	 */
	public $itemTemplate = "<li>{link} <span class=\"divider\">/</span></li>\n";
	/**
	 * @var string the template used to render each active item in the breadcrumbs. The token `{link}`
	 * will be replaced with the actual HTML link for each active item.
	 */
	public $activeItemTemplate = "<li class=\"active\">{link}</li>\n";

	/**
	 * Renders the widget.
	 */
	public function run()
	{
		if (empty($this->links)) {
			return;
		}
		$links = array();
		if ($this->homeLink === null) {
			$links[] = strtr($this->itemTemplate, array('{link}' => Html::a(Yii::t('yii|Home'), Yii::$app->homeUrl)));
		} elseif ($this->homeLink !== false) {
			$links[] = strtr($this->itemTemplate, array('{link}' => $this->homeLink));
		}
		foreach ($this->links as $link) {
			if (!is_array($link)) {
				$link = array('label' => $link);
			}
			if (isset($link['label'])) {
				$label = $this->encodeLabels ? Html::encode($link['label']) : $link['label'];
			} else {
				throw new InvalidConfigException('The "label" element is required for each link.');
			}
			if (isset($link['url'])) {
				$links[] = strtr($this->itemTemplate, array('{link}' => Html::a($label, $link['url'])));
			} else {
				$links[] = strtr($this->activeItemTemplate, array('{link}' => $label));
			}
		}
		$tagName = isset($this->options['tag']) ? $this->options['tag'] : 'ul';
		unset($this->options['tag']);
		echo Html::tag($tagName, implode('', $links), $this->options);
	}
}