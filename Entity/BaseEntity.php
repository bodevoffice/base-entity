<?php
/**
 * Created by PhpStorm.
 * User: erman.titiz
 * Date: 28/09/16
 * Time: 15:40
 */

namespace BiberLtd\Bundle\BaseEntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class BaseEntity
{

    public $date_added;/** @var \DateTime Date when the entry is created. */
    public $date_updated;/** @var \DateTime Date when the entry is updated. */
    public $date_removed;/** @var \DateTime Date when the entry is removed. */
    protected $new = false;/** @var bool Marks the object as new. */
    protected $modified = false;/** @var bool Marks the object as modified or not modified */
    protected $localized = false;/** @var bool Marks the object as localizable. */
    protected $timezone = 'Europe/Istanbul';/** @var string  application timezone */
    private $mainClass;

    /**
     * @name            __construct()
     *                  Initializes entity.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     */
    public function __construct($timezone = 'Europe/Istanbul') {

        $new = true;
        foreach ($this as $key => $value) {
            if ($this->date_added !== NULL ) {
                $new = false;
                break 1;
            }
        }

        $this->timezone = $timezone;
        $this->new = $new;
        $this->modified = false;
        if ($this->new) {
            $this->setDateAdded();
        }
        if(is_null($this->mainClass))
        {
            $this->mainClass = get_class($this);
        }
    }

    /**
     * @name            isLocalized()
     *                  Checks if the object is localizable.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    public function isLocalized() {
        return $this->localized;
    }

    /**
     * @name            isModified()
     *                  Checks if the object is modified.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    public function isModified() {
        return $this->modified;
    }

    /**
     * @name            isNew()
     *                  Checks if this is a new entity.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    public function isNew() {
        return $this->new;
    }

    /**
     * @name            setModified()
     *                  Sets the modified property to true or false.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.1
     *
     * @param           string          $property       Name of property to be checked.
     * @param           mixed           $value          Value to be set.
     *
     * @return          object          $this
     */
    public function setModified($property, $value) {
        $explodedProp = explode('_', $property);
        $ucFirstProp = '';
        foreach ($explodedProp as $prop) {
            $ucFirstProp .= ucfirst($prop);
        }
        $get = 'get' . $ucFirstProp;
        if ($this->$get() !== $value) {
            $this->modified = true;
            $this->setDateUpdated();
        }
        return $this;
    }

    /**
     * @name            getDateAdded()
     *  				Returns the creation date of the history entry.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          \DateTime          $this->date_added
     */
    public function getDateAdded() {
        return $this->date_added;
    }

    /**
     * @name            setDateAdded()
     *                  Sets the object creation date.
     *
     * @author          Can Berkol
     * @since           1.0.1
     * @version         1.0.1
     *
     * @param           string          $timezone
     *
     * @return          object          $this
     */
    public function setDateAdded() {
        $this->date_added = new \DateTime('now', new \DateTimeZone($this->timezone));
        return $this;
    }

    /**
     * @name            setDateRemoved()
     *  				Sets the date when the entry is removed.
     *                  NOTE: Removal means setting Date Removed column to a date. Actual removing will not occur unless
     *                  specifically instructed within the code.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since			1.0.1
     * @version         1.0.1
     *
     * @return          object          $this
     */
    public function setDateRemoved() {
        $this->date_removed = new \DateTime('now', new \DateTimeZone($this->timezone));
        return $this;
    }

    /**
     * @name            getDateRemoved()
     *  				Returns the date when the entry is deleted.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          mixed             null | \DateTime
     */
    public function getDateRemoved() {
        return $this->date_removed;
    }

    /**
     * @name            getDateUpdated()
     *  				Gets the date when the entry is last updated.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          \DateTime          $this->date_updated
     */
    public function getDateUpdated() {
        return $this->date_updated;
    }

