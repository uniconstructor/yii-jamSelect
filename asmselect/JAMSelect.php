﻿<?php
/**
 * JAMSelect class file.
 *
 * @author Stefan Volkmar <volkmar_yii@email.de>
 * @license BSD
 */

/**
 * A widget that encapsulates the jQuery amselect plugin for multiple selects.
 *
 * @author Stefan Volkmar <volkmar_yii@email.de>
 * @version: 1.0
 *
 * @see: http://www.ryancramer.com/projects/asmselect/
 */

class JAMSelect extends CInputWidget
{
	/**
	 * @var string the chain of method calls that would be appended at
     * the end of the amselect constructor.
	 */
	public $methodChain;
	/**
	 * @var mixed the CSS file used for the widget. Defaults to null, meaning
	 * using the default CSS file included together with the widget.
	 * If false, no CSS file will be used. Otherwise, the specified CSS file
	 * will be included when using this widget.
	 */
    public $cssFile=false;
	/**
	 * @var string Ordered list 'ol', or unordered list 'ul'
	 * Defaults to 'ol'.
	 */
    public $listType;
	/**
	 * @var boolean Should the list be sortable?
	 * Defaults to false.
	 */
    public $sortable;
	/**
	 * @var boolean Use the highlight feature?
	 * Defaults to false.
	 */
    public $highlight;
	/**
	 * @var boolean Animate the the adding/removing of items in the list?
	 * Defaults to false.
	 */
    public $animate;
	/**
	 * @var string Where to place new selected items in list: top or bottom
	 * Defaults to 'bottom'.
	 */
    public $addItemTarget;
	/**
	 * @var boolean Hide the option when added to the list? works only in FF
	 * Defaults to false.
	 */
    public $hideWhenAdded;
	/**
	 * @var boolean Debug mode keeps original select visible
	 * Defaults to false.
	 */
    public $debugMode;
	/**
	 * @var string Text used in the "remove" link
	 * Defaults to 'remove'.
	 */
    public $removeLabel;
	/**
	 * @var string Text that precedes highlight of added item
	 * Defaults to 'Added: '.
	 */
    public $highlightAddedLabel;
	/**
	 * @var string Text that precedes highlight of removed item
	 * Defaults to 'Removed: '.
	 */
    public $highlightRemovedLabel;
	/**
	 * @var string Class for container that wraps this widget
	 * Defaults to 'asmContainer'.
	 */
    public $containerClass;
	/**
	 * @var string Class for the newly created <select>
	 * Defaults to 'asmSelect'.
	 */
    public $selectClass;
	/**
	 * @var string Class for items that are already selected / disabled
	 * Defaults to 'asmOptionDisabled'.
	 */
    public $optionDisabledClass;
	/**
	 * @var string Class for the list ($ol)
	 * Defaults to 'asmList'.
	 */
    public $listClass;
	/**
	 * @var string Another class given to the list when it is sortable
	 * Defaults to 'asmListSortable'.
	 */
    public $listSortableClass;
	/**
	 * @var string Class for the <li> list items
	 * Defaults to 'asmListItem'.
	 */
    public $listItemClass;
	/**
	 * @var string Class for the label text that appears in list items
	 * Defaults to 'asmListItemLabel'.
	 */
    public $listItemLabelClass;
	/**
	 * @var string Class given to the "remove" link
	 * Defaults to 'asmListItemRemove'.
	 */
    public $removeClass;
	/**
	 * @var string Class given to the highlight <span>
	 * Defaults to 'asmHighlight'.
	 */
    public $highlightClass;
   /**
    * The Data
    *
    * @var array
    */
	private $_data = array();

   /**
    * The selected option tags
	* (when no model/attribute is
	*  passed to the widget)
    *
    * @var array
    */
	private $_selected = array();

    protected $baseUrl;
    protected $options = array();
    protected $cs;
    protected $id;
    protected $widgetName;

    // read and writeable properties
	public function setData($data)
	{
		if(!is_array($data))
            throw new CException(Yii::t(get_class($this),
                    'Invalid type. Property "data" must be an array.'));
			
		$this->_data = $data;
	}

	public function getData()
	{
		return $this->_data;
	}

	public function setSelected($selected)
	{
		if(!is_array($selected))
            throw new CException(Yii::t(get_class($this),
                    'Invalid type. Property "selected" must be an array.'));

		$this->_selected = $selected;
	}

	public function getSelected()
	{
		return $this->_selected;
	}
    
	/**
	 * Initializes the widget.
	 */
	public function init()
	{		
		list($this->widgetName,$this->id)=$this->resolveNameID();
		if(isset($this->htmlOptions['id']))
			$this->id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$this->id;

		if(isset($this->htmlOptions['name']))
			$this->widgetName=$this->htmlOptions['name'];
		else
			$this->htmlOptions['name']=$this->widgetName;
      	
      	$this->baseUrl = CHtml::asset(dirname(__FILE__).'/assets');

  		$this->cs = Yii::app()->getClientScript();
		$this->cs->registerCoreScript('jquery');
        $this->cs->registerCoreScript('jqueryui');
        $this->cs->registerScriptFile($this->baseUrl.'/js/jquery.asmselect.js');

		if($this->cssFile!==false)
			$this->cs->registerCssFile($this->cssFile);
        else
            $this->cs->registerCssFile($this->baseUrl.'/css/jquery.asmselect.css');

        parent::init();
	}

	/**
	 * Executes the widget.
	 */
    public function run()
    {
        $js = $this->createJsCode();
        $this->cs->registerScript(__CLASS__.'#'.$this->id, $js, CClientScript::POS_READY);
        $this->renderMarkup();
    }

    /**
     * The javascript needed
     */
    protected function createJsCode()
    {
        $opts = CJavaScript::encode($this->getClientOptions());
        if($this->methodChain!==null)
            $js = "jQuery('#{$this->id}').asmSelect($opts).{$this->methodChain};";
        else
            $js = "jQuery('#{$this->id}').asmSelect($opts);";
        return $js;
    }

	/**
	 * @return array the javascript options
	 */
	protected function getClientOptions()
	{
	    $options = array();

		static $properties=array(
			'listType', 'sortable', 'highlight',
            'animate', 'addItemTarget', 'hideWhenAdded','debugMode',
            'removeLabel', 'selectClass', 'optionDisabledClass',
            'containerClass', 'listClass', 'listSortableClass',
            'listItemClass', 'listItemLabelClass', 'removeClass',
            'highlightClass');

		foreach($properties as $property)
		{
			if($this->$property!==null)
				$options[$property]=$this->$property;
		}
		return $options;
	}

    protected function renderMarkup(){

		$this->htmlOptions['multiple'] = "multiple";

		$html = ($this->hasModel())
		? CHtml::activeDropDownList($this->model, $this->attribute.'[]', $this->_data, $this->htmlOptions)
		: CHtml::dropDownList($this->widgetName.'[]', $this->_selected, $this->_data, $this->htmlOptions);
		echo $html;
    }
}