    /**
     * @name            setDateUpdated()
     *                  Sets the object update date.
     *
     * @author          Can Berkol
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          object          $this
     */
    public function setDateUpdated() {
        $this->date_updated = new \DateTime('now', new \DateTimeZone($this->timezone));
        return $this;
    }
    private function _isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
    public function toJson($returnArray = false,$mainClass='')
    {
        if(method_exists($this,'serialize'))
        {
            return $this->serialize();
        }
        $reflector = new \ReflectionClass(get_class($this));
        $props = $reflector->getProperties(\ReflectionProperty::IS_PRIVATE);

        if($mainClass!='')
        {
            $this->mainClass=$mainClass;
        }
        $object = [];
        foreach($props as $property)
        {
            $value = $this->variableToFunction($property->getName(),'get',true);
            if($this->_isJson($value))
            {
                $value = (array)json_decode($value,true);

            }elseif(is_array($value))
            {
                $valueData=[];
                foreach($value as $valueRow)
                {
                    if(is_object($valueRow))
                    {
                        if(get_class($valueRow)!="")
                        {
                            if($this->mainClass!=get_class($valueRow))
                            {
                                $valueData[]=$valueRow->toJson(true,$this->mainClass);

                            }else{
                                $valueData[]=$valueRow->variableToFunction('id','get',true);
                            }
                        }else{
                            $valueData[]= (array) $valueRow;
                        }
                    }
                }
                $value = $valueData;
            }elseif(is_object($value))
            {
                if(get_class($value)!="")
                {

                    if($this->mainClass!=get_class($value))
                    {
                        if(method_exists($value,'toJson'))
                        {
                            $value=$value->toJson(true,$this->mainClass);
                        }

                    }else{
                        $value = $value->variableToFunction('id','get',true);
                    }

                }else{
                    $value= ((array)$value);
                }
            }

            $object[$property->getName()] = $value;
        }
        return $returnArray ? (array)$object : json_encode($object);
    }
    public function fromJson($json)
    {
        $properties = json_decode($json);
        $reflector = new \ReflectionClass(get_called_class());
        $props = $reflector->getProperties(\ReflectionProperty::IS_PRIVATE);
        $privateObjects=[];
        foreach ($props as $prop)
        {
            $privateObjects[$prop->getName()]=$prop->getDocComment();
        }
        unset($props);
        foreach($properties as $property => $value)
        {

            if(is_object($value))
            {
                $annotationReader = new \BiberLtd\Bundle\BaseEntityBundle\AnnotationReader();
                $type = $annotationReader->getPropertyType($reflector->getProperty($property));

                if (class_exists($type)) {
                    $myclass = new $type();
                    $myclass->fromJson(json_encode($value));
                    $this->{$this->variableToFunction($property)}($myclass);

                }else{
                    if(is_object($value))
                    {
                        $value = json_encode($value);
                    }
                    $this->{$this->variableToFunction($property)}($value);
                }

            }else{

                $this->{$this->variableToFunction($property)}($value);
            }

        }
        return $this;
    }
    private function splitAtUpperCase($s) {
        return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
    }
    private function functionToVariable($s)
    {
        $s = $this->splitAtUpperCase($s); return strtolower(implode($s,'_'));
    }
    private function variableToFunction($variable,$determinate='set',$run=false,$defaultValue=null)
    {
        $func =  $determinate.$this->dbColumnToVariable($variable);
        return $run ? (method_exists($this, $func) ? $this->{$func}() : $defaultValue) : (method_exists($this,$func) ? $func : function () use ($defaultValue){ return $defaultValue; });
    }
    private function dbColumnToVariable($column)
    {
        return ucfirst(implode('',array_map("ucfirst", explode('_',$column))));
    }
    public function __call($method, $arguments)
    {
        if(method_exists($this, $method) && (preg_match('/set.*/', $method) ? true : false)) {
            if(!$this->setModified($this->functionToVariable($method), $arguments)->isModified()){
                return $this;
            }
            return call_user_func_array(array($this,$method),$arguments);
        }
    }
